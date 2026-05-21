@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/books/createuser.css') }}">
@endsection

@section('content')

<!-- ✅ JavaScript Toggle Functions -->
<script>
    const toggleBtn = document.getElementById('customMenuToggle');
    const closeBtn = document.getElementById('customMenuClose');
    const routeWrapper = document.getElementById('routeWrapper');

    toggleBtn.addEventListener('click', () => {
        routeWrapper.classList.add('open');
    });

    closeBtn.addEventListener('click', () => {
        routeWrapper.classList.remove('open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            routeWrapper.classList.remove('open');
        }
    });
</script>
    
<div class="container">
    <div class="card p-4">
        <h3 id="cnu" class="text-center mb-4">Create New User</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" id="lname" name="lname" value="{{ old('lname') }}" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" id="fname" name="fname" value="{{ old('fname') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create User</button>
        </form>
    </div>
</div>


@endsection
