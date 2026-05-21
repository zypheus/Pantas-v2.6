@extends('layouts.sec')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/books/logs.css') }}">
@endsection

@section('content')
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <a href="{{ route('book.index') }}" id="back" class="btn btn-secondary">Back</a>
        <h3 class="mb-0">Record Book Transaction</h3>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @error('student_id')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <form action="{{ route('logs.store') }}" method="POST" class="mt-3" id="logTransactionForm">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="rfid" class="form-label">RFID Tag:</label>
                <input type="text" class="form-control" name="rfid" id="rfid_input"
                    value="{{ request('rfid') }}" style="border-radius: 50px;" required>
                <ul id="bookSuggestions" class="list-group position-absolute w-100"
                    style="z-index:1000; display:none; max-height:200px; overflow-y:auto;"></ul>
            </div>

            <div class="col-md-4 position-relative">
                <label for="patron_name" class="form-label">Patron Name:</label>
                <input type="hidden" name="student_id" id="student_id" value="{{ request('student_id') }}">
                <input type="text" id="patron_name" class="form-control" autocomplete="off"
                    placeholder="Search name or ID number…"
                    value="{{ $prefillPatronLabel ?? '' }}">
                <ul id="patronSuggestions" class="list-group position-absolute w-100"
                    style="z-index:1000; display:none; max-height:200px; overflow-y:auto;"></ul>
            </div>

            <div class="col-md-4">
                <label for="status" class="form-label">Circulation:</label>
                <select name="status" id="status_select" class="form-select" style="border-radius: 50px;" required>
                    <option value="checked_out" {{ request('status', 'checked_out') === 'checked_out' ? 'selected' : '' }}>Check out (outside library)</option>
                    <option value="room_use" {{ request('status') === 'room_use' ? 'selected' : '' }}>Room use (inside library only)</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Check in</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" id="record" class="btn btn-primary">Record Transaction</button>
        </div>
    </form>

    <form method="GET" action="{{ route('logs.index') }}" class="row g-3 align-items-end mb-4 mt-4">
        <div class="col-md-4">
            <label class="form-label">Filter by Patron Name</label>
            <select class="form-select" name="student_id">
                <option value="">All</option>
                @foreach ($filterStudents as $fs)
                <option value="{{ $fs->id }}" {{ (string) request('student_id') === (string) $fs->id ? 'selected' : '' }}>
                    {{ $fs->lastname }}, {{ $fs->firstname }}@if($fs->id_number) — {{ $fs->id_number }}@endif
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filter by Book Title</label>
            <select class="form-select" name="book_title">
                <option value="">All</option>
                @foreach ($bookTitles as $title)
                <option value="{{ $title }}" {{ request('book_title')==$title ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Loan type</label>
            <select class="form-select" name="circulation_type">
                <option value="">All</option>
                <option value="checkout" {{ request('circulation_type') === 'checkout' ? 'selected' : '' }}>Check out</option>
                <option value="room_use" {{ request('circulation_type') === 'room_use' ? 'selected' : '' }}>Room use</option>
            </select>
        </div>

        <div class="col-md-12 d-flex justify-content-end mt-2">
            <button type="submit" id="apply" class="btn btn-primary me-2">Apply Filters</button>
            <a href="{{ route('logs.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <h3 class="transact mt-5">Transaction Logs</h3>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Barcode</th>
                    <th>Patron Name</th>
                    <th>Status</th>
                    <th>Loan type</th>
                    <th>Timestamp</th>
                    <th>Due Date</th>
                    <th>Renewals</th>
                    <th>Returned</th>
                    <th>Overdue</th>
                    <th>Days Late</th>
                    <th>Fine</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                $isCheckIn = $log->status === 'Checked In';
                $daysLate = 0;
                $isOverdue = false;
                $tooltip = '';

                if ($isCheckIn && $log->due_date && $log->returned_date) {
                    $daysLate = max(0, $log->due_date->diffInDays($log->returned_date, false));
                    $daysLateWhole = floor($daysLate);

                    $settings = \App\Models\FineSetting::latest('created_at')->first();
                    $gracePeriod = $settings->grace_period_days ?? 0;
                    $finePerDay = $settings->fine_per_day ?? 0;

                    $effectiveLate = max(0, $daysLateWhole - $gracePeriod);
                    $isOverdue = $effectiveLate > 0;

                    if ($isOverdue) {
                        $tooltip = "{$effectiveLate} day(s) × ₱".number_format($finePerDay,2)." = ₱".number_format($log->fine_incurred,2);
                    }
                }
                @endphp
                <tr>
                    <td>{{ $log->book->title_statement ?? 'N/A' }}</td>
                    <td>{{ $log->book->barcode ?? 'N/A' }}</td>
                    <td>{{ $log->patronLabel() }}</td>
                    <td>{{ $log->status }}</td>
                    <td>{{ $log->circulationLabel() }}</td>
                    <td>{{ $log->timestamp_manila ?? '—' }}</td>
                    <td>{{ $log->due_date ?? '—' }}</td>
                    <td>
                        @if(($log->circulation_type ?? \App\Models\BookLog::CIRCULATION_CHECKOUT) === \App\Models\BookLog::CIRCULATION_CHECKOUT)
                            {{ (int) ($log->renew_count ?? 0) }}/{{ \App\Http\Controllers\BookController::MAX_RENEWALS_PER_LOAN }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->returned_date ?? '—' }}</td>

                    <td>
                        @if($isCheckIn)
                        @if($isOverdue)
                        <span class="badge bg-danger">Overdue</span>
                        @else
                        <span class="badge bg-success">On Time</span>
                        @endif
                        @else
                        —
                        @endif
                    </td>

                    <td>
                        @if($isCheckIn && $isOverdue)
                        {{ $effectiveLate }}
                        @else
                        —
                        @endif
                    </td>

                    <td>
                        @if($isCheckIn && $isOverdue)
                        <span data-bs-toggle="tooltip" title="{{ $tooltip }}">
                            ₱{{ number_format($log->fine_incurred,2) }} <i class="bi bi-info-circle"></i>
                        </span>
                        @else
                        —
                        @endif
                    </td>

                    <td class="text-nowrap">
                        @if($log->status === 'Checked Out'
                            && (($log->circulation_type ?? \App\Models\BookLog::CIRCULATION_CHECKOUT) === \App\Models\BookLog::CIRCULATION_CHECKOUT)
                            && $log->due_date
                            && (int) ($log->renew_count ?? 0) < \App\Http\Controllers\BookController::MAX_RENEWALS_PER_LOAN)
                            <form method="POST" action="{{ route('logs.renew', $log->book_id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $log->student_id }}">
                                <button type="submit" class="btn btn-sm btn-outline-primary"
                                    onclick="return confirm('Renew this loan and extend the due date?');">
                                    Renew
                                </button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center text-muted">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <a href="{{ url('/export-transactions') }}" class="btn12">Download Transactions Report</a>
    </div>

</div>

@if(session('overdue_modal'))
<div class="modal fade" id="overdueModal" tabindex="-1" aria-labelledby="overdueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="overdueModalLabel">⚠️ Overdue Book Notice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Book:</strong> {{ session('overdue_modal.book_title') }}</p>
                <p><strong>Patron:</strong> {{ session('overdue_modal.patron_name') }}</p>
                <p><strong>Days Late:</strong> {{ session('overdue_modal.days_late') }}</p>
                <p><strong>Fine:</strong> ₱{{ number_format(session('overdue_modal.fine'),2) }}</p>
                <p><em>{{ session('overdue_modal.breakdown') }}</em></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var overdueModal = new bootstrap.Modal(document.getElementById('overdueModal'));
        overdueModal.show();
    });
