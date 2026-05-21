@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/accounts/accounts.css') }}">
@endsection

@section('content')
<div class="accounts-page">
    <header class="accounts-page__hero">
        <div>
            <p class="accounts-page__eyebrow">User management</p>
            <h1 class="accounts-page__title">Edit account</h1>
            <p class="accounts-page__subtitle">{{ $user->fname }} {{ $user->lname }} · {{ $user->email }}</p>
        </div>
        <div class="accounts-page__hero-actions">
            <a href="{{ route('users.index') }}" class="accounts-btn accounts-btn--outline">← All users</a>
        </div>
    </header>

    @include('accounts.partials.subnav')
    @include('accounts.partials.alerts')

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="accounts-card accounts-form">
                <h2 class="accounts-card__title">Account details</h2>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fname" class="form-label">First name</label>
                            <input type="text" name="fname" id="fname" class="form-control"
                                   value="{{ old('fname', $user->fname) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lname" class="form-label">Last name</label>
                            <input type="text" name="lname" id="lname" class="form-control"
                                   value="{{ old('lname', $user->lname) }}" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="faculty" {{ old('role', $user->role) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="accounts-btn accounts-btn--success">Save changes</button>
                        <a href="{{ route('users.index') }}" class="accounts-btn accounts-btn--outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="accounts-card">
                <h2 class="accounts-card__title">Summary</h2>
                <dl class="mb-0">
                    <dt class="text-muted small">User ID</dt>
                    <dd class="mb-2">#{{ $user->id }}</dd>
                    <dt class="text-muted small">Current role</dt>
                    <dd class="mb-2">
                        @php
                            $roleClass = in_array($user->role, ['admin', 'staff', 'faculty', 'student'], true)
                                ? $user->role
                                : 'default';
                        @endphp
                        <span class="accounts-badge accounts-badge--{{ $roleClass }}">{{ $user->role }}</span>
                    </dd>
                    <dt class="text-muted small">Created</dt>
                    <dd class="mb-0">{{ $user->created_at?->format('M j, Y g:i A') ?? '—' }}</dd>
                </dl>
                <p class="accounts-form-hint mt-3 mb-0">Password cannot be changed here. Create a new account or use a separate reset flow if needed.</p>
            </div>
        </div>
    </div>
</div>
@endsection
