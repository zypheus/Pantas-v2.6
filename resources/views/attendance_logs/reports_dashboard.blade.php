@extends('layouts.sec')

@section('styles')
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h4 class="mb-0">Patron gate — report dashboard</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance_logs.reports.hub', request()->only(['from','to'])) }}" class="btn btn-outline-secondary btn-sm">Reports menu</a>
            <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="btn btn-success btn-sm">Export CSV</a>
            <a href="{{ route('attendance_logs.index') }}" class="btn btn-primary btn-sm">Attendance logs</a>
        </div>
    </div>
    <p class="text-muted small mb-3">
        Based on <strong>School gate IN scans</strong>. “Distinct days with IN” counts at most <strong>one calendar day per patron</strong> per distinct date, even with multiple INs that day.
    </p>
    <p class="text-muted small mb-4">
        <strong>Auto OUT:</strong> If a patron is still <strong>IN</strong> after their scan day, an <strong>OUT</strong> is recorded at <strong>end of that day</strong> so each visit is properly closed.
    </p>

    @if(!empty($only))
        <div class="mb-3">
            <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}" class="btn btn-outline-primary btn-sm">Open full dashboard</a>
        </div>
    @endif

    @include('attendance_logs.partials.patron_reports_body', ['only' => $only ?? null])
</div>
@endsection