</script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("logTransactionForm");
        const input = document.getElementById("patron_name");
        const suggestionsBox = document.getElementById("patronSuggestions");
        const studentIdInput = document.getElementById("student_id");

        if (form) {
            form.addEventListener("submit", function (e) {
                if (!studentIdInput || !String(studentIdInput.value || "").trim()) {
                    e.preventDefault();
                    alert("Select a patron from the suggestions list (student record required).");
                }
            });
        }

        input.addEventListener("input", function () {
            studentIdInput.value = "";
            const query = this.value.trim();
            if (query.length < 1) { suggestionsBox.style.display = "none"; return; }

            fetch(`/patron-suggestions?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = "";
                    if (data.length) {
                        data.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item list-group-item-action";
                            li.textContent = item.name;
                            li.dataset.id = item.id;
                            li.addEventListener("click", function () {
                                input.value = item.name;
                                studentIdInput.value = item.id;
                                suggestionsBox.style.display = "none";
                            });
                            suggestionsBox.appendChild(li);
                        });
                        suggestionsBox.style.display = "block";
                    } else {
                        suggestionsBox.style.display = "none";
                    }
                });
        });

        document.addEventListener("click", function (e) {
            if (!suggestionsBox.contains(e.target) && e.target !== input) {
                suggestionsBox.style.display = "none";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const rfidInput = document.getElementById("rfid_input");
        const bookBox = document.getElementById("bookSuggestions");
        const patronInput = document.getElementById("patron_name");
        const studentIdInput = document.getElementById("student_id");
        const statusSelect = document.getElementById("status_select");

        rfidInput.addEventListener("input", function () {
            const query = this.value.trim();
            if (query.length < 1) { bookBox.style.display = "none"; return; }

            fetch(`/book-suggestions?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    bookBox.innerHTML = "";
                    if (data.length) {
                        data.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item list-group-item-action";
                            li.textContent = `${item.title} — ${item.author} (RFID: ${item.rfid}, Barcode: ${item.barcode})`;
                            li.addEventListener("click", function () {
                                rfidInput.value = item.rfid ?? item.barcode;
                                if ((item.availability || '') === 'Borrowed') {
                                    if (statusSelect) statusSelect.value = 'checked_in';
                                    if (patronInput) patronInput.value = item.last_patron || '';
                                    if (studentIdInput) studentIdInput.value = item.last_student_id || '';
                                } else {
                                    if (statusSelect) statusSelect.value = 'checked_out';
                                    if (studentIdInput) studentIdInput.value = '';
                                }
                                bookBox.style.display = "none";
                            });
                            bookBox.appendChild(li);
                        });
                        bookBox.style.display = "block";
                    } else { bookBox.style.display = "none"; }
                });
        });

        document.addEventListener("click", function (e) {
            if (!bookBox.contains(e.target) && e.target !== rfidInput) {
                bookBox.style.display = "none";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endsection
