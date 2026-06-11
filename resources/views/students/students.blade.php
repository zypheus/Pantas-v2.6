@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
@endsection

@section('content')
<!-- ✅ JavaScript Toggle Functions -->
<script>
    const toggleBtn = document.getElementById('customMenuToggle');
    const closeBtn = document.getElementById('customMenuClose');
    const routeWrapper = document.getElementById('routeWrapper');

    toggleBtn.addEventListener('click', () => {
        routeWrapper.classList.add('open');
    });

    closeBtn.addEventListener('click', () => {
        routeWrapper.classList.remove('open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            routeWrapper.classList.remove('open');
        }
    });
</script>

    <div class="container mt-5 students-page">
        <div class="card">
            <div id="rs" class="card-header text-center">
                <h4>Registered Students</h4>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="mb-3">
                    <!-- Search Form -->
                    <form action="{{ route('students.index') }}" method="GET" class="row g-2 mb-3">
                        <!-- 🔍 Search -->
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Search patrons..."
                                   value="{{ request('search') }}">
                        </div>
                        <!-- 🎓 Program / Course (Loaded from programs table) -->
                        <div class="col-md-4">
                            <select name="program_id" class="form-select form-select-sm">
                                <option value="">All Courses</option>
                    
                                @foreach ($programs as $program)
                                    <option value="{{ $program->program_code }}"
                                        {{ request('program_id') == $program->program_code ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    
                        <!-- 📚 Year Filter -->
                        <div class="col-md-3">
                            <select name="year" class="form-select form-select-sm">
                                <option value="">All Years</option>
                                <option value="1st Year" {{ request('year') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ request('year') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ request('year') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ request('year') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                <option value="5th Year" {{ request('year') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                <option value="6th Year" {{ request('year') == '6th Year' ? 'selected' : '' }}>6th Year</option>
                            </select>
                        </div>
                    
                        <!-- 🔎 Apply Button -->
                        <div class="col-md-1">
                            <button type="submit" id="fil" class="btn btn-primary btn-sm w-100">Filter</button>
                        </div>
                    </form>
                    
                    <!-- Register + Pending -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <a href="{{ route('students.create') }}" id="fil" class="btn btn-add">+ Register Patron</a>
                        <a href="{{ route('pending.index') }}" id="fil" class="btn btn-warning">View Pending Registrations</a>
                        <a href="{{ route('students.pending.requests') }}" id="fil" class="btn btn-warning btn-sm">Patron edit requests</a>
                        <a href="{{ route('students.export') }}" id="fil" class="btn btn-success btn-sm">Export CSV</a>
                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="file" name="file" class="form-control form-control-sm" style="max-width: 220px;" accept=".xlsx,.csv" required>
                            <button type="submit" id="fil" class="btn btn-primary btn-sm">Import</button>
                        </form>
                    </div>
                </div>

                <div class="mb-3 text-center">
                    <a href="{{ route('students.index') }}" id="rs" class="btn btn-outline-primary btn-sm active">Students</a>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">Faculty &amp; Staff</a>
                </div>


                <div class="table-responsive students-table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Actions</th>
                                <th>Generate ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>
                                        @if($student->profile_picture)
                                            <img src="{{ asset($student->profile_picture) }}" alt="Profile" class="profile-img">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $student->lastname }}</td>
                                    <td>{{ $student->firstname }}</td>
                                    <td>{{ $student->course }}</td>
                                    <td>{{ $student->year }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('students.edit', $student->id) }}">Edit</a></li>
                                                <li>
                                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item" type="submit">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Generate
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ url('idcard/front/' . $student->id) }}" target="_blank">Front</a></li>
                                                <li ><a class="dropdown-item" href="{{ url('idcard/back/' . $student->id) }}" target="_blank">Back</a></li>
                                                <li ><a class="dropdown-item" href="{{ url('idcard/download/' . $student->id) }}">Download ZIP</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

                <a href="{{ route('book.index') }}" id="fil" class="btn btn-back mt-3">← Back to Books</a>

            </div>
        </div>
    </div>
    
    
@endsection
