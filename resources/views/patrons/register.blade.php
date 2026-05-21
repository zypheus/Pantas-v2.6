<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e3f2fd);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 3rem 2rem;
            width: 100%;
            max-width: 520px;
        }

        .register-title {
            font-weight: 700;
            font-size: 1.9rem;
        }

        .register-subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .rounded-custom {
            border-radius: 12px;
            padding: 0.8rem;
            font-size: 1rem;
        }

        .btn-primary, .btn-outline-secondary {
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .register-card { padding: 2rem 1.2rem; }
            .register-title { font-size: 1.5rem; }
        }

        .toggle-btns .btn {
            border-radius: 50px;
            padding: 0.6rem 1.4rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/d.png') }}" alt="Logo" class="mb-3" style="max-width:160px;">
            <h3 class="register-title">School Registration</h3>
            <p class="register-subtitle">Choose type and fill out your details</p>

            <div class="toggle-btns d-flex justify-content-center gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary active" id="studentBtn">Student</button>
                <button type="button" class="btn btn-outline-secondary" id="employeeBtn">Employee</button>
            </div>
        </div>

        <!-- STUDENT REGISTRATION FORM -->
        <form id="studentForm" method="POST" action="{{ route('pending.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="firstname" class="form-control rounded-custom" placeholder="First Name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="lastname" class="form-control rounded-custom" placeholder="Last Name" required>
                </div>
            </div>

            <div class="mt-3">
                <input type="text" name="course" class="form-control rounded-custom" placeholder="Course" required>
            </div>

            <div class="mt-3">
                <select name="year" class="form-select rounded-custom" required>
                    <option value="" disabled selected>Select Year Level</option>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year">Fourth Year</option>
                    <option value="Fifth Year">Fifth Year</option>
                </select>
            </div>

            <div class="mt-3">
                <label class="form-label">Profile Picture (optional)</label>
                <input type="file" name="profile_picture" class="form-control rounded-custom" accept=".jpg,.jpeg,.png">
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Submit Student Registration</button>
            </div>
        </form>

        <!-- EMPLOYEE REGISTRATION FORM -->
        <form id="employeeForm" method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" style="display:none;">
            @csrf
            <div class="mt-3">
                <input type="text" name="employee_name" class="form-control rounded-custom" placeholder="Full Name" required>
            </div>
            <div class="mt-3">
                <input type="text" name="department" class="form-control rounded-custom" placeholder="Department" required>
            </div>
            <div class="mt-3">
                <input type="text" name="position" class="form-control rounded-custom" placeholder="Position" required>
            </div>
            <div class="mt-3">
                <label class="form-label">Formal Picture</label>
                <input type="file" name="formal_picture" class="form-control rounded-custom" accept=".jpg,.jpeg,.png">
            </div>
            <div class="mt-3">
                <input type="date" name="birth_date" class="form-control rounded-custom" placeholder="Birthdate">
            </div>
            <div class="mt-3">
                <input type="text" name="sex" class="form-control rounded-custom" placeholder="Sex (Male/Female)">
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Submit Employee Registration</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary w-100">Back to Home</a>
        </div>
    </div>

    <script>
        const studentBtn = document.getElementById('studentBtn');
        const employeeBtn = document.getElementById('employeeBtn');
        const studentForm = document.getElementById('studentForm');
        const employeeForm = document.getElementById('employeeForm');

        studentBtn.addEventListener('click', () => {
            studentForm.style.display = 'block';
            employeeForm.style.display = 'none';
            studentBtn.classList.add('btn-primary', 'active');
            employeeBtn.classList.remove('btn-primary', 'active');
            employeeBtn.classList.add('btn-outline-secondary');
        });

        employeeBtn.addEventListener('click', () => {
            employeeForm.style.display = 'block';
            studentForm.style.display = 'none';
            employeeBtn.classList.add('btn-primary', 'active');
            studentBtn.classList.remove('btn-primary', 'active');
            studentBtn.classList.add('btn-outline-secondary');
        });
    </script>
</body>
</html>
