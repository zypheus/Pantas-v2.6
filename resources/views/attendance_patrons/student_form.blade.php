@extends('layouts.sidebar')

@section('title', $mode === 'create' ? 'Add Attendance Student' : 'Edit Attendance Student')

@section('header')
    <div>
        <h1 class="h4 mb-1">{{ $mode === 'create' ? 'Add Attendance Student' : 'Edit Attendance Student' }}</h1>
        <p class="text-muted mb-0">Store student details, contact information, photo, signature, and QR/RFID assignment.</p>
    </div>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('attendance.patrons.students.store') : route('attendance.patrons.students.update', $student) }}" class="card border-0 shadow-sm">
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Student ID *</label><input class="form-control" name="student_id" value="{{ old('student_id', $student->student_id) }}" required></div>
                <div class="col-md-3"><label class="form-label">First name *</label><input class="form-control" name="firstname" value="{{ old('firstname', $student->firstname) }}" required></div>
                <div class="col-md-3"><label class="form-label">Last name *</label><input class="form-control" name="lastname" value="{{ old('lastname', $student->lastname) }}" required></div>
                <div class="col-md-3"><label class="form-label">Middle initial</label><input class="form-control" name="middle_initial" value="{{ old('middle_initial', $student->middle_initial) }}"></div>
                <div class="col-md-3"><label class="form-label">Birth date</label><input class="form-control" type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date ? substr((string) $student->birth_date, 0, 10) : '') }}"></div>
                <div class="col-md-3"><label class="form-label">Educational level</label><input class="form-control" name="educational_level" value="{{ old('educational_level', $student->educational_level) }}" placeholder="College"></div>
                <div class="col-md-3">
                    <label class="form-label">Course / Program</label>
                    <select class="form-select" name="course">
                        <option value="">Select program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->program_name }}" @selected(old('course', $student->course) === $program->program_name)>{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Year level</label><input class="form-control" name="year" value="{{ old('year', $student->year) }}"></div>
                <div class="col-md-3"><label class="form-label">Mobile number</label><input class="form-control" name="mobile_number" value="{{ old('mobile_number', $student->mobile_number) }}"></div>
                <div class="col-md-3"><label class="form-label">Blood type</label><input class="form-control" name="blood_type" value="{{ old('blood_type', $student->blood_type) }}"></div>
                @if ($mode === 'create')
                    <div class="col-md-3"><label class="form-label">QR/RFID code</label><input class="form-control" name="qrcode" value="{{ old('qrcode') }}" placeholder="Auto: S-00000001"></div>
                @endif
                <div class="col-md-6"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ old('address', $student->address) }}"></div>
                <div class="col-md-3"><label class="form-label">Emergency contact</label><input class="form-control" name="emergency_person" value="{{ old('emergency_person', $student->emergency_person) }}"></div>
                <div class="col-md-3"><label class="form-label">Relationship</label><input class="form-control" name="emergency_relationship" value="{{ old('emergency_relationship', $student->emergency_relationship) }}"></div>
                <div class="col-md-3"><label class="form-label">Emergency number</label><input class="form-control" name="emergency_number" value="{{ old('emergency_number', $student->emergency_number) }}"></div>
                <div class="col-md-9"><label class="form-label">Emergency address</label><input class="form-control" name="emergency_address" value="{{ old('emergency_address', $student->emergency_address) }}"></div>
                <div class="col-md-4"><label class="form-label">Profile picture</label><input class="form-control" type="file" name="profile_picture" accept="image/*"></div>
                <div class="col-md-8"><label class="form-label">Signature data/base64</label><textarea class="form-control" name="student_signature" rows="2" placeholder="Optional base64 signature">{{ old('student_signature') }}</textarea></div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('attendance.pending.index', ['tab' => 'students']) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">{{ $mode === 'create' ? 'Save Student' : 'Update Student' }}</button>
        </div>
    </form>
@endsection
