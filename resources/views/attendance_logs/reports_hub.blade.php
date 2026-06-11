@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tailwind-build.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 44px; height: 44px;">
                <i class="bi bi-clipboard-data-fill fs-5" aria-hidden="true"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Patron Attendance Reports</h4>
                <p class="mb-0 text-muted small">Choose a dashboard, export, or focused report view</p>
            </div>
        </div>
        <a href="{{ route('attendance_logs.index') }}" class="btn btn-outline btn-sm border border-secondary">
            <i class="bi bi-arrow-left-circle me-1" aria-hidden="true"></i>
            Attendance logs
        </a>
    </div>

    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
        <span>
            Summaries are built from <strong>school gate IN scans</strong>. If someone forgets to scan OUT, the system automatically closes their visit at the end of the day.
        </span>
    </div>

    <div class="card card-border bg-base-100 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-base">Report Date Range</h5>
            <form method="GET" class="d-flex flex-wrap align-items-end gap-3">
                <label class="form-control w-auto">
                    <span class="label-text mb-1">From</span>
                    <input type="date" name="from" value="{{ request('from') }}" class="input input-bordered input-sm" />
                </label>
                <label class="form-control w-auto">
                    <span class="label-text mb-1">To</span>
                    <input type="date" name="to" value="{{ request('to') }}" class="input input-bordered input-sm" />
                </label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel-fill me-1" aria-hidden="true"></i>
                        Apply
                    </button>
                    <a href="{{ route('attendance_logs.reports.hub') }}" class="btn btn-outline btn-sm border border-secondary">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                </div>
            </form>

            @if(request('from') || request('to'))
                <p class="text-muted small mt-3 mb-0">
                    Filtering all reports from <strong>{{ request('from') ?: 'start' }}</strong> to <strong>{{ request('to') ?: 'today' }}</strong>.
                </p>
            @endif
        </div>
    </div>

    <div class="grid gap-3 mb-4 md:grid-cols-2">
        <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}" class="btn btn-primary btn-lg justify-start">
            <i class="bi bi-bar-chart-line-fill me-2" aria-hidden="true"></i>
            Open full dashboard
        </a>
        <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="btn btn-success btn-lg justify-start">
            <i class="bi bi-filetype-csv me-2" aria-hidden="true"></i>
            Download combined CSV
        </a>
    </div>

    <div class="card card-border bg-base-100 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-base">Open a Single Report</h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'top-ins'])) }}" class="btn btn-outline btn-primary btn-sm">
                    <i class="bi bi-trophy-fill me-1" aria-hidden="true"></i>
                    Top INs
                </a>
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'distinct-days'])) }}" class="btn btn-outline btn-success btn-sm">
                    <i class="bi bi-calendar-check-fill me-1" aria-hidden="true"></i>
                    Distinct IN days
                </a>
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'program-totals'])) }}" class="btn btn-outline btn-warning btn-sm">
                    <i class="bi bi-diagram-3-fill me-1" aria-hidden="true"></i>
                    Program totals
                </a>
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'weekly'])) }}" class="btn btn-outline btn-info btn-sm">
                    <i class="bi bi-calendar-week-fill me-1" aria-hidden="true"></i>
                    Weekly trend
                </a>
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'monthly'])) }}" class="btn btn-outline btn-error btn-sm">
                    <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                    Monthly trend
                </a>
                <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'busiest-hour'])) }}" class="btn btn-outline btn-secondary btn-sm">
                    <i class="bi bi-clock-fill me-1" aria-hidden="true"></i>
                    Busiest hour
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
