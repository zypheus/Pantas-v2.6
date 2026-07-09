@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/accounts/accounts.css') }}">
@endsection

@section('content')
@php
    $roleCounts = $users->groupBy('role')->map->count();
@endphp
<div class="accounts-page">
    <header class="accounts-page__hero">
        <div>
            <p class="accounts-page__eyebrow">User management</p>
            <h1 class="accounts-page__title">User accounts</h1>
            <p class="accounts-page__subtitle">Manage module-aware staff logins.</p>
        </div>
        <div class="accounts-page__hero-actions">
            <a href="{{ route('users.create') }}" class="accounts-btn accounts-btn--primary">+ Create account</a>
            <a href="{{ route('book.index') }}" class="accounts-btn accounts-btn--outline">← Catalog</a>
        </div>
    </header>

    @include('accounts.partials.subnav')
    @include('accounts.partials.alerts')

    <div class="accounts-stats">
        <div class="accounts-stat">
            <div class="accounts-stat__value">{{ $users->count() }}</div>
            <div class="accounts-stat__label">Total users</div>
        </div>
        <div class="accounts-stat">
            <div class="accounts-stat__value">{{ $roleCounts->get('super_admin', 0) }}</div>
            <div class="accounts-stat__label">Super Admins</div>
        </div>
        <div class="accounts-stat">
            <div class="accounts-stat__value">{{ $roleCounts->get('library_admin', 0) + $roleCounts->get('library_staff', 0) }}</div>
            <div class="accounts-stat__label">Library Staff</div>
        </div>
        <div class="accounts-stat">
            <div class="accounts-stat__value">{{ $roleCounts->get('attendance_admin', 0) + $roleCounts->get('attendance_staff', 0) }}</div>
            <div class="accounts-stat__label">Attendance Staff</div>
        </div>
    </div>

    <div class="accounts-card accounts-card--flush-table">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $roleClass = in_array($user->role, ['super_admin', 'library_admin', 'library_staff', 'attendance_admin', 'attendance_staff'], true)
                                    ? $user->role
                                    : 'default';
                            @endphp
                            <tr>
                                <td>
                                    <div class="accounts-user-cell">
                                        <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                                        <small>ID #{{ $user->id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </td>
                                <td>
                                    <span class="accounts-badge accounts-badge--{{ $roleClass }}">{{ $user->role }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="accounts-btn accounts-btn--warning accounts-btn--sm">Edit</a>
                                    @if((int) $user->id !== (int) auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this user account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="accounts-btn accounts-btn--danger accounts-btn--sm">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="accounts-empty">
                <div class="accounts-empty__icon">👤</div>
                <p class="mb-2">No user accounts yet.</p>
                <a href="{{ route('users.create') }}" class="accounts-btn accounts-btn--primary">Create first account</a>
            </div>
        @endif
    </div>
</div>
@endsection
