<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('clear')) {
            $request->session()->forget(['tasks.filters.q', 'tasks.filters.status', 'tasks.filters.per_page', 'tasks.filters.tab']);

            return redirect()->route('tasks.index');
        }

        return view('tasks.index', $this->buildIndexData($request));
    }

    public function show(Task $task): View
    {
        return view('tasks.show', compact('task'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->safe()->only([
            'title',
            'description',
            'notes',
            'status',
            'priority',
            'due_date',
        ]));

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function setStatus(Request $request, Task $task): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,done'],
        ]);

        $task->update(['status' => $data['status']]);

        return back()->with('success', 'Task status updated.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer', 'exists:tasks,id'],
            'bulk_action' => ['required', 'in:mark_done,delete'],
        ]);

        $query = Task::query()->whereIn('id', $data['task_ids']);

        if ($data['bulk_action'] === 'mark_done') {
            $query->update(['status' => 'done']);

            return back()->with('success', 'Selected tasks marked as done.');
        }

        $query->delete();

        return back()->with('success', 'Selected tasks deleted.');
    }

    private function buildIndexData(Request $request, bool $forceEmptyFilters = false): array
    {
        $session = $request->session();

        $q = $forceEmptyFilters ? '' : $request->query('q', $session->get('tasks.filters.q', ''));
        $status = $forceEmptyFilters ? '' : $request->query('status', $session->get('tasks.filters.status', ''));
        $tab = $forceEmptyFilters ? 'all' : $request->query('tab', $session->get('tasks.filters.tab', 'all'));

        $perPage = (int) $request->query('per_page', $session->get('tasks.filters.per_page', 10));
        $perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;

        if (! in_array($tab, ['all', 'pending', 'done'], true)) {
            $tab = 'all';
        }

        $effectiveStatus = in_array($status, ['pending', 'done'], true)
            ? $status
            : ($tab !== 'all' ? $tab : null);

        $session->put('tasks.filters.q', $q);
        $session->put('tasks.filters.status', $status);
        $session->put('tasks.filters.per_page', $perPage);
        $session->put('tasks.filters.tab', $tab);

        $tasks = Task::query()
            ->select(['id', 'title', 'description', 'notes', 'status', 'priority', 'due_date', 'created_at'])
            ->status($effectiveStatus)
            ->search($q)
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $counts = [
            'all' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        return compact('tasks', 'q', 'status', 'tab', 'perPage', 'counts');
    }




    //test to add to githup
}
