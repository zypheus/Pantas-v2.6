@extends('layouts.sidebar')

@section('title', 'Attendance Patrons')

@section('header')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 mb-1">Attendance Patrons</h1>
            <p class="text-muted mb-0">Manage attendance students, employees, pending registrations, imports, exports, and ID cards.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance.patrons.students.create') }}" class="btn btn-primary btn-sm">Add Student</a>
            <a href="{{ route('attendance.patrons.employees.create') }}" class="btn btn-outline-primary btn-sm">Add Employee</a>
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
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-3">
        @foreach ([
            'Students' => $studentCount,
            'Employees' => $employeeCount,
            'Pending Students' => $pendingStudentCount,
            'Pending Employees' => $pendingEmployeeCount,
        ] as $label => $count)
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small text-uppercase fw-semibold mb-1">{{ $label }}</p>
                        <div class="h3 mb-0">{{ $count }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'students' ? 'active' : '' }}" href="{{ route('attendance.pending.index', ['tab' => 'students']) }}">Student Data</a></li>
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'employees' ? 'active' : '' }}" href="{{ route('attendance.pending.index', ['tab' => 'employees']) }}">Employee Data</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('attendance.pending.students') }}">Pending Students</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('attendance.pending.employees') }}">Pending Employees</a></li>
    </ul>

    @if ($activeTab === 'employees')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('attendance.pending.index') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="employees">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Search</label>
                        <input class="form-control" name="employee_search" value="{{ request('employee_search') }}" placeholder="Name, department, position, employee ID, QR">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="employee_department">
                            <option value="">All departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}" @selected(request('employee_department') === $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="employee_position">
                            <option value="">All positions</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position }}" @selected(request('employee_position') === $position)>{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('attendance.patrons.employees.export') }}" class="btn btn-success btn-sm">Export Employees</a>
            <a href="{{ route('attendance.patrons.employees.template') }}" class="btn btn-outline-secondary btn-sm">Download Template</a>
            <a href="{{ route('attendance.patrons.employees.ids.bulk') }}" class="btn btn-outline-primary btn-sm">Bulk Download ID Cards</a>
            <form method="POST" action="{{ route('attendance.patrons.employees.import') }}" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input class="form-control form-control-sm" type="file" name="file" accept=".csv,.xlsx" required>
                <button class="btn btn-outline-success btn-sm">Import</button>
            </form>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Name</th><th>Employee ID</th><th>Department</th><th>Position</th><th>QR/RFID</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middle_initial }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->position }}</td>
                            <td><code>{{ $employee->qrcode }}</code></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="{{ route('attendance.patrons.employees.id', $employee) }}">ID</a>
                                    <a class="btn btn-outline-secondary" href="{{ route('attendance.patrons.employees.id.download', $employee) }}">Download</a>
                                    <a class="btn btn-outline-primary" href="{{ route('attendance.patrons.employees.edit', $employee) }}">Edit</a>
                                </div>
                                <form method="POST" action="{{ route('attendance.patrons.employees.destroy', $employee) }}" class="d-inline" onsubmit="return confirm('Delete this employee record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No attendance employees found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $employees->links() }}</div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('attendance.pending.index') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="students">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Search</label>
                        <input class="form-control" name="student_search" value="{{ request('student_search') }}" placeholder="Name, course, student ID, QR">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Course / Program</label>
                        <select class="form-select" name="student_course">
                            <option value="">All courses</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->program_name }}" @selected(request('student_course') === $program->program_name)>{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">Year</label>
                        <select class="form-select" name="student_year">
                            <option value="">All</option>
                            @foreach ($studentYears as $year)
                                <option value="{{ $year }}" @selected(request('student_year') === $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">Education Level</label>
                        <select class="form-select" name="student_educational_level">
                            <option value="">All</option>
                            @foreach ($educationalLevels as $level)
                                <option value="{{ $level }}" @selected(request('student_educational_level') === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-1 d-grid"><button class="btn btn-primary">Apply</button></div>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('attendance.patrons.students.export') }}" class="btn btn-success btn-sm">Export Students</a>
            <a href="{{ route('attendance.patrons.students.template') }}" class="btn btn-outline-secondary btn-sm">Download Template</a>
            <a href="{{ route('attendance.patrons.students.ids.bulk') }}" class="btn btn-outline-primary btn-sm">Bulk Download ID Cards</a>
            <form method="POST" action="{{ route('attendance.patrons.students.import') }}" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input class="form-control form-control-sm" type="file" name="file" accept=".csv,.xlsx" required>
                <button class="btn btn-outline-success btn-sm">Import</button>
            </form>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Name</th><th>Student ID</th><th>Course</th><th>Year</th><th>QR/RFID</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->lastname }}, {{ $student->firstname }} {{ $student->middle_initial }}</td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->course }}</td>
                            <td>{{ $student->year }}</td>
                            <td><code>{{ $student->qrcode }}</code></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="{{ route('attendance.patrons.students.id', $student) }}">ID</a>
                                    <a class="btn btn-outline-secondary" href="{{ route('attendance.patrons.students.id.download', $student) }}">Download</a>
                                    <a class="btn btn-outline-primary" href="{{ route('attendance.patrons.students.edit', $student) }}">Edit</a>
                                </div>
                                <form method="POST" action="{{ route('attendance.patrons.students.destroy', $student) }}" class="d-inline" onsubmit="return confirm('Delete this student record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No attendance students found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $students->links() }}</div>
        </div>
    @endif
@endsection
