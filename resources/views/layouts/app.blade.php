<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Todo Pro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { background: #f6f7fb; }
    .card { border: 0; border-radius: 14px; }
    .badge-soft { background: rgba(13,110,253,.1); color: #0d6efd; }
    .badge-done { background: rgba(25,135,84,.12); color: #198754; }
    .badge-pending { background: rgba(255,193,7,.2); color: #8a6a00; }
    .badge-priority-low { background: rgba(13,202,240,.15); color: #087990; }
    .badge-priority-medium { background: rgba(255,193,7,.2); color: #8a6a00; }
    .badge-priority-high { background: rgba(220,53,69,.14); color: #b02a37; }
    .toast-container { z-index: 1095; }
  </style>

  @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container py-2">
    <a class="navbar-brand fw-bold" href="{{ route('tasks.index') }}">Todo Pro</a>
    <div class="ms-auto small text-muted">
      Tasks Dashboard
    </div>
  </div>
</nav>

<main class="container my-4">
  @yield('content')
</main>

<div class="toast-container position-fixed top-0 end-0 p-3">
  @foreach (['success', 'error', 'info'] as $flashType)
    @if (session($flashType))
      <div class="toast align-items-center text-bg-{{ $flashType === 'error' ? 'danger' : $flashType }} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3500">
        <div class="d-flex">
          <div class="toast-body">{{ session($flashType) }}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    @endif
  @endforeach

  @if ($errors->any())
    <div class="toast align-items-center text-bg-danger border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4500">
      <div class="d-flex">
        <div class="toast-body">{{ $errors->first() }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.toast').forEach((el) => {
    const toast = new bootstrap.Toast(el);
    toast.show();
  });
</script>

@stack('scripts')
</body>
</html>
