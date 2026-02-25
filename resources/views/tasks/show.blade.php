@extends('layouts.app', ['header' => 'Task Details', 'subheader' => 'Full information for this task'])

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-xl-8">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
          <div>
            <h2 class="h4 mb-1">{{ $task->title }}</h2>
            <div class="small text-muted">Created {{ $task->created_at->format('Y-m-d H:i') }}</div>
          </div>
          <a class="btn btn-outline-secondary" href="{{ route('tasks.index') }}">Back</a>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="badge {{ $task->status === 'done' ? 'badge-done' : 'badge-pending' }}">{{ ucfirst($task->status) }}</span>
          <span class="badge {{ $task->priority === 'high' ? 'badge-priority-high' : ($task->priority === 'medium' ? 'badge-priority-medium' : 'badge-priority-low') }}">{{ ucfirst($task->priority) }} Priority</span>
          @if($task->due_date)
            <span class="badge badge-soft">Due {{ $task->due_date->format('Y-m-d') }}</span>
          @endif
        </div>

        @if($task->notes)
          <div class="mb-4">
            <div class="fw-semibold mb-2">Notes</div>
            <p class="mb-0 text-muted">{{ $task->notes }}</p>
          </div>
        @endif

        @if($task->description)
          <div class="mb-4">
            <div class="fw-semibold mb-2">Description</div>
            <p class="mb-0 text-muted">{{ $task->description }}</p>
          </div>
        @endif

        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('tasks.index') }}" class="btn btn-dark">Open Board</a>
          <button class="btn btn-outline-danger" id="deleteTaskBtn" type="button">Delete</button>
        </div>

        <form id="deleteTaskForm" method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-none">
          @csrf
          @method('DELETE')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('deleteTaskBtn').addEventListener('click', async () => {
    const result = await Swal.fire({
      title: 'Delete this task?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
    });

    if (result.isConfirmed) {
      document.getElementById('deleteTaskForm').submit();
    }
  });
</script>
@endpush
