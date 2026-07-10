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

    <form id="attendanceStudentForm" method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('attendance.patrons.students.store') : route('attendance.patrons.students.update', $student) }}" class="card border-0 shadow-sm">
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
                <div class="col-md-8">
                    <label class="form-label" for="attendanceStudentSignaturePad">Signature (draw below)</label>
                    <input type="hidden" name="student_signature" id="attendanceStudentSignatureInput" value="{{ old('student_signature', $student->student_signature) }}">
                    <div class="attendance-signature-box">
                        <canvas id="attendanceStudentSignaturePad" class="attendance-signature-pad"></canvas>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        <button type="button" id="clearAttendanceStudentSignature" class="btn btn-outline-danger btn-sm">Clear signature</button>
                    </div>
                    @if ($mode === 'edit' && $student->student_signature)
                        <div class="mt-3">
                            <p class="small text-muted mb-1">Current signature</p>
                            <img src="{{ asset($student->student_signature) }}" alt="Current student signature" class="attendance-signature-preview">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('attendance.pending.index', ['tab' => 'students']) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">{{ $mode === 'create' ? 'Save Student' : 'Update Student' }}</button>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .attendance-signature-box {
            width: 100%;
            max-width: 560px;
            height: 160px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
        }

        .attendance-signature-pad {
            display: block;
            width: 100%;
            height: 100%;
            touch-action: none;
            cursor: crosshair;
        }

        .attendance-signature-preview {
            max-width: 260px;
            max-height: 90px;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
            object-fit: contain;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('attendanceStudentForm');
            const canvas = document.getElementById('attendanceStudentSignaturePad');
            const input = document.getElementById('attendanceStudentSignatureInput');
            const clearButton = document.getElementById('clearAttendanceStudentSignature');

            if (!form || !canvas || !input || !clearButton) return;

            const context = canvas.getContext('2d');
            let drawing = false;
            let hasDrawing = false;

            function resizeCanvas() {
                const ratio = window.devicePixelRatio || 1;
                const rect = canvas.getBoundingClientRect();
                const snapshot = hasDrawing ? canvas.toDataURL('image/png') : null;

                canvas.width = Math.max(1, Math.floor(rect.width * ratio));
                canvas.height = Math.max(1, Math.floor(rect.height * ratio));

                context.setTransform(ratio, 0, 0, ratio, 0, 0);
                context.lineCap = 'round';
                context.lineJoin = 'round';
                context.lineWidth = 2;
                context.strokeStyle = '#1f2937';

                if (snapshot) {
                    const image = new Image();
                    image.onload = function () {
                        context.drawImage(image, 0, 0, rect.width, rect.height);
                    };
                    image.src = snapshot;
                }
            }

            function point(event) {
                const rect = canvas.getBoundingClientRect();
                const source = event.touches ? event.touches[0] : event;

                return {
                    x: source.clientX - rect.left,
                    y: source.clientY - rect.top,
                };
            }

            function start(event) {
                drawing = true;
                const position = point(event);
                context.beginPath();
                context.moveTo(position.x, position.y);
                event.preventDefault();
            }

            function move(event) {
                if (!drawing) return;
                const position = point(event);
                context.lineTo(position.x, position.y);
                context.stroke();
                hasDrawing = true;
                event.preventDefault();
            }

            function end() {
                if (!drawing) return;
                drawing = false;
                if (hasDrawing) {
                    input.value = canvas.toDataURL('image/png');
                }
            }

            clearButton.addEventListener('click', function () {
                context.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawing = false;
                input.value = '';
            });

            form.addEventListener('submit', function () {
                if (hasDrawing) {
                    input.value = canvas.toDataURL('image/png');
                }
            });

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            window.addEventListener('mouseup', end);
            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', move, { passive: false });
            canvas.addEventListener('touchend', end);
            window.addEventListener('resize', resizeCanvas);

            resizeCanvas();
        });
    </script>
@endpush
