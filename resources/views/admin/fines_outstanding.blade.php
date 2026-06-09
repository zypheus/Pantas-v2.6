@extends('layouts.sidebar')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="mb-0">Outstanding fines</h3>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('fines.edit') }}" class="btn btn-outline-secondary btn-sm">Fine policy</a>
            <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary btn-sm">Circulation</a>
        </div>
    </div>

    <p class="text-muted">
        Record when a patron <strong>pays</strong> or you <strong>waive</strong> a fine from a returned book. Original amounts stay in the database for auditing.
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3 border-warning">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Total still owed (this list / filters)</span>
            <span class="h5 mb-0 text-danger">₱{{ number_format($totalOutstanding, 2) }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('fines.outstanding') }}" class="row g-2 mb-4">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Search patron, student ID, book title, barcode…"
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('fines.outstanding') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Patron</th>
                    <th>Book</th>
                    <th>Returned</th>
                    <th class="text-end">Fine (₱)</th>
                    <th style="min-width: 220px;">Clear fine</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        {{ $log->patronLabel() }}
                        @if($log->student?->id_number)
                            <br><small class="text-muted">ID: {{ $log->student->id_number }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $log->book->title_statement ?? '—' }}
                        <br><small class="text-muted">{{ $log->book->barcode ?? '' }}</small>
                    </td>
                    <td>
                        {{ $log->returned_date ? $log->returned_date->timezone('Asia/Manila')->format('M j, Y g:i A') : '—' }}
                    </td>
                    <td class="text-end fw-semibold">₱{{ number_format((float) ($log->fine_balance ?? $log->fine_incurred), 2) }}</td>
                    <td>
                        <form method="POST" action="{{ route('fines.logs.clear', $log) }}" class="d-flex flex-column gap-1">
                            @csrf
                            <select name="fine_clearance_type" class="form-select form-select-sm" required>
                                <option value="paid">Paid</option>
                                <option value="waived">Waived</option>
                            </select>
                            <input type="number" name="fine_clearance_amount" class="form-control form-control-sm"
                                   step="0.01" min="0.01"
                                   max="{{ (float) ($log->fine_balance ?? $log->fine_incurred) }}"
                                   value="{{ number_format((float) ($log->fine_balance ?? $log->fine_incurred), 2, '.', '') }}"
                                   required>
                            <input type="text" name="fine_clearance_note" class="form-control form-control-sm" placeholder="Receipt #, note (optional)">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Apply this clearance amount?');">
                                Confirm
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No outstanding fines. Patrons only see amounts here until you clear them.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
