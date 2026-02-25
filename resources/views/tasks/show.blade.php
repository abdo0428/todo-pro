@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="mb-1">{{ $task->title }}</h4>
            <div class="text-muted small">Created {{ $task->created_at->format('Y-m-d H:i') }}</div>
          </div>
          <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="badge {{ $task->status === 'done' ? 'badge-done' : 'badge-pending' }}">{{ ucfirst($task->status) }}</span>
          <span class="badge {{ $task->priority === 'high' ? 'badge-priority-high' : ($task->priority === 'medium' ? 'badge-priority-medium' : 'badge-priority-low') }}">{{ ucfirst($task->priority) }} Priority</span>
          @if($task->due_date)
            <span class="badge badge-soft">Due {{ $task->due_date->format('Y-m-d') }}</span>
          @endif
        </div>

        @if($task->notes)
          <div class="mb-3">
            <div class="fw-semibold mb-1">Notes</div>
            <p class="mb-0 text-muted">{{ $task->notes }}</p>
          </div>
        @endif

        @if($task->description)
          <div class="mb-4">
            <div class="fw-semibold mb-1">Description</div>
            <p class="mb-0 text-muted">{{ $task->description }}</p>
          </div>
        @endif

        <div class="d-flex gap-2">
          <button
            class="btn btn-primary btn-edit"
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
            <button class="btn btn-outline-danger" type="submit">Delete</button>
          </form>
        </div>
      </div>
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

      <form id="editForm" method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="edit_task_id" value="{{ $task->id }}">
        <input type="hidden" name="edit_update_url" value="{{ route('tasks.update', $task) }}">

        <div class="modal-body">
          @if($errors->any() && old('edit_task_id') == $task->id)
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
            <input id="editTitle" name="title" class="form-control" value="{{ old('title', $task->title) }}" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select id="editPriority" name="priority" class="form-select" required>
                <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input id="editDueDate" type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select id="editStatus" name="status" class="form-select" required>
                <option value="pending" @selected(old('status', $task->status) === 'pending')>Pending</option>
                <option value="done" @selected(old('status', $task->status) === 'done')>Done</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea id="editNotes" name="notes" class="form-control" rows="3">{{ old('notes', $task->notes) }}</textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Description</label>
            <textarea id="editDescription" name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
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

  @if($errors->any() && old('edit_task_id') == $task->id)
    new bootstrap.Modal(document.getElementById('editModal')).show();
  @endif
</script>
@endpush
