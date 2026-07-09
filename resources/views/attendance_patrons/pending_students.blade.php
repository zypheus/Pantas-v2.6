@extends('layouts.sidebar')

@section('title', 'Pending Attendance Students')

@section('header')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 mb-1">Pending Attendance Students</h1>
            <p class="text-muted mb-0">Review student registration requests before adding them to attendance patrons.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance.pending.index', ['tab' => 'students']) }}" class="btn btn-outline-secondary btn-sm">Student Data</a>
            <a href="{{ route('attendance.pending.employees') }}" class="btn btn-outline-primary btn-sm">Pending Employees</a>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Pending Student Requests</p>
                    <div class="h3 mb-0">{{ $pendingStudentCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Registered Students</p>
                    <div class="h3 mb-0">{{ $studentCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>Education Level</th>
                            <th>Course / Year</th>
                            <th>Contact</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($pendingStudents as $student)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $student->firstname }} {{ $student->middle_initial }} {{ $student->lastname }}</div>
                                <div class="small text-muted">{{ $student->birth_date }}</div>
                            </td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->educational_level ?: 'N/A' }}</td>
                            <td>{{ $student->course ?: 'N/A' }} {{ $student->year }}</td>
                            <td>
                                <div>{{ $student->mobile_number ?: 'N/A' }}</div>
                                <div class="small text-muted">{{ $student->emergency_person }} {{ $student->emergency_number }}</div>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('attendance.students.approve', $student->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('attendance.students.reject', $student->id) }}" class="d-inline" onsubmit="return confirm('Reject this student registration?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No pending attendance student registrations.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pendingStudents->links() }}
        </div>
    </div>
@endsection
