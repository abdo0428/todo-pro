@extends('layouts.guest')

@push('styles')
<style>
  .landing-shell { padding-top: 34px; padding-bottom: 34px; }
  .site-nav {
    background: rgba(255, 255, 255, .72);
    border: 1px solid rgba(18, 33, 39, .08);
    border-radius: 16px;
    padding: 10px 14px;
    backdrop-filter: blur(8px);
  }
  .hero-card {
    border-radius: 22px;
    background: linear-gradient(135deg, #091a23 0%, #123743 50%, #0f766e 100%);
    color: #f5fffd;
    padding: 34px;
    box-shadow: 0 22px 50px rgba(8, 28, 32, .24);
  }
  .hero-tag {
    display: inline-flex;
    border: 1px solid rgba(255, 255, 255, .3);
    background: rgba(255, 255, 255, .16);
    border-radius: 999px;
    padding: 4px 12px;
    font-size: 12px;
    margin-bottom: 16px;
  }
  .feature-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .feature-card {
    background: #fff;
    border: 1px solid rgba(18, 33, 39, .08);
    border-radius: 16px;
    padding: 16px;
    min-height: 118px;
  }
  .proof-card {
    background: linear-gradient(130deg, #fff8ee 0%, #ffffff 100%);
    border: 1px solid rgba(18, 33, 39, .08);
    border-radius: 16px;
    padding: 16px;
  }
  @media (max-width: 767px) {
    .hero-card { padding: 24px; }
    .feature-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
<div class="container landing-shell">
  <nav class="site-nav d-flex justify-content-between align-items-center mb-3 mb-lg-4">
    <div class="brand h4 mb-0">Todo Pro</div>
    <div class="d-flex gap-2">
      @auth
        <a href="{{ route('tasks.index') }}" class="btn btn-dark">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-outline-dark">Login</a>
        <a href="{{ route('register') }}" class="btn btn-dark">Create Account</a>
      @endauth
    </div>
  </nav>

  <div class="row g-3 g-lg-4 align-items-stretch">
    <div class="col-lg-7">
      <div class="hero-card h-100">
        <div class="hero-tag">Built for focus</div>
        <h1 class="display-5 fw-bold mb-3">Modern task management with private workspaces.</h1>
        <p class="mb-4 opacity-75">Organize personal tasks, filter instantly with AJAX, and keep each user account fully isolated and secure.</p>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ auth()->check() ? route('tasks.index') : route('register') }}" class="btn btn-light btn-lg fw-semibold">Start Free</a>
          <a href="{{ auth()->check() ? route('account.edit') : route('login') }}" class="btn btn-outline-light btn-lg">View Account</a>
        </div>
      </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-3">
      <div class="feature-grid">
        <article class="feature-card">
          <div class="fw-semibold mb-1">Live Dashboard</div>
          <div class="text-muted small">Search, tabs, pagination, and task actions without page reloads.</div>
        </article>
        <article class="feature-card">
          <div class="fw-semibold mb-1">Account Control</div>
          <div class="text-muted small">Registration, login, profile editing, and secure password updates.</div>
        </article>
        <article class="feature-card">
          <div class="fw-semibold mb-1">Private Tasks</div>
          <div class="text-muted small">Each user can access only their own tasks and records.</div>
        </article>
        <article class="feature-card">
          <div class="fw-semibold mb-1">Fast UX</div>
          <div class="text-muted small">Lightweight UI focused on speed, clarity, and workflow efficiency.</div>
        </article>
      </div>

      <div class="proof-card">
        <div class="fw-semibold mb-1">Ready for teams and demos</div>
        <div class="small text-muted">Use seeded data for instant testing, then scale with your own workflow and branding.</div>
      </div>
    </div>
  </div>
</div>
@endsection
