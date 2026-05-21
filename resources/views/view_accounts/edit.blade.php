<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .form-label {
            font-weight: 600;
        }
        .edt{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
                <h3 class="edt">Edit User Details</h3>

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" name="fname" id="fname" class="form-control" value="{{ old('fname', $user->fname) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" name="lname" id="lname" class="form-control" value="{{ old('lname', $user->lname) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option disabled selected>Select Role</option>
                            <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="faculty" {{ $user->role == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
</body>
</html>
