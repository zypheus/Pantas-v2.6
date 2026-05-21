<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/signature_pad/signature_pad.umd.min.js') }}"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        canvas {
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .btn-save {
            background-color: #007bff;
            color: white;
        }
        .btn-save:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="card">
        <div class="card-header text-center">
            <h4>Edit Employee Information</h4>
        </div>

        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form id="employeeForm" method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h5 class="mb-3">Employee Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="firstname" class="form-control" placeholder="First Name" value="{{ old('firstname', $employee->firstname) }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="lastname" class="form-control" placeholder="Last Name" value="{{ old('lastname', $employee->lastname) }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="department" class="form-control" placeholder="Department" value="{{ old('department', $employee->department) }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="position" class="form-control" placeholder="Position" value="{{ old('position', $employee->position) }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="employee_id" class="form-control" placeholder="Employee ID" value="{{ old('employee_id', $employee->employee_id) }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="date" name="birth_date" class="form-control" placeholder="Birth Date" value="{{ old('birth_date', $employee->birth_date) }}" required>
                    </div>

                    <div class="col-md-6">
                        <select name="sex" class="form-select" required>
                            <option value="">Select Sex</option>
                            <option value="Male" {{ old('sex', $employee->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $employee->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('sex', $employee->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="tin_id_number" class="form-control" placeholder="TIN ID Number" value="{{ old('tin_id_number', $employee->tin_id_number) }}">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="philhealth_number" class="form-control" placeholder="PhilHealth Number" value="{{ old('philhealth_number', $employee->philhealth_number) }}">
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="sss_number" class="form-control" placeholder="SSS Number" value="{{ old('sss_number', $employee->sss_number) }}">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="hdmf_number" class="form-control" placeholder="HDMF Number" value="{{ old('hdmf_number', $employee->hdmf_number) }}">
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="blood_type" class="form-control" placeholder="Blood Type" value="{{ old('blood_type', $employee->blood_type) }}">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="civil_status" class="form-control" placeholder="Civil Status" value="{{ old('civil_status', $employee->civil_status) }}">
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_name" class="form-control" placeholder="Emergency Contact Name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_relationship" class="form-control" placeholder="Relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_number" class="form-control" placeholder="Contact Number" value="{{ old('emergency_contact_number', $employee->emergency_contact_number) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Formal Picture</label>
                        <input type="file" name="formal_picture" class="form-control" accept=".jpg,.jpeg,.png">
                        @if($employee->formal_picture)
                            <div class="mt-2">
                                <img src="{{ asset($employee->formal_picture) }}" alt="Formal Picture" width="120" class="rounded">
                        
                                <div class="mt-2">
                                    <a href="{{ asset($employee->formal_picture) }}" 
                                       download 
                                       class="btn btn-outline-primary btn-sm">
                                        Download Image
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Home Address">{{ old('address', $employee->address) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Signature (draw below)</label><br>
                        <canvas id="employeeSignaturePad" width="500" height="150"></canvas>
                        <input type="hidden" name="employee_signature" id="employeeSignatureInput" value="{{ old('employee_signature', $employee->employee_signature) }}">
                        <div class="mt-2">
                            <button type="button" id="clearEmployeeSignature" class="btn btn-outline-danger btn-sm">Clear</button>
                        </div>

                        @if($employee->employee_signature)
                            <div class="mt-3">
                                <p>Current Signature:</p>
                                <img src="{{ asset($employee->employee_signature) }}" alt="Current Signature" height="80">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-save px-4">Update Employee</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary px-4">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById('employeeSignaturePad');
    const signaturePad = new SignaturePad(canvas);
    const input = document.getElementById('employeeSignatureInput');

    document.getElementById('clearEmployeeSignature').addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });

    document.getElementById('employeeForm').addEventListener('submit', function () {
        if (!signaturePad.isEmpty()) {
            input.value = signaturePad.toDataURL();
        }
    });
</script>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
