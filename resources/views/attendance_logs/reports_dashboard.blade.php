@extends('layouts.sidebar')

@section('title', 'Attendance Report Dashboard')

@section('header')
    <div class="reports-dashboard-header">
        <div>
            <span class="reports-dashboard-kicker">Attendance analytics</span>
            <h1>{{ ! empty($only) ? 'Focused Report Dashboard' : 'Patron Gate Report Dashboard' }}</h1>
            <p>Charts and tables based on school gate IN scans.</p>
        </div>

        <div class="reports-dashboard-header-actions">
            <a href="{{ route('attendance_logs.reports.hub', request()->only(['from','to'])) }}" class="reports-dashboard-action reports-dashboard-action--light">
                <i class="bi bi-grid" aria-hidden="true"></i>
                Reports menu
            </a>
            <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="reports-dashboard-action reports-dashboard-action--success">
                <i class="bi bi-filetype-csv" aria-hidden="true"></i>
                Export CSV
            </a>
            <a href="{{ route('attendance_logs.index') }}" class="reports-dashboard-action reports-dashboard-action--primary">
                <i class="bi bi-list-check" aria-hidden="true"></i>
                Attendance logs
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $range = request()->only(['from', 'to']);
        $hasRange = request('from') || request('to');
        $programInsTotal = collect($programAttendanceTotals ?? [])->sum(fn ($row) => (int) ($row->ins_count ?? 0));
        $registeredPatrons = collect($programAttendanceTotals ?? [])->sum(fn ($row) => (int) ($row->student_count ?? 0));
        $topPatron = collect($topStudentsByIns ?? [])->first();
        $busiestHour = collect($busiestHours ?? [])->first();
    @endphp

    <div class="reports-dashboard-shell">
        <section class="reports-dashboard-note">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Counting rules</strong>
                <p>
                    Distinct days count at most one IN per patron per calendar date. Auto OUT closes open visits at
                    the end of the scan day when a patron forgets to scan OUT.
                </p>
            </div>
        </section>

        <section class="reports-dashboard-toolbar">
            <div>
                <span class="reports-dashboard-kicker">Date range</span>
                <h2>{{ $hasRange ? (request('from') ?: 'Start').' to '.(request('to') ?: 'Today') : 'All available scans' }}</h2>
            </div>

            <form method="GET" class="reports-dashboard-filter">
                @if (! empty($only))
                    <input type="hidden" name="only" value="{{ $only }}">
                @endif
                <label>
                    <span>From</span>
                    <input type="date" name="from" value="{{ request('from') }}">
                </label>
                <label>
                    <span>To</span>
                    <input type="date" name="to" value="{{ request('to') }}">
                </label>
                <button type="submit" class="reports-dashboard-filter-submit">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    Apply
                </button>
                <a href="{{ route('attendance_logs.reports.dashboard', ! empty($only) ? ['only' => $only] : []) }}" class="reports-dashboard-filter-clear">
                    Clear
                </a>
            </form>
        </section>

        <section class="reports-dashboard-summary" aria-label="Report summary">
            <div class="reports-dashboard-summary-card reports-dashboard-summary-card--blue">
                <span><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span>
                <div>
                    <small>IN scans</small>
                    <strong>{{ number_format($programInsTotal) }}</strong>
                </div>
            </div>

            <div class="reports-dashboard-summary-card reports-dashboard-summary-card--green">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                <div>
                    <small>Registered patrons</small>
                    <strong>{{ number_format($registeredPatrons) }}</strong>
                </div>
            </div>

            <div class="reports-dashboard-summary-card reports-dashboard-summary-card--amber">
                <span><i class="bi bi-trophy-fill" aria-hidden="true"></i></span>
                <div>
                    <small>Top patron</small>
                    <strong>{{ $topPatron ? number_format((int) $topPatron->ins_count).' INs' : 'No data' }}</strong>
                </div>
            </div>

            <div class="reports-dashboard-summary-card reports-dashboard-summary-card--slate">
                <span><i class="bi bi-clock-fill" aria-hidden="true"></i></span>
                <div>
                    <small>Busiest hour</small>
                    <strong>{{ $busiestHour && (int) $busiestHour->count > 0 ? $busiestHour->label : 'No data' }}</strong>
                </div>
            </div>
        </section>

        @if(!empty($only))
            <div class="reports-dashboard-focused-bar">
                <span>
                    <i class="bi bi-pin-angle-fill" aria-hidden="true"></i>
                    Showing one focused report
                </span>
                <a href="{{ route('attendance_logs.reports.dashboard', $range) }}">
                    Open full dashboard
                </a>
            </div>
        @endif

        <div class="reports-dashboard-body">
            @include('attendance_logs.partials.patron_reports_body', ['only' => $only ?? null])
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .reports-dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .reports-dashboard-kicker {
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1rem;
            text-transform: uppercase;
        }

        .reports-dashboard-header h1 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 2.1rem;
            letter-spacing: 0;
        }

        .reports-dashboard-header p {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .reports-dashboard-header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            justify-content: flex-end;
        }

        .reports-dashboard-action,
        .reports-dashboard-filter-submit,
        .reports-dashboard-filter-clear,
        .reports-dashboard-focused-bar a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 2.55rem;
            border-radius: 0.75rem;
            font-weight: 800;
            text-decoration: none;
            padding: 0.55rem 0.9rem;
        }

        .reports-dashboard-action--light,
        .reports-dashboard-filter-clear {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .reports-dashboard-action--primary,
        .reports-dashboard-filter-submit {
            border: 1px solid #1e3a8a;
            background: #1e3a8a;
            color: #ffffff;
        }

        .reports-dashboard-action--success {
            border: 1px solid #047857;
            background: #047857;
            color: #ffffff;
        }

        .reports-dashboard-action:hover,
        .reports-dashboard-action:focus,
        .reports-dashboard-filter-clear:hover,
        .reports-dashboard-filter-clear:focus,
        .reports-dashboard-focused-bar a:hover,
        .reports-dashboard-focused-bar a:focus {
            color: #1e3a8a;
            border-color: #2563eb;
            background: #eff6ff;
            text-decoration: none;
        }

        .reports-dashboard-action--primary:hover,
        .reports-dashboard-action--primary:focus,
        .reports-dashboard-filter-submit:hover,
        .reports-dashboard-filter-submit:focus {
            color: #ffffff;
            background: #172554;
            border-color: #172554;
        }

        .reports-dashboard-action--success:hover,
        .reports-dashboard-action--success:focus {
            color: #ffffff;
            background: #065f46;
            border-color: #065f46;
        }

        .reports-dashboard-shell {
            display: grid;
            gap: 1rem;
            max-width: 1240px;
            margin: 0 auto;
        }

        .reports-dashboard-note,
        .reports-dashboard-toolbar,
        .reports-dashboard-focused-bar {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        }

        .reports-dashboard-note {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 1rem;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .reports-dashboard-note > i {
            display: grid;
            place-items: center;
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 0.7rem;
            background: #1e3a8a;
            color: #ffffff;
            flex: 0 0 auto;
        }

        .reports-dashboard-note strong {
            color: #0f172a;
            font-weight: 800;
        }

        .reports-dashboard-note p {
            margin: 0.25rem 0 0;
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .reports-dashboard-toolbar {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1.1rem;
        }

        .reports-dashboard-toolbar h2 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.35rem;
        }

        .reports-dashboard-filter {
            display: flex;
            align-items: end;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .reports-dashboard-filter label {
            display: grid;
            gap: 0.3rem;
        }

        .reports-dashboard-filter label span {
            color: #334155;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .reports-dashboard-filter input {
            min-height: 2.55rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            background: #ffffff;
            color: #0f172a;
            padding: 0.5rem 0.7rem;
        }

        .reports-dashboard-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .reports-dashboard-summary-card {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-height: 6rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        }

        .reports-dashboard-summary-card > span {
            display: grid;
            place-items: center;
            width: 2.9rem;
            height: 2.9rem;
            border-radius: 0.9rem;
            color: #ffffff;
            font-size: 1.25rem;
            flex: 0 0 auto;
        }

        .reports-dashboard-summary-card small {
            display: block;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .reports-dashboard-summary-card strong {
            display: block;
            margin-top: 0.2rem;
            color: #0f172a;
            font-size: 1.2rem;
            font-weight: 900;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .reports-dashboard-summary-card--blue > span {
            background: #2563eb;
        }

        .reports-dashboard-summary-card--green > span {
            background: #16a34a;
        }

        .reports-dashboard-summary-card--amber > span {
            background: #d97706;
        }

        .reports-dashboard-summary-card--slate > span {
            background: #475569;
        }

        .reports-dashboard-focused-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.85rem 1rem;
        }

        .reports-dashboard-focused-bar span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #334155;
            font-weight: 800;
        }

        .reports-dashboard-focused-bar a {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .reports-dashboard-body .row {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }

        .reports-dashboard-body .card {
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08) !important;
        }

        .reports-dashboard-body .card-header {
            min-height: 3rem;
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem !important;
            font-size: 0.88rem !important;
            font-weight: 900 !important;
            letter-spacing: 0.01em;
        }

        .reports-dashboard-body .card-body > .p-3.border-bottom {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom-color: #e2e8f0 !important;
        }

        .reports-dashboard-body table {
            margin-bottom: 0;
            color: #0f172a;
        }

        .reports-dashboard-body table thead th {
            color: #475569;
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .reports-dashboard-body table tbody td {
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .reports-dashboard-body .table-zebra tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .reports-dashboard-body .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.4rem;
            border-radius: 999px;
            padding: 0.32rem 0.55rem;
            font-size: 0.74rem;
            font-weight: 900;
        }

        .reports-dashboard-body .badge-neutral,
        .reports-dashboard-body .badge-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }

        .reports-dashboard-body .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .reports-dashboard-body .badge-info {
            background: #cffafe;
            color: #155e75;
        }

        .reports-dashboard-body .badge-error {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 1100px) {
            .reports-dashboard-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .reports-dashboard-summary {
                grid-template-columns: 1fr;
            }

            .reports-dashboard-filter,
            .reports-dashboard-filter label,
            .reports-dashboard-filter input,
            .reports-dashboard-filter-submit,
            .reports-dashboard-filter-clear,
            .reports-dashboard-header-actions,
            .reports-dashboard-action {
                width: 100%;
            }
        }
    </style>
@endpush
