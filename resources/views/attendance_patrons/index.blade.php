@extends('layouts.sidebar')

@section('title', 'Attendance Patrons')

@section('header')
    <div>
        <h1 class="h4 mb-1">Attendance Patrons</h1>
        <p class="text-muted mb-0">Review pending Attendance registrations and current registered counts.</p>
    </div>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Registered Attendance Students</p>
                    <div class="h3 mb-0">{{ $studentCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Registered Attendance Employees</p>
                    <div class="h3 mb-0">{{ $employeeCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Pending Attendance Students</h2>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Student ID</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingStudents as $student)
                                    <tr>
                                        <td>{{ $student->firstname }} {{ $student->lastname }}</td>
                                        <td>{{ $student->student_id }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('attendance.students.approve', $student->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-primary" type="submit">Approve</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No pending student registrations.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pendingStudents->links() }}
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Pending Attendance Employees</h2>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Employee ID</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingEmployees as $employee)
                                    <tr>
                                        <td>{{ $employee->firstname }} {{ $employee->lastname }}</td>
                                        <td>{{ $employee->employee_id ?? $employee->employee_number }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('attendance.employees.approve', $employee->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-primary" type="submit">Approve</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No pending employee registrations.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pendingEmployees->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
