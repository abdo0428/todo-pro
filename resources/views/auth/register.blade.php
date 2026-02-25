@extends('layouts.guest')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="glass p-4 p-lg-5">
        <h1 class="h3 mb-2">Create Account</h1>
        <p class="text-muted mb-4">Get your own secure workspace in under a minute.</p>

        <form method="POST" action="{{ route('register.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            @error('password')<div class="col-12 text-danger small">{{ $message }}</div>@enderror
          </div>

          <button class="btn btn-dark w-100" type="submit">Register</button>
        </form>

        <div class="small mt-3 text-center">
          Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
