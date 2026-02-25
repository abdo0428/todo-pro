@extends('layouts.guest')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
      <div class="glass p-4 p-lg-5">
        <h1 class="h3 mb-2">Welcome Back</h1>
        <p class="text-muted mb-4">Sign in to continue to your private tasks board.</p>

        <form method="POST" action="{{ route('login.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4 form-check">
            <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>

          <button class="btn btn-dark w-100" type="submit">Login</button>
        </form>

        <div class="small mt-3 text-center">
          No account? <a href="{{ route('register') }}">Create one</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
