<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Registration</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="mx-auto" style="max-width: 960px;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h3 mb-1">Attendance Registration</h1>
                    <p class="text-muted mb-0">Submit a record for attendance module approval.</p>
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('patron.register') }}">Change module</a>
            </div>

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

            <div class="row g-4">
                <section class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Student</h2>
                            <form method="POST" action="{{ route('attendance.pending.store') }}" class="row g-3">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label" for="student_id">Student ID</label>
                                    <input class="form-control" id="student_id" name="student_id" value="{{ old('student_id') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="firstname">First name</label>
                                    <input class="form-control" id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="lastname">Last name</label>
                                    <input class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="middle_initial">M.I.</label>
                                    <input class="form-control" id="middle_initial" name="middle_initial" value="{{ old('middle_initial') }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="birth_date">Birth date</label>
                                    <input class="form-control" id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label" for="course">Course</label>
                                    <select class="form-select" id="course" name="course">
                                        <option value="">Select course</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->program_name }}" @selected(old('course') === $program->program_name)>{{ $program->program_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label" for="year">Year</label>
                                    <input class="form-control" id="year" name="year" value="{{ old('year') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="mobile_number">Mobile number</label>
                                    <input class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">Submit Student Registration</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <section class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Employee</h2>
                            <form method="POST" action="{{ route('attendance.pendingEmployee.store') }}" class="row g-3">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label" for="employee_id">Employee ID</label>
                                    <input class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="employee_firstname">First name</label>
                                    <input class="form-control" id="employee_firstname" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="employee_lastname">Last name</label>
                                    <input class="form-control" id="employee_lastname" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="employee_middle_initial">M.I.</label>
                                    <input class="form-control" id="employee_middle_initial" name="middle_initial" value="{{ old('middle_initial') }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="employee_birth_date">Birth date</label>
                                    <input class="form-control" id="employee_birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="department">Department</label>
                                    <input class="form-control" id="department" name="department" value="{{ old('department') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="position">Position</label>
                                    <input class="form-control" id="position" name="position" value="{{ old('position') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="employee_address">Address</label>
                                    <textarea class="form-control" id="employee_address" name="address" rows="2">{{ old('address') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">Submit Employee Registration</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
