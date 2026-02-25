@php
  $from = $tasks->firstItem() ?? 0;
  $to = $tasks->lastItem() ?? 0;
@endphp

@if($tasks->count())
  <form id="bulkForm" class="mb-3">
    <div class="d-flex flex-wrap gap-2 mb-3">
      <select name="bulk_action" class="form-select" style="max-width: 220px;" required>
        <option value="">Bulk action...</option>
        <option value="mark_done">Mark Done</option>
        <option value="delete">Delete</option>
      </select>
      <button class="btn btn-outline-dark" type="submit">Apply to Selected</button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
            <th style="width: 60px;">#</th>
            <th>Task</th>
            <th style="width: 150px;">Priority</th>
            <th style="width: 140px;">Due Date</th>
            <th style="width: 130px;">Status</th>
            <th style="width: 290px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tasks as $task)
            <tr>
              <td><input class="task-checkbox" type="checkbox" name="task_ids[]" value="{{ $task->id }}"></td>
              <td class="text-muted">{{ $task->id }}</td>
              <td>
                <div class="fw-semibold">{{ $task->title }}</div>
                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($task->notes ?: $task->description, 90) }}</div>
              </td>
              <td>
                @if($task->priority === 'high')
                  <span class="badge badge-priority-high">High</span>
                @elseif($task->priority === 'medium')
                  <span class="badge badge-priority-medium">Medium</span>
                @else
                  <span class="badge badge-priority-low">Low</span>
                @endif
              </td>
              <td>
                @if($task->due_date)
                  <span class="badge badge-soft">{{ $task->due_date->format('Y-m-d') }}</span>
                @else
                  <span class="text-muted small">No date</span>
                @endif
              </td>
              <td>
                @if($task->status === 'done')
                  <span class="badge badge-done">Done</span>
                @else
                  <span class="badge badge-pending">Pending</span>
                @endif
              </td>
              <td>
                <div class="d-flex flex-wrap gap-2">
                  <div class="form-check form-switch m-0 pt-1">
                    <input
                      class="form-check-input quick-status-toggle"
                      type="checkbox"
                      data-url="{{ route('tasks.status', $task) }}"
                      @checked($task->status === 'done')
                    >
                  </div>

                  <button class="btn btn-sm btn-outline-success btn-ajax-status" data-url="{{ route('tasks.status', $task) }}" data-status="done">Mark Done</button>
                  <button class="btn btn-sm btn-outline-warning btn-ajax-status" data-url="{{ route('tasks.status', $task) }}" data-status="pending">Mark Pending</button>

                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('tasks.show', $task) }}">View</a>

                  <button
                    class="btn btn-sm btn-outline-primary btn-edit"
                    data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $task->id }}"
                    data-title="{{ e($task->title) }}"
                    data-description="{{ e($task->description) }}"
                    data-notes="{{ e($task->notes) }}"
                    data-status="{{ $task->status }}"
                    data-priority="{{ $task->priority }}"
                    data-due-date="{{ optional($task->due_date)->format('Y-m-d') }}"
                    data-update-url="{{ route('tasks.update', $task) }}"
                  >
                    Edit
                  </button>

                  <button class="btn btn-sm btn-outline-danger btn-ajax-delete" data-url="{{ route('tasks.destroy', $task) }}">Delete</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </form>
@else
  <div class="text-center py-5 border rounded-3 bg-light-subtle">
    <div class="h6 mb-1">No tasks found</div>
    <p class="text-muted mb-3">Start by creating your first task or clear filters.</p>
    <div class="d-flex justify-content-center gap-2">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create Task</button>
      <button id="clearFiltersBtn" class="btn btn-outline-secondary" type="button">Clear Filters</button>
    </div>
  </div>
@endif

@if($tasks->hasPages())
  <div class="d-flex justify-content-between align-items-center mt-3 small">
    <span class="text-muted">{{ $from }}-{{ $to }} of {{ $tasks->total() }}</span>
    {{ $tasks->links() }}
  </div>
@endif
