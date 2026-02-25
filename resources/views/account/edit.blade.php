@extends('layouts.app', ['header' => 'Account Settings', 'subheader' => 'Manage profile details and security'])

@section('content')
<div class="row g-4">
  <div class="col-12 col-xl-7">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h2 class="h5 mb-3">Profile</h2>
        <form method="POST" action="{{ route('account.profile') }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <button class="btn btn-dark" type="submit">Save Profile</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4">
        <h2 class="h5 mb-3">Change Password</h2>
        <form method="POST" action="{{ route('account.password') }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>

          <button class="btn btn-outline-dark" type="submit">Update Password</button>
        </form>
      </div>
    </div>

    <div class="card border-danger border-1 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h2 class="h5 text-danger mb-2">Danger Zone</h2>
        <p class="text-muted small">Deleting your account removes all your tasks permanently.</p>
        <form method="POST" action="{{ route('account.destroy') }}">
          @csrf
          @method('DELETE')
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button class="btn btn-danger" type="submit">Delete Account</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
