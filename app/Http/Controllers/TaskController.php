<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('clear')) {
            $this->clearFilters($request);

            return redirect()->route('tasks.index');
        }

        $data = $this->buildIndexData($request);

        return view('tasks.index', $data);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->buildIndexData($request);

        return response()->json([
            'html' => view('tasks.partials.table', $data)->render(),
            'counts' => $data['counts'],
            'tab' => $data['tab'],
            'status' => $data['status'],
            'q' => $data['q'],
            'perPage' => $data['perPage'],
        ]);
    }

    public function show(Request $request, Task $task): View
    {
        $task = $this->ownedTask($request, $task);

        return view('tasks.show', compact('task'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse|JsonResponse
    {
        $task = $request->user()->tasks()->create($request->validated());

        return $this->respond($request, 'Task created successfully.', ['task' => $task], route('tasks.index'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $task = $this->ownedTask($request, $task);

        $task->update($request->safe()->only([
            'title',
            'description',
            'notes',
            'status',
            'priority',
            'due_date',
        ]));

        return $this->respond($request, 'Task updated successfully.', ['task' => $task->fresh()], route('tasks.index'));
    }

    public function destroy(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $task = $this->ownedTask($request, $task);
        $task->delete();

        return $this->respond($request, 'Task deleted successfully.', [], route('tasks.index'));
    }

    public function setStatus(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $task = $this->ownedTask($request, $task);

        $data = $request->validate([
            'status' => ['required', 'in:pending,done'],
        ]);

        $task->update(['status' => $data['status']]);

        return $this->respond($request, 'Task status updated.', ['task' => $task->fresh()], route('tasks.index'));
    }

    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer'],
            'bulk_action' => ['required', 'in:mark_done,delete'],
        ]);

        $query = $request->user()->tasks()->whereIn('id', $data['task_ids']);

        if ($data['bulk_action'] === 'mark_done') {
            $query->update(['status' => 'done']);

            return $this->respond($request, 'Selected tasks marked as done.', [], route('tasks.index'));
        }

        $query->delete();

        return $this->respond($request, 'Selected tasks deleted.', [], route('tasks.index'));
    }

    public function fillDemo(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $existingCount = $user->tasks()->count();

        DB::transaction(function () use ($user, $existingCount) {
            for ($i = 1; $i <= 12; $i++) {
                $user->tasks()->create([
                    'title' => 'Demo Task '.($existingCount + $i),
                    'description' => 'Sample description generated for testing UI and filters.',
                    'notes' => Str::random(35),
                    'status' => fake()->randomElement(['pending', 'done']),
                    'priority' => fake()->randomElement(['low', 'medium', 'high']),
                    'due_date' => fake()->optional(0.7)->dateTimeBetween('-5 days', '+20 days')?->format('Y-m-d'),
                ]);
            }
        });

        return $this->respond($request, 'Demo tasks created.', [], route('tasks.index'));
    }

    private function buildIndexData(Request $request): array
    {
        $session = $request->session();

        $q = $request->query('q', $session->get('tasks.filters.q', ''));
        $status = $request->query('status', $session->get('tasks.filters.status', ''));
        $tab = $request->query('tab', $session->get('tasks.filters.tab', 'all'));

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

        $base = $request->user()->tasks();

        $tasks = $base
            ->select(['id', 'user_id', 'title', 'description', 'notes', 'status', 'priority', 'due_date', 'created_at'])
            ->status($effectiveStatus)
            ->search($q)
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $counts = [
            'all' => $request->user()->tasks()->count(),
            'pending' => $request->user()->tasks()->where('status', 'pending')->count(),
            'done' => $request->user()->tasks()->where('status', 'done')->count(),
        ];

        return compact('tasks', 'q', 'status', 'tab', 'perPage', 'counts');
    }

    private function clearFilters(Request $request): void
    {
        $request->session()->forget(['tasks.filters.q', 'tasks.filters.status', 'tasks.filters.per_page', 'tasks.filters.tab']);
    }

    private function ownedTask(Request $request, Task $task): Task
    {
        abort_unless((int) $task->user_id === (int) $request->user()->id, 404);

        return $task;
    }

    private function respond(Request $request, string $message, array $extra, string $redirectTo): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge(['message' => $message], $extra));
        }

        return redirect($redirectTo)->with('success', $message);
    }
}
