@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/accounts/accounts.css') }}">
@endsection

@section('content')
<div class="accounts-page">
    <header class="accounts-page__hero">
        <div>
            <p class="accounts-page__eyebrow">User management</p>
            <h1 class="accounts-page__title">Create account</h1>
            <p class="accounts-page__subtitle">Add a staff, faculty, or admin login for the library system.</p>
        </div>
        <div class="accounts-page__hero-actions">
            <a href="{{ route('users.index') }}" class="accounts-btn accounts-btn--outline">View users</a>
            <a href="{{ route('book.index') }}" class="accounts-btn accounts-btn--outline">← Catalog</a>
        </div>
    </header>

    @include('accounts.partials.subnav')
    @include('accounts.partials.alerts')

    <div class="accounts-card accounts-card--narrow accounts-form">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="fname" class="form-label">First name</label>
                    <input type="text" id="fname" name="fname" value="{{ old('fname') }}"
                           class="form-control" required autocomplete="given-name">
                </div>
                <div class="col-md-6">
                    <label for="lname" class="form-label">Last name</label>
                    <input type="text" id="lname" name="lname" value="{{ old('lname') }}"
                           class="form-control" required autocomplete="family-name">
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-control" required autocomplete="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       required minlength="6" autocomplete="new-password">
                <p class="accounts-form-hint">Minimum 6 characters.</p>
            </div>

            <div class="mb-4">
                <label class="form-label d-block mb-2">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Select role</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="faculty" {{ old('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                </select>
                <p class="accounts-form-hint">Admin: full access · Staff: daily operations · Faculty/Student: limited.</p>
            </div>

            <button type="submit" class="accounts-btn accounts-btn--primary w-100">Create user</button>
        </form>
    </div>
</div>
@endsection
