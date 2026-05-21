@extends('layouts.sec')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>SMS — scan template &amp; targeted sends</h3>
        <a href="{{ route('sms.page') }}" class="btn btn-secondary">
            Back to SMS Blast
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header fw-semibold">Attendance scan SMS template</div>
        <div class="card-body">
            <form method="POST" action="{{ route('sms.scan-message.update') }}">
                @csrf

                <div class="mb-3">
                    <label for="scanMessage" class="form-label">Message template</label>
                    <textarea id="scanMessage" name="message" class="form-control" rows="5">{{ old('message', $message) }}</textarea>
                    <small class="text-muted">
                        Tags for scan notices:<br>
                        <b>{name}</b> — student name<br>
                        <b>{status}</b> — IN or OUT<br>
                        <b>{time}</b> — scan time
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save template
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Send to one student</div>
        <div class="card-body">
            <form method="POST" action="{{ route('sms.send-one-student') }}" id="smsOneStudentForm">
                @csrf
                <div class="mb-3 position-relative">
                    <label for="sms_patron_name" class="form-label">Patron</label>
                    <input type="hidden" name="student_id" id="sms_student_id" value="{{ old('student_id') }}">
                    <input type="text" id="sms_patron_name" class="form-control" autocomplete="off"
                        placeholder="Search name or ID number…" value="{{ old('sms_patron_label') }}">
                    <ul id="smsPatronSuggestions" class="list-group position-absolute w-100 shadow-sm"
                        style="z-index: 1050; display: none; max-height: 220px; overflow-y: auto;"></ul>
                    <small class="text-muted">Select a patron from the list (must have a mobile number on file).</small>
                </div>
                <div class="mb-3">
                    <label for="sms_one_message" class="form-label">Message</label>
                    <textarea id="sms_one_message" name="message" class="form-control" rows="4" required
                        placeholder="Hello {name}, …">{{ old('sms_one_message') }}</textarea>
                    <small class="text-muted"><b>{name}</b> is replaced with the student’s name.</small>
                </div>
                <button type="submit" class="btn btn-primary">Send SMS</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Send to all students with overdue books</div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Patrons are included if they have at least one <strong>checked out</strong> loan with a <strong>due date before today</strong> (Asia/Manila), and they have a <strong>mobile number</strong> on file.
                Room-use loans (no due date) are not counted. Currently matching patrons: <strong>{{ $overduePatronsWithMobile }}</strong>.
            </p>
            <form method="POST" action="{{ route('sms.send-overdue') }}" id="smsOverdueForm"
                onsubmit="return confirm('Send this SMS to every patron with overdue books who has a mobile number?');">
                @csrf
                <div class="mb-3">
                    <label for="sms_overdue_message" class="form-label">Message</label>
                    <textarea id="sms_overdue_message" name="message" class="form-control" rows="5" required
                        placeholder="Hello {name}, you have {count} overdue book(s): {titles}. Please return them to the library.">{{ old('sms_overdue_message') }}</textarea>
                    <small class="text-muted">
                        <b>{name}</b> — student name<br>
                        <b>{count}</b> — number of overdue loans<br>
                        <b>{titles}</b> — up to 5 titles, then “…”
                    </small>
                </div>
                <button type="submit" class="btn btn-warning" {{ $overduePatronsWithMobile === 0 ? 'disabled' : '' }}>
                    Send to all overdue ({{ $overduePatronsWithMobile }})
                </button>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('smsOneStudentForm');
    const input = document.getElementById('sms_patron_name');
    const suggestionsBox = document.getElementById('smsPatronSuggestions');
    const studentIdInput = document.getElementById('sms_student_id');

    if (form && input && suggestionsBox && studentIdInput) {
        form.addEventListener('submit', function (e) {
            if (!String(studentIdInput.value || '').trim()) {
                e.preventDefault();
                alert('Choose a patron from the suggestions list.');
            }
        });

        input.addEventListener('input', function () {
            studentIdInput.value = '';
            const query = this.value.trim();
            if (query.length < 1) {
                suggestionsBox.style.display = 'none';
                return;
            }
            fetch('{{ route('patron.suggestions') }}?query=' + encodeURIComponent(query))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    suggestionsBox.innerHTML = '';
                    if (data.length) {
                        data.forEach(function (item) {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action';
                            li.textContent = item.name;
                            li.dataset.id = item.id;
                            li.addEventListener('click', function () {
                                input.value = item.name;
                                studentIdInput.value = item.id;
                                suggestionsBox.style.display = 'none';
                            });
                            suggestionsBox.appendChild(li);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                })
                .catch(function () { suggestionsBox.style.display = 'none'; });
        });

        document.addEventListener('click', function (e) {
            if (!suggestionsBox.contains(e.target) && e.target !== input) {
                suggestionsBox.style.display = 'none';
            }
        });
    }
});
</script>

@endsection
