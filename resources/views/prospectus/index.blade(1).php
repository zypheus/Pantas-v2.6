<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Prospectus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CDN -->

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/fontsource/poppins/latin-400.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/fontsource/poppins/latin-600.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/fontsource/poppins/latin-700.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/prospectus/index.css') }}">
</head>

<body>
    <!-- Header with Left Logo and Right Logout Button -->
    <div class="d-flex align-items-center px-4 py-2 flex-wrap" style="background-color: white;">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
        <h1 class="school-name mb-0 ms-2"></h1>

        <!-- IMPORTANT: add ms-auto to push right -->
        <div class="d-flex gap-2 flex-wrap ms-auto" style="margin-right: 9rem;">
            <a href="{{ route('book.index') }}" class="btn1 btn-sm">Home</a>
            <div class="attendance_dropdown">
                <button class="attendance_dropdown-button">Attendance</button>
                <div class="attendance_dropdown-content">
                    <a href="{{ route('attendance.scan') }}">Attendance</a>
                    <a href="{{ route('attendance_logs.index') }}">Attendance-logs</a>
                </div>
            </div>
            <a href="{{ route('landing') }}" class="btn2 btn-sm"> OPAC</a>
            <a class="btn3 btn-sm">Prospectus Manager</a>
            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Circulation</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('logs.index') }}">Circulation</a>
                    <a href="{{ route('book.report.download') }}">Download Book Report</a>
                    <a href="{{ route('students.report') }}">Student Report</a>
                </div>
            </div>
            <a href="https://area51lmslibrary.com/user-account/?fbclid=IwY2xjawLvE-xleHRuA2FlbQIxMABicmlkETFHTzhpTjBrRURpVWFFdW9hAR7tC4LGq_N7YomZscUpiyZKJxd0BCy69WYZuj5CxaseF8G5ctGQnauMPJnheg_aem_ZvE4NOhe8ZwtNtoumemmyg"
                class="btn4 btn-sm" target="_blank" rel="noopener noreferrer" hidden>
                51 Learned
            </a>
            <a href="{{ route('files.index') }}" class="btn4 btn-sm">Repository</a>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn5">Logout</button>
            </form>
        </div>
    </div>

    <!-- Banner -->
    <section class="hero">
        <div class="hero-text">
            <img src="{{ asset('images/Bannernew.jpg') }}" alt="Smart Library Banner" />
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <h3 class="section-title fw-bold" style="color:#22333b;"> Prospectus Manager</h3>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Filter Form -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <!-- Left: Course Dropdown -->
            <form method="GET" action="{{ route('prospectus.index') }}" class="d-flex align-items-center gap-2">
                <select name="course" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Select Course --</option>
                    @foreach($courses as $course)
                    <option value="{{ $course }}" {{ $selectedCourse===$course ? 'selected' : '' }}>{{ $course }}
                    </option>
                    @endforeach
                </select>
            </form>

            <!-- Right: Add Program + Home -->
            <div class="d-flex align-items-center gap-2">
                <button id="addProgramBtn" class="btn btn-add-subject">Add Program</button>
                <a href="{{ route('book.index') }}" class="btn btn-go-to-home">Go to Home</a>
            </div>
        </div>

        <!-- Subject Grid -->
        @if($selectedCourse && $subjectsByYear)
        <div class="row g-4">
            @foreach($subjectsByYear as $year => $subjects)
            <div class="col-12 col-md-6">
                <div class="card-year">
                    <div class="year-title">{{ $year }}</div>
                    <div class="subject-list">
                        @foreach($subjects as $subj)
                        <div class="subject-item">
                            <span>{{ $subj->subject }}</span>
                            <div class="d-flex gap-1">
                                <a href="{{ route('prospectus.edit', $subj->id) }}" class="btn btn-sm btn-edit">Edit</a>
                                <form action="{{ route('prospectus.destroy', $subj->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this subject?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-end">
                        <a href="{{ route('prospectus.addSubject', ['course' => $selectedCourse, 'year' => $year]) }}"
                            class="btn btn-sm btn-success">
                            ➕ Add Subject
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @elseif($selectedCourse)
        <p class="text-muted text-center mt-4">No subjects found for {{ $selectedCourse }}</p>
        @endif
    </div>

    <!-- Modal for Add Program -->
    <div class="modal fade" id="programModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS + Fetch -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('addProgramBtn').addEventListener('click', function () {
                const modal = new bootstrap.Modal(document.getElementById('programModal'));
                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = '<p>Loading form...</p>';

                fetch("{{ route('prospectus.create') }}")
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.text();
                    })
                    .then(html => {
                        modalContent.innerHTML = html;
                    })
                    .catch(error => {
                        modalContent.innerHTML = `<p class="text-danger">Failed to load: ${error.message}</p>`;
                    });

                modal.show();
            });
        });
    </script>
</body>
</html>