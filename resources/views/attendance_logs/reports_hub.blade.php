@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tailwind-build.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_logs/index.css') }}">
@endsection

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Patron gate — reports</h4>
    <p class="text-muted small mb-4">
        Summaries are built from <strong>school gate IN scans</strong>. If someone forgets to scan OUT, the system automatically closes their visit at the end of the day.
    </p>

    <div class="mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border px-3 py-2" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="export-btn" style="font-size: 0.9rem;">Apply</button>
                <a href="{{ route('attendance_logs.reports.hub') }}" class="export-btn" style="font-size: 0.9rem;">Clear</a>
            </div>
        </form>
        @if(request('from') || request('to'))
            <p class="text-muted small mt-2 mb-0">
                Filtering all reports to: <strong>{{ request('from') ?: '…' }}</strong> → <strong>{{ request('to') ?: '…' }}</strong>
            </p>
        @endif
    </div>

    <div class="flex flex-col gap-3 mb-4">
        <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}" class="export-btn text-center">
            📊 Open full dashboard (all tables on one page)
        </a>
        <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="export-btn text-center">
            📥 Download combined CSV export
        </a>
    </div>

    <p class="small text-gray-600 mb-2">Open a single report:</p>
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'top-ins'])) }}" class="export-btn" style="font-size: 0.85rem;">Top INs</a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'distinct-days'])) }}" class="export-btn" style="font-size: 0.85rem;">Distinct IN days</a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'program-totals'])) }}" class="export-btn" style="font-size: 0.85rem;">Program totals</a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'weekly'])) }}" class="export-btn" style="font-size: 0.85rem;">Weekly trend</a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'monthly'])) }}" class="export-btn" style="font-size: 0.85rem;">Monthly trend</a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'busiest-hour'])) }}" class="export-btn" style="font-size: 0.85rem;">Busiest hour</a>
    </div>

    <a href="{{ route('attendance_logs.index') }}" class="export-btn">← Back to attendance logs</a>
</div>
@endsection
