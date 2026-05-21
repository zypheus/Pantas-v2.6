@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/view_accounts/list.css') }}">
@endsection

@section('content')
    
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-primary">User Accounts</h2>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="text-center">ID</th>
                    <th scope="col">First Name</th>
                     <th scope="col">Last Name</th>
                    <th scope="col">Email</th>
                    <th scope="col" class="text-center">Role</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td>{{ $user->fname }}</td>
                        <td>{{ $user->lname }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            <span class="badge badge-role badge-{{ $user->role }}">{{ $user->role }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@endsection