@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
@endsection

@section('content')
<div class="container mt-5 employees-page">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-0">Registered Faculty &amp; Staff</h4>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('employees.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, designation…" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="program" class="form-select form-select-sm">
                        <option value="">All programs</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->program_code }}" @selected(request('program') === $program->program_code)>
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="year_start_work" class="form-select form-select-sm">
                        <option value="">All start years</option>
                        @foreach ($workStartYears as $yr)
                            <option value="{{ $yr }}" @selected(request('year_start_work') == (string) $yr)>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <a href="{{ route('employees.create') }}" class="btn btn-add">+ Register Patron</a>
                <a href="{{ route('pending.employees') }}" class="btn btn-warning">View Pending Registrations</a>
                <a href="{{ route('patron.register') }}" class="btn btn-outline-secondary btn-sm" target="_blank">Public registration form</a>
            </div>

            <div class="mb-3 text-center">
                <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm">Students</a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm active">Faculty &amp; Staff</a>
            </div>

            <div class="table-responsive employees-table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>ID Number</th>
                            <th>Designation</th>
                            <th>Program</th>
                            <th>Start year</th>
                            <th>QR</th>
                            <th>Actions</th>
                            <th>ID card</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faculty as $employee)
                            @php
                                $programLabel = $programs->firstWhere('program_code', $employee->program)?->program_name
                                    ?? $employee->program
                                    ?? $employee->department;
                            @endphp
                            <tr>
                                <td>
                                    @if ($employee->formal_picture)
                                        <img src="{{ asset($employee->formal_picture) }}" width="64" height="64" class="rounded object-fit-cover" alt="">
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $employee->firstname }}
                                    {{ $employee->middle_initial ? $employee->middle_initial.'. ' : '' }}
                                    {{ $employee->lastname }}
                                </td>
                                <td>{{ $employee->employee_id }}</td>
                                <td>{{ $employee->designation ?? $employee->position }}</td>
                                <td>{{ $programLabel }}</td>
                                <td>{{ $employee->year_start_work ?? '—' }}</td>
                                <td><code class="small">{{ $employee->qrcode }}</code></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Options</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">Edit</a></li>
                                            <li>
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Generate</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.id.front', $employee->id) }}" target="_blank">Front</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.id.back', $employee->id) }}" target="_blank">Back</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.id.download', $employee->id) }}">Download ZIP</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-muted">No faculty or staff registered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $faculty->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
