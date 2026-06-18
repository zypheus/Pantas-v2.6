@extends('layouts.sidebar')

@section('title', 'Pending Registrations')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
    <style>
        .hidden { display: none; }
    </style>
@endsection

@section('content')
@php
    $activeTab = $activeTab ?? (request('tab') === 'employees' ? 'employees' : 'students');
    $showEmployees = $activeTab === 'employees';
@endphp

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-0">Pending Registrations</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <button id="showStudents" class="btn {{ $showEmployees ? 'btn-outline-primary' : 'btn-primary' }} me-2">View Students</button>
                    <button id="showEmployees" class="btn {{ $showEmployees ? 'btn-primary' : 'btn-outline-primary' }}">View Employees</button>
                </div>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">
                    &larr; Back to Registered
                </a>
            </div>

            <div id="studentTable" class="{{ $showEmployees ? 'hidden' : '' }}">
                <h4>Pending Student Registrations</h4>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3 align-middle">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingStudents as $p)
                                <tr>
                                    <td>
                                        @if($p->profile_picture)
                                            <img src="{{ asset($p->profile_picture) }}" width="80" alt="">
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td>{{ $p->firstname }} {{ $p->lastname }}</td>
                                    <td>{{ $p->course }}</td>
                                    <td>{{ $p->year }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('students.approve', $p->id) }}" method="POST">
                                                        @csrf
                                                        <button class="dropdown-item">Approve</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('students.reject', $p->id) }}" method="POST">
                                                        @csrf
                                                        <button class="dropdown-item">Reject</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No pending student registrations</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="employeeTable" class="{{ $showEmployees ? '' : 'hidden' }}">
                <h4>Pending Faculty &amp; Staff Registrations</h4>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3 align-middle">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Designation</th>
                                <th>Program</th>
                                <th>Start year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingEmployees as $e)
                                <tr>
                                    <td>
                                        @if($e->formal_picture)
                                            <img src="{{ asset($e->formal_picture) }}" width="80" alt="">
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td>{{ $e->firstname }} {{ $e->middle_initial ? $e->middle_initial.'. ' : '' }}{{ $e->lastname }}</td>
                                    <td>{{ $e->employee_id }}</td>
                                    <td>{{ $e->designation ?? $e->position }}</td>
                                    <td>{{ $e->program ?? $e->department }}</td>
                                    <td>{{ $e->year_start_work ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('employees.approve', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                        <form action="{{ route('employees.reject', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7">No pending faculty &amp; staff registrations</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const studentTable = document.getElementById('studentTable');
    const employeeTable = document.getElementById('employeeTable');
    const btnStudents = document.getElementById('showStudents');
    const btnEmployees = document.getElementById('showEmployees');

    btnStudents.addEventListener('click', () => {
        studentTable.classList.remove('hidden');
        employeeTable.classList.add('hidden');
        btnStudents.classList.replace('btn-outline-primary', 'btn-primary');
        btnEmployees.classList.replace('btn-primary', 'btn-outline-primary');
    });

    btnEmployees.addEventListener('click', () => {
        employeeTable.classList.remove('hidden');
        studentTable.classList.add('hidden');
        btnEmployees.classList.replace('btn-outline-primary', 'btn-primary');
        btnStudents.classList.replace('btn-primary', 'btn-outline-primary');
    });
</script>
@endsection
