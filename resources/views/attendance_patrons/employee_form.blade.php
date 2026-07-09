@extends('layouts.sidebar')

@section('title', $mode === 'create' ? 'Add Attendance Employee' : 'Edit Attendance Employee')

@section('header')
    <div>
        <h1 class="h4 mb-1">{{ $mode === 'create' ? 'Add Attendance Employee' : 'Edit Attendance Employee' }}</h1>
        <p class="text-muted mb-0">Store employee details, government IDs, emergency details, photo, signature, and QR/RFID assignment.</p>
    </div>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('attendance.patrons.employees.store') : route('attendance.patrons.employees.update', $employee) }}" class="card border-0 shadow-sm">
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Employee ID *</label><input class="form-control" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required></div>
                <div class="col-md-3"><label class="form-label">Employee number</label><input class="form-control" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}"></div>
                <div class="col-md-3"><label class="form-label">First name *</label><input class="form-control" name="firstname" value="{{ old('firstname', $employee->firstname) }}" required></div>
                <div class="col-md-3"><label class="form-label">Last name *</label><input class="form-control" name="lastname" value="{{ old('lastname', $employee->lastname) }}" required></div>
                <div class="col-md-2"><label class="form-label">Middle initial</label><input class="form-control" name="middle_initial" value="{{ old('middle_initial', $employee->middle_initial) }}"></div>
                <div class="col-md-3"><label class="form-label">Department</label><input class="form-control" name="department" value="{{ old('department', $employee->department) }}"></div>
                <div class="col-md-3"><label class="form-label">Position</label><input class="form-control" name="position" value="{{ old('position', $employee->position) }}"></div>
                <div class="col-md-2"><label class="form-label">Birth date</label><input class="form-control" type="date" name="birth_date" value="{{ old('birth_date', $employee->birth_date ? substr((string) $employee->birth_date, 0, 10) : '') }}"></div>
                <div class="col-md-2"><label class="form-label">Mobile</label><input class="form-control" name="mobile_number" value="{{ old('mobile_number', $employee->mobile_number) }}"></div>
                <div class="col-md-2"><label class="form-label">Sex</label><input class="form-control" name="sex" value="{{ old('sex', $employee->sex) }}"></div>
                <div class="col-md-2"><label class="form-label">Civil status</label><input class="form-control" name="civil_status" value="{{ old('civil_status', $employee->civil_status) }}"></div>
                <div class="col-md-2"><label class="form-label">Blood type</label><input class="form-control" name="blood_type" value="{{ old('blood_type', $employee->blood_type) }}"></div>
                @if ($mode === 'create')
                    <div class="col-md-2"><label class="form-label">QR/RFID</label><input class="form-control" name="qrcode" value="{{ old('qrcode') }}" placeholder="Auto: E-00000001"></div>
                @endif
                <div class="col-md-3"><label class="form-label">TIN</label><input class="form-control" name="tin_id_number" value="{{ old('tin_id_number', $employee->tin_id_number) }}"></div>
                <div class="col-md-3"><label class="form-label">PhilHealth</label><input class="form-control" name="philhealth_number" value="{{ old('philhealth_number', $employee->philhealth_number) }}"></div>
                <div class="col-md-3"><label class="form-label">SSS</label><input class="form-control" name="sss_number" value="{{ old('sss_number', $employee->sss_number) }}"></div>
                <div class="col-md-3"><label class="form-label">HDMF</label><input class="form-control" name="hdmf_number" value="{{ old('hdmf_number', $employee->hdmf_number) }}"></div>
                <div class="col-md-6"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ old('address', $employee->address) }}"></div>
                <div class="col-md-3"><label class="form-label">Emergency contact</label><input class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"></div>
                <div class="col-md-3"><label class="form-label">Relationship</label><input class="form-control" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"></div>
                <div class="col-md-3"><label class="form-label">Emergency number</label><input class="form-control" name="emergency_contact_number" value="{{ old('emergency_contact_number', $employee->emergency_contact_number) }}"></div>
                <div class="col-md-9"><label class="form-label">Emergency address</label><input class="form-control" name="emergency_address" value="{{ old('emergency_address', $employee->emergency_address) }}"></div>
                <div class="col-md-4"><label class="form-label">Formal picture</label><input class="form-control" type="file" name="formal_picture" accept="image/*"></div>
                <div class="col-md-8"><label class="form-label">Signature data/base64</label><textarea class="form-control" name="employee_signature" rows="2" placeholder="Optional base64 signature">{{ old('employee_signature') }}</textarea></div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('attendance.pending.index', ['tab' => 'employees']) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">{{ $mode === 'create' ? 'Save Employee' : 'Update Employee' }}</button>
        </div>
    </form>
@endsection
