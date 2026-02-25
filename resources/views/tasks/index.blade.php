@extends('layouts.app')

@section('content')
@php
  $from = $tasks->firstItem() ?? 0;
  $to = $tasks->lastItem() ?? 0;
@endphp

<div class="row g-4">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
          <div>
            <div class="h5 mb-0 fw-bold">Tasks</div>
            <div class="text-muted small">Simple, fast workflow for managing your todos.</div>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">+ New Task</button>
        </div>

        <ul class="nav nav-pills mb-3 gap-2" role="tablist">
          <li class="nav-item">
            <a class="nav-link @if($tab === 'all') active @endif" href="{{ route('tasks.index', array_merge(request()->query(), ['tab' => 'all', 'status' => ''])) }}">
              All <span class="badge text-bg-light ms-1">{{ $counts['all'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link @if($tab === 'pending') active @endif" href="{{ route('tasks.index', array_merge(request()->query(), ['tab' => 'pending', 'status' => ''])) }}">
              Pending <span class="badge text-bg-light ms-1">{{ $counts['pending'] }}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link @if($tab === 'done') active @endif" href="{{ route('tasks.index', array_merge(request()->query(), ['tab' => 'done', 'status' => ''])) }}">
              Done <span class="badge text-bg-light ms-1">{{ $counts['done'] }}</span>
            </a>
          </li>
        </ul>

        <form id="filtersForm" class="row g-2 mb-3" method="GET" action="{{ route('tasks.index') }}">
          <input type="hidden" name="tab" value="{{ $tab }}">

          <div class="col-12 col-md-4">
            <input id="searchInput" type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search title, notes, description...">
          </div>

          <div class="col-6 col-md-3">
            <select id="statusFilter" name="status" class="form-select">
              <option value="" @selected($status === '')>All Status</option>
              <option value="pending" @selected($status==='pending')>Pending</option>
              <option value="done" @selected($status==='done')>Done</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <select id="perPageFilter" name="per_page" class="form-select">
              @foreach([10,25,50] as $n)
                <option value="{{ $n }}" @selected($perPage===$n)>{{ $n }} / page</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-outline-primary w-100" type="submit">Apply</button>
            <a class="btn btn-outline-secondary w-100" href="{{ route('tasks.index', ['clear' => 1]) }}">Clear</a>
          </div>
        </form>

        @if($tasks->count())
          <form id="bulkForm" method="POST" action="{{ route('tasks.bulk-action') }}">
            @csrf

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
                    <th style="width: 280px;">Actions</th>
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
                          <form method="POST" action="{{ route('tasks.status', $task) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'pending' : 'done' }}" class="status-value">
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input quick-status-toggle" type="checkbox" @checked($task->status === 'done')>
                            </div>
                          </form>

                          <form method="POST" action="{{ route('tasks.status', $task) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="done">
                            <button class="btn btn-sm btn-outline-success" type="submit">Mark Done</button>
                          </form>

                          <form method="POST" action="{{ route('tasks.status', $task) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button class="btn btn-sm btn-outline-warning" type="submit">Mark Pending</button>
                          </form>

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

                          <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="form-delete">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                          </form>
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
              <a class="btn btn-outline-secondary" href="{{ route('tasks.index', ['clear' => 1]) }}">Clear Filters</a>
            </div>
          </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
          <div class="text-muted small">Showing {{ $from }}-{{ $to }} of {{ $tasks->total() }}</div>
          <div>{{ $tasks->links() }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="modal-body">
          @if($errors->any() && !old('edit_task_id'))
            <div class="alert alert-danger py-2">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select" required>
                <option value="low" @selected(old('priority') === 'low')>Low</option>
                <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                <option value="high" @selected(old('priority') === 'high')>High</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="pending" @selected(old('status', 'pending') === 'pending')>Pending</option>
                <option value="done" @selected(old('status') === 'done')>Done</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editForm" method="POST" action="{{ old('edit_update_url', '#') }}">
        @csrf
        @method('PUT')

        <input type="hidden" id="editTaskId" name="edit_task_id" value="{{ old('edit_task_id') }}">
        <input type="hidden" id="editUpdateUrl" name="edit_update_url" value="{{ old('edit_update_url') }}">

        <div class="modal-body">
          @if($errors->any() && old('edit_task_id'))
            <div class="alert alert-danger py-2">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input id="editTitle" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select id="editPriority" name="priority" class="form-select" required>
                <option value="low" @selected(old('priority') === 'low')>Low</option>
                <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                <option value="high" @selected(old('priority') === 'high')>High</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input id="editDueDate" type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select id="editStatus" name="status" class="form-select" required>
                <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                <option value="done" @selected(old('status') === 'done')>Done</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea id="editNotes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Description</label>
            <textarea id="editDescription" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.form-delete').forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      Swal.fire({
        title: 'Delete this task?',
        text: "You won't be able to undo this.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });

  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      document.querySelectorAll('.task-checkbox').forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
      });
    });
  }

  document.querySelectorAll('.quick-status-toggle').forEach((checkbox) => {
    checkbox.addEventListener('change', (event) => {
      const form = event.target.closest('form');
      const hidden = form.querySelector('.status-value');
      hidden.value = event.target.checked ? 'done' : 'pending';
      form.submit();
    });
  });

  document.querySelectorAll('.btn-edit').forEach((button) => {
    button.addEventListener('click', () => {
      const editForm = document.getElementById('editForm');
      const updateUrl = button.dataset.updateUrl;

      editForm.action = updateUrl;
      document.getElementById('editUpdateUrl').value = updateUrl;
      document.getElementById('editTaskId').value = button.dataset.id;
      document.getElementById('editTitle').value = button.dataset.title;
      document.getElementById('editDescription').value = button.dataset.description;
      document.getElementById('editNotes').value = button.dataset.notes;
      document.getElementById('editStatus').value = button.dataset.status;
      document.getElementById('editPriority').value = button.dataset.priority;
      document.getElementById('editDueDate').value = button.dataset.dueDate;
    });
  });

  const filtersForm = document.getElementById('filtersForm');
  const statusFilter = document.getElementById('statusFilter');
  const perPageFilter = document.getElementById('perPageFilter');
  const searchInput = document.getElementById('searchInput');

  if (statusFilter) {
    statusFilter.addEventListener('change', () => filtersForm.submit());
  }

  if (perPageFilter) {
    perPageFilter.addEventListener('change', () => filtersForm.submit());
  }

  let searchDebounce;
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(() => filtersForm.submit(), 450);
    });
  }

  @if($errors->any() && !old('edit_task_id'))
    new bootstrap.Modal(document.getElementById('createModal')).show();
  @endif

  @if($errors->any() && old('edit_task_id'))
    new bootstrap.Modal(document.getElementById('editModal')).show();
  @endif
</script>
@endpush
