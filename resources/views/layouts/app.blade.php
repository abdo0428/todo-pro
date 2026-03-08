<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Todo Pro' }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --bg: #f4f7f8;
      --panel: #ffffff;
      --ink: #152226;
      --muted: #5f7177;
      --brand: #0f766e;
      --brand-soft: rgba(15, 118, 110, .14);
      --accent: #ff6b2c;
    }
    body {
      background: radial-gradient(circle at 10% 0%, #e6fffa 0%, #f4f7f8 45%), var(--bg);
      color: var(--ink);
      font-family: 'Manrope', sans-serif;
      min-height: 100vh;
    }
    .app-shell { min-height: 100vh; }
    .side-wrap {
      background: linear-gradient(170deg, #052a26 0%, #0b4d46 55%, #106e62 100%);
      color: #e8fffb;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(3, 32, 30, .28);
    }
    .brand {
      font-family: 'Space Grotesk', sans-serif;
      letter-spacing: .3px;
    }
    .side-link {
      color: #d4f4ef;
      border-radius: 12px;
      padding: 10px 12px;
      text-decoration: none;
      display: block;
      font-weight: 600;
    }
    .side-link.active,
    .side-link:hover {
      background: rgba(255, 255, 255, .14);
      color: #fff;
    }
    .panel {
      background: var(--panel);
      border: 0;
      border-radius: 18px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, .06);
    }
    .topbar {
      border-bottom: 1px solid rgba(18, 36, 42, .08);
      margin-bottom: 22px;
      padding-bottom: 14px;
    }
    .badge-done { background: rgba(25, 135, 84, .12); color: #198754; }
    .badge-pending { background: rgba(255, 193, 7, .24); color: #8c6500; }
    .badge-priority-low { background: rgba(13, 202, 240, .15); color: #0b7285; }
    .badge-priority-medium { background: rgba(255, 193, 7, .24); color: #8c6500; }
    .badge-priority-high { background: rgba(220, 53, 69, .16); color: #a61e2f; }
    .badge-soft { background: var(--brand-soft); color: var(--brand); }
    .toast-container { z-index: 1095; }
    @media (max-width: 991px) {
      .side-wrap { border-radius: 16px; }
    }
  </style>

  @stack('styles')
</head>
<body>
<div class="container-fluid p-3 p-lg-4 app-shell">
  <div class="row g-3 g-lg-4 h-100">
    <aside class="col-12 col-lg-3 col-xl-2">
      <div class="side-wrap h-100 p-3 p-lg-4 d-flex flex-column gap-3">
        <div>
          <div class="brand h4 mb-1">Todo Pro</div>
          <div class="small opacity-75">Organize faster</div>
        </div>

        <nav class="d-flex flex-column gap-2">
          <a href="{{ route('tasks.index') }}" class="side-link @if(request()->routeIs('tasks.*')) active @endif">Tasks</a>
          <a href="{{ route('account.edit') }}" class="side-link @if(request()->routeIs('account.*')) active @endif">Account</a>
      
      
        </nav>
        <div class="mt-2 p-3 rounded-3" style="background: rgba(255, 255, 255, .08);">
          <div class="small mb-2 opacity-75">{{ auth()->user()->name }}</div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm w-100" type="submit">Logout</button>
          </form>
        </div>
        <div class="mt-2 p-3 rounded-3" style="background: rgba(255, 255, 255, .08);">
          <div class="fw-semibold mb-2">Quick Demo Fill</div>
          <p class="small mb-3">Create sample tasks instantly for testing filters and views.</p>
          <form method="POST" action="{{ route('tasks.fill-demo') }}" class="ajax-action-form" data-success="Demo data inserted.">
            @csrf
            <button class="btn btn-sm btn-light w-100" type="submit">Fill Tasks</button>
          </form>
        </div>


      </div>
    </aside>

    <main class="col-12 col-lg-9 col-xl-10">
      <div class="panel p-3 p-lg-4 h-100">
        <div class="topbar d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h1 class="h5 mb-0">{{ $header ?? 'Dashboard' }}</h1>
            <div class="text-muted small">{{ $subheader ?? 'Stay focused and ship faster.' }}</div>
          </div>
        </div>

        @yield('content')
      </div>
    </main>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
  @foreach (['success', 'error', 'info'] as $flashType)
    @if (session($flashType))
      <div class="toast align-items-center text-bg-{{ $flashType === 'error' ? 'danger' : $flashType }} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3200">
        <div class="d-flex">
          <div class="toast-body">{{ session($flashType) }}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    @endif
  @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  function showToast(message, type = 'success') {
    const wrapper = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    const tone = type === 'error' ? 'danger' : type;
    toast.className = `toast align-items-center text-bg-${tone} border-0 mb-2`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
    wrapper.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3200 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
  }

  document.querySelectorAll('.toast').forEach((el) => new bootstrap.Toast(el).show());

  document.addEventListener('submit', async (event) => {
    const form = event.target.closest('.ajax-action-form');
    if (!form) return;

    event.preventDefault();

    const response = await fetch(form.action, {
      method: form.method,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: new FormData(form),
    });

    const payload = await response.json().catch(() => ({}));

    if (response.ok) {
      showToast(payload.message || form.dataset.success || 'Done.');
      if (window.reloadTasksData) {
        window.reloadTasksData();
      }
      return;
    }

    showToast(payload.message || 'Request failed.', 'error');
  });
</script>

@stack('scripts')
</body>
</html>


