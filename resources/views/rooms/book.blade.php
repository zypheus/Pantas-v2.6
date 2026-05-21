<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Study Room</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
</head>
<body style="background: #f1f5f9; min-height: 100vh;">
<div class="rooms-page rooms-page--public">
    <header class="rooms-page__hero" style="border: none; padding-bottom: 0;">
        <div>
            <p class="rooms-page__eyebrow">Library room booking</p>
            <h1 class="rooms-page__title">Book a study room</h1>
            <p class="rooms-page__subtitle">Submit a request — staff will review and approve your reservation.</p>
        </div>
        <a href="{{ auth()->check() ? route('book.index') : route('home') }}" class="rooms-btn rooms-btn--outline">← Home</a>
    </header>

    @auth
        @include('rooms.partials.subnav')
    @endauth

    @include('rooms.partials.alerts')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="rooms-book-card rooms-form">
                <form method="POST" action="{{ route('room-reservations.store') }}">
                    @csrf

                    <div class="rooms-form-section">
                        <h2 class="rooms-form-section__title">Room &amp; schedule</h2>
                        <div class="mb-3">
                            <label class="form-label" for="room_id">Room</label>
                            <select name="room_id" id="room_id" class="form-select" required>
                                <option value="">Select a room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} (max {{ $room->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="datePicker">Date</label>
                            <input type="text" id="datePicker" name="date" class="form-control" required
                                   value="{{ old('date') }}" placeholder="Select date">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Start time</label>
                                <div class="d-flex gap-2">
                                    <select id="start_time" name="start_time" class="form-select" required>
                                        <option value="">Time</option>
                                    </select>
                                    <select id="start_ampm" name="start_ampm" class="form-select" required style="max-width: 5rem;">
                                        <option value="AM" {{ old('start_ampm') === 'PM' ? '' : 'selected' }}>AM</option>
                                        <option value="PM" {{ old('start_ampm') === 'PM' ? 'selected' : '' }}>PM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End time</label>
                                <div class="d-flex gap-2">
                                    <select id="end_time" name="end_time" class="form-select" required>
                                        <option value="">Time</option>
                                    </select>
                                    <select id="end_ampm" name="end_ampm" class="form-select" required style="max-width: 5rem;">
                                        <option value="AM" {{ old('end_ampm') === 'PM' ? '' : 'selected' }}>AM</option>
                                        <option value="PM" {{ old('end_ampm') === 'PM' ? 'selected' : '' }}>PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rooms-form-section">
                        <h2 class="rooms-form-section__title">Contact &amp; attendees</h2>
                        <div class="mb-3">
                            <label class="form-label" for="patron_email">Email</label>
                            <input type="email" name="patron_email" id="patron_email" class="form-control" required
                                   value="{{ old('patron_email') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="numStudents">Number of students</label>
                            <input type="number" name="number_of_students" id="numStudents" class="form-control"
                                   min="1" max="20" required value="{{ old('number_of_students') }}"
                                   placeholder="Max 20">
                        </div>
                        <div id="studentFields"></div>
                    </div>

                    <button type="submit" class="rooms-btn rooms-btn--primary w-100">Submit reservation request</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <aside class="rooms-book-aside">
                <h3>Before you book</h3>
                <ul>
                    <li>Reservations require staff approval.</li>
                    <li>Choose a date from today onward.</li>
                    <li>Enter the full name of each student attending.</li>
                    <li>You will receive email confirmation when approved.</li>
                </ul>
            </aside>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script>
    flatpickr('#datePicker', {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        disableMobile: true,
    });

    document.addEventListener('DOMContentLoaded', function () {
        const startSelect = document.getElementById('start_time');
        const endSelect = document.getElementById('end_time');
        const times = [];
        for (let h = 1; h <= 12; h++) {
            times.push(`${h}:00`, `${h}:30`);
        }
        times.forEach(time => {
            ['start_time', 'end_time'].forEach(id => {
                const sel = document.getElementById(id);
                const opt = document.createElement('option');
                opt.value = time;
                opt.textContent = time;
                sel.appendChild(opt);
            });
        });

        const numStudents = document.getElementById('numStudents');
        const studentFields = document.getElementById('studentFields');
        numStudents.addEventListener('input', function () {
            studentFields.innerHTML = '';
            const count = parseInt(this.value, 10);
            if (!isNaN(count) && count > 0 && count <= 20) {
                for (let i = 1; i <= count; i++) {
                    studentFields.innerHTML += `
                        <div class="mb-3">
                            <label class="form-label">Student ${i} name</label>
                            <input type="text" name="student_names[]" class="form-control" required>
                        </div>`;
                }
            } else if (count > 20) {
                studentFields.innerHTML = '<div class="alert alert-warning">Maximum 20 students per booking.</div>';
            }
        });
        if (numStudents.value) numStudents.dispatchEvent(new Event('input'));
    });
</script>
</body>
</html>
