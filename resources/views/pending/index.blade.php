<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Registrations</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="container py-4">
    <h3>Pending Registrations</h3>

    @if(session('success')) 
        <div class="alert alert-success">{{ session('success') }}</div> 
    @endif
    @if(session('error')) 
        <div class="alert alert-danger">{{ session('error') }}</div> 
    @endif

    <!-- Toggle Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <button id="showStudents" class="btn btn-primary me-2">View Students</button>
            <button id="showEmployees" class="btn btn-outline-primary" hidden>View Employees</button>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            ← Back to Registered
        </a>
    </div>

    <!-- 🧑‍🎓 Pending Students Table -->
    <div id="studentTable">
        <h4>Pending Student Registrations</h4>
        <table class="table table-bordered mt-3">
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
                                <img src="{{ asset($p->profile_picture) }}" width="80">
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
                    <tr><td colspan="6">No pending student registrations</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 🧑‍💼 Pending Employees Table -->
    <div id="employeeTable" class="hidden">
        <h4>Pending Employee Registrations</h4>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingEmployees as $e)
                    <tr>
                        <td>
                            @if($e->formal_picture)
                                <img src="{{ asset($e->formal_picture) }}" width="80">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $e->firstname }} {{ $e->lastname }}</td>
                        <td>{{ $e->department }}</td>
                        <td>{{ $e->position }}</td>
                        <td>
                            <form action="{{ route('employees.approve', $e->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form action="{{ route('employees.reject', $e->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No pending employee registrations</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Toggle Script -->
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

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
