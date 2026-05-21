<!DOCTYPE html>
<html>
<head>
    <title>Registered Faculty</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
</head>
<body>

<!-- Header with Left Logo and Right Logout Button -->
<div class="d-flex align-items-center px-4 py-2 flex-wrap" style="background-color: white; position: relative;">
    <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
    <h1 class="school-name mb-0 ms-2"></h1>

    <button id="customMenuToggle" class="d-md-none toggle-btn">&#9776;</button>

    <div id="routeWrapper" class="d-flex gap-2 flex-wrap ms-auto responsive-nav">
        <button id="customMenuClose" class="d-md-none close-btn">&times;</button>

        <a href="{{ route('book.index') }}" class="btn0 btn-sm">Home</a>
        <a class="btn2 btn-sm" href="{{ route('attendance.scan') }}">Attendance</a>
        <a class="btn2 btn-sm" href="{{ route('attendance_logs.index') }}">Attendance-logs</a>
        <a href="{{ route('students.report') }}" class="btn2 btn-sm">ID Generation</a>
        <a href="{{ route('files.index') }}" class="btn4 btn-sm" hidden>Repository</a>
        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn5">Logout</button>
        </form>
    </div>
</div>

<!-- ✅ JavaScript Toggle Functions -->
<script>
    const toggleBtn = document.getElementById('customMenuToggle');
    const closeBtn = document.getElementById('customMenuClose');
    const routeWrapper = document.getElementById('routeWrapper');
    toggleBtn.addEventListener('click', () => routeWrapper.classList.add('open'));
    closeBtn.addEventListener('click', () => routeWrapper.classList.remove('open'));
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) routeWrapper.classList.remove('open');
    });
</script>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h4>Registered Faculty</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Search + Buttons -->
            <div class="mb-3">
                <div class="d-flex mb-2" style="max-width: 350px;">
                    <form action="{{ route('employees.index') }}" method="GET" class="d-flex w-100">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search faculty..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm ms-2">Search</button>
                    </form>
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <a href="" class="btn btn-add">+ Register Faculty</a>
                    <a href="{{ route('pending.index') }}" class="btn btn-warning">View Pending Registrations</a>
                </div>
                
                <div class="mb-3 text-center">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm ">Students</a>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm active">Faculty</a>
                </div>
            </div>

            <!-- Faculty Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                            <th>Generate ID</th> <!-- ✅ Added column -->
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($faculty as $employee)
                            <tr>
                                <td>
                                    @if($employee->formal_picture)
                                        <img src="{{ asset($employee->formal_picture) }}" width="80" class="rounded">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{{ $employee->firstname }} {{ $employee->lastname }}</td>
                                <td>{{ $employee->department }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->qrcode }}</td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Options
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">Edit</a></li>
                                            <li>
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item" type="submit">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                <!-- ✅ New Generate ID dropdown -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Generate
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.id.front', $employee->id) }}" target="_blank">Front</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.id.back', $employee->id) }}" target="_blank">Back</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.id.download', $employee->id) }}">Download ZIP</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No faculty found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $faculty->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <a href="{{ route('book.index') }}" class="btn btn-back mt-3">← Back to Books</a>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
