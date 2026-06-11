@extends('layouts.sidebar')

@section('styles')
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 44px; height: 44px;">
                <i class="bi bi-bar-chart-line-fill fs-5" aria-hidden="true"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Patron Attendance Reports</h4>
                <p class="mb-0 text-muted small">Gate scan analytics and attendance trends</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('attendance_logs.reports.hub', request()->only(['from','to'])) }}" class="btn btn-outline btn-sm border border-secondary">
                <i class="bi bi-grid-3x3-gap-fill me-1" aria-hidden="true"></i>
                Reports menu
            </a>
            <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="btn btn-success btn-sm">
                <i class="bi bi-filetype-csv me-1" aria-hidden="true"></i>
                Export CSV
            </a>
            <a href="{{ route('attendance_logs.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                Attendance logs
            </a>
        </div>
    </div>

    <p class="text-muted small mb-3">
        Based on <strong>School gate IN scans</strong>. <strong>Distinct days with IN</strong> counts at most <strong>one calendar day per patron</strong> per distinct date, even with multiple INs that day.
    </p>
    <p class="text-muted small mb-4">
        <strong>Auto OUT:</strong> If a patron is still <strong>IN</strong> after their scan day, an <strong>OUT</strong> is recorded at <strong>end of that day</strong> so each visit is properly closed.
    </p>

    @if(!empty($only))
        <div class="mb-3">
            <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}" class="btn btn-outline btn-primary btn-sm">
                <i class="bi bi-layout-three-columns me-1" aria-hidden="true"></i>
                Open full dashboard
            </a>
        </div>
    @endif

    @include('attendance_logs.partials.patron_reports_body', ['only' => $only ?? null])
</div>
@endsection
