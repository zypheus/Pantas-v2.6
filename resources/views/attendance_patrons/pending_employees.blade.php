@extends('layouts.sidebar')

@section('title', 'Pending Attendance Employees')

@section('header')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 mb-1">Pending Attendance Employees</h1>
            <p class="text-muted mb-0">Review employee and faculty/staff registration requests before approval.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance.pending.index', ['tab' => 'employees']) }}" class="btn btn-outline-secondary btn-sm">Employee Data</a>
            <a href="{{ route('attendance.pending.students') }}" class="btn btn-outline-primary btn-sm">Pending Students</a>
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
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Pending Employee Requests</p>
                    <div class="h3 mb-0">{{ $pendingEmployeeCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small text-uppercase fw-semibold mb-1">Registered Employees</p>
                    <div class="h3 mb-0">{{ $employeeCount }}</div>
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
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>QR Code</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($pendingEmployees as $employee)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $employee->firstname }} {{ $employee->middle_initial }} {{ $employee->lastname }}</div>
                                <div class="small text-muted">{{ $employee->birth_date }}</div>
                            </td>
                            <td>{{ $employee->employee_id ?? $employee->employee_number }}</td>
                            <td>{{ $employee->department ?: 'N/A' }}</td>
                            <td>{{ $employee->position ?: 'N/A' }}</td>
                            <td>
                                <div>{{ $employee->mobile_number ?: 'N/A' }}</div>
                                <div class="small text-muted">{{ $employee->emergency_contact_name }} {{ $employee->emergency_contact_number }}</div>
                            </td>
                            <td><code>{{ $employee->qrcode }}</code></td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('attendance.employees.approve', $employee->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('attendance.employees.reject', $employee->id) }}" class="d-inline" onsubmit="return confirm('Reject this employee registration?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No pending attendance employee registrations.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pendingEmployees->links() }}
        </div>
    </div>
@endsection
