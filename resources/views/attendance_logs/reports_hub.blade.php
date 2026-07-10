@extends('layouts.sidebar')

@section('title', 'Attendance Reports')

@section('header')
    <div class="reports-hub-header">
        <div>
            <span class="reports-hub-kicker">Attendance analytics</span>
            <h1>Patron Attendance Reports</h1>
            <p>Review attendance volume, patron frequency, absence checks, and exportable summaries.</p>
        </div>

        <a href="{{ route('attendance_logs.index') }}" class="reports-hub-header-link">
            <i class="bi bi-list-check" aria-hidden="true"></i>
            <span>Attendance logs</span>
        </a>
    </div>
@endsection

@section('content')
    @php
        $range = request()->only(['from', 'to']);
        $hasRange = request('from') || request('to');
        $focusedReports = [
            [
                'label' => 'Top INs',
                'description' => 'Patrons with the highest number of IN scans.',
                'only' => 'top-ins',
                'icon' => 'bi-trophy-fill',
                'tone' => 'blue',
            ],
            [
                'label' => 'Distinct IN Days',
                'description' => 'Patrons counted once per calendar day with an IN scan.',
                'only' => 'distinct-days',
                'icon' => 'bi-calendar-check-fill',
                'tone' => 'green',
            ],
            [
                'label' => 'Program Totals',
                'description' => 'Registered patrons, IN totals, and average use per program.',
                'only' => 'program-totals',
                'icon' => 'bi-diagram-3-fill',
                'tone' => 'amber',
            ],
            [
                'label' => 'Weekly Trend',
                'description' => 'Week-by-week IN scan movement.',
                'only' => 'weekly',
                'icon' => 'bi-calendar-week-fill',
                'tone' => 'cyan',
            ],
            [
                'label' => 'Monthly Trend',
                'description' => 'Month-by-month IN scan movement.',
                'only' => 'monthly',
                'icon' => 'bi-calendar3',
                'tone' => 'red',
            ],
            [
                'label' => 'Busiest Hour',
                'description' => 'Hours with the highest IN scan activity.',
                'only' => 'busiest-hour',
                'icon' => 'bi-clock-fill',
                'tone' => 'slate',
            ],
        ];
    @endphp

    <div class="reports-hub-shell">
        <section class="reports-hub-info">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>How reports are counted</strong>
                <p>
                    Summaries are built from school gate <strong>IN</strong> scans. If a patron forgets to scan OUT,
                    the system closes the visit at the end of that scan day.
                </p>
            </div>
        </section>

        <section class="reports-hub-filter-card">
            <div class="reports-hub-card-heading">
                <div>
                    <span>Date range</span>
                    <h2>Filter report period</h2>
                </div>
                @if ($hasRange)
                    <span class="reports-hub-range-chip">
                        {{ request('from') ?: 'Start' }} to {{ request('to') ?: 'Today' }}
                    </span>
                @endif
            </div>

            <form method="GET" class="reports-hub-filter-form">
                <label>
                    <span>From</span>
                    <input type="date" name="from" value="{{ request('from') }}">
                </label>

                <label>
                    <span>To</span>
                    <input type="date" name="to" value="{{ request('to') }}">
                </label>

                <div class="reports-hub-filter-actions">
                    <button type="submit" class="reports-hub-btn reports-hub-btn--primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        Apply filter
                    </button>
                    <a href="{{ route('attendance_logs.reports.hub') }}" class="reports-hub-btn reports-hub-btn--secondary">
                        Clear
                    </a>
                </div>
            </form>
        </section>

        <section class="reports-hub-primary-grid" aria-label="Primary report actions">
            <a href="{{ route('attendance_logs.reports.dashboard', $range) }}" class="reports-hub-primary-card reports-hub-primary-card--dashboard">
                <span class="reports-hub-primary-icon"><i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i></span>
                <span>
                    <strong>Open Full Dashboard</strong>
                    <small>Charts and tables for all report categories.</small>
                </span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>

            <a href="{{ route('attendance_logs.reports.export', $range) }}" class="reports-hub-primary-card reports-hub-primary-card--export">
                <span class="reports-hub-primary-icon"><i class="bi bi-filetype-csv" aria-hidden="true"></i></span>
                <span>
                    <strong>Download Combined CSV</strong>
                    <small>Export the filtered report pack for spreadsheet review.</small>
                </span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>

            <a href="{{ route('attendance_logs.absences') }}" class="reports-hub-primary-card reports-hub-primary-card--absence">
                <span class="reports-hub-primary-icon"><i class="bi bi-person-x-fill" aria-hidden="true"></i></span>
                <span>
                    <strong>View Daily Absences</strong>
                    <small>Find patrons without an IN scan for a selected date.</small>
                </span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </section>

        <section class="reports-hub-section">
            <div class="reports-hub-section-heading">
                <div>
                    <span>Focused reports</span>
                    <h2>Open one report view</h2>
                </div>
                <p>Use these when you only need one chart and table.</p>
            </div>

            <div class="reports-hub-report-grid">
                @foreach ($focusedReports as $report)
                    <a
                        href="{{ route('attendance_logs.reports.dashboard', array_merge($range, ['only' => $report['only']])) }}"
                        class="reports-hub-report-card reports-hub-report-card--{{ $report['tone'] }}"
                    >
                        <span class="reports-hub-report-icon">
                            <i class="bi {{ $report['icon'] }}" aria-hidden="true"></i>
                        </span>
                        <span>
                            <strong>{{ $report['label'] }}</strong>
                            <small>{{ $report['description'] }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .reports-hub-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .reports-hub-kicker,
        .reports-hub-card-heading span,
        .reports-hub-section-heading span {
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1rem;
            text-transform: uppercase;
        }

        .reports-hub-header h1,
        .reports-hub-card-heading h2,
        .reports-hub-section-heading h2 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-weight: 800;
            letter-spacing: 0;
        }

        .reports-hub-header h1 {
            font-size: 1.65rem;
            line-height: 2.1rem;
        }

        .reports-hub-header p,
        .reports-hub-section-heading p,
        .reports-hub-info p {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .reports-hub-header-link,
        .reports-hub-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 2.55rem;
            border-radius: 0.75rem;
            font-weight: 800;
            text-decoration: none;
        }

        .reports-hub-header-link {
            padding: 0.65rem 0.95rem;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .reports-hub-header-link:hover,
        .reports-hub-header-link:focus {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1e3a8a;
            text-decoration: none;
        }

        .reports-hub-shell {
            display: grid;
            gap: 1rem;
            max-width: 1180px;
            margin: 0 auto;
        }

        .reports-hub-info,
        .reports-hub-filter-card,
        .reports-hub-section {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        }

        .reports-hub-info {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 1rem;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .reports-hub-info > i {
            display: grid;
            place-items: center;
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 0.7rem;
            background: #1e3a8a;
            color: #ffffff;
            flex: 0 0 auto;
        }

        .reports-hub-info strong {
            color: #0f172a;
            font-weight: 800;
        }

        .reports-hub-filter-card,
        .reports-hub-section {
            padding: 1.15rem;
        }

        .reports-hub-card-heading,
        .reports-hub-section-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .reports-hub-card-heading h2,
        .reports-hub-section-heading h2 {
            font-size: 1.05rem;
            line-height: 1.35rem;
        }

        .reports-hub-range-chip {
            display: inline-flex;
            align-items: center;
            min-height: 2rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .reports-hub-filter-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 13rem)) minmax(16rem, 1fr);
            gap: 0.85rem;
            align-items: end;
        }

        .reports-hub-filter-form label {
            display: grid;
            gap: 0.35rem;
        }

        .reports-hub-filter-form label span {
            color: #334155;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .reports-hub-filter-form input {
            width: 100%;
            min-height: 2.6rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            background: #ffffff;
            color: #0f172a;
            padding: 0.55rem 0.75rem;
        }

        .reports-hub-filter-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .reports-hub-btn {
            border: 1px solid transparent;
            padding: 0.55rem 0.9rem;
        }

        .reports-hub-btn--primary {
            background: #1e3a8a;
            color: #ffffff;
        }

        .reports-hub-btn--primary:hover,
        .reports-hub-btn--primary:focus {
            background: #172554;
            color: #ffffff;
        }

        .reports-hub-btn--secondary {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .reports-hub-primary-grid,
        .reports-hub-report-grid {
            display: grid;
            gap: 0.85rem;
        }

        .reports-hub-primary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .reports-hub-primary-card,
        .reports-hub-report-card {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: 0;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .reports-hub-primary-card {
            min-height: 7rem;
            padding: 1rem;
        }

        .reports-hub-primary-card:hover,
        .reports-hub-primary-card:focus,
        .reports-hub-report-card:hover,
        .reports-hub-report-card:focus {
            border-color: #2563eb;
            box-shadow: 0 18px 42px rgba(37, 99, 235, 0.14);
            color: #0f172a;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .reports-hub-primary-icon,
        .reports-hub-report-icon {
            display: grid;
            place-items: center;
            border-radius: 0.9rem;
            color: #ffffff;
            flex: 0 0 auto;
        }

        .reports-hub-primary-icon {
            width: 3rem;
            height: 3rem;
            font-size: 1.35rem;
        }

        .reports-hub-primary-card > .bi-arrow-right {
            margin-left: auto;
            color: #64748b;
            flex: 0 0 auto;
        }

        .reports-hub-primary-card strong,
        .reports-hub-report-card strong {
            display: block;
            color: #0f172a;
            font-weight: 900;
            line-height: 1.2;
        }

        .reports-hub-primary-card small,
        .reports-hub-report-card small {
            display: block;
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 0.83rem;
            line-height: 1.35;
        }

        .reports-hub-primary-card--dashboard .reports-hub-primary-icon {
            background: #1e3a8a;
        }

        .reports-hub-primary-card--export .reports-hub-primary-icon {
            background: #047857;
        }

        .reports-hub-primary-card--absence .reports-hub-primary-icon {
            background: #b45309;
        }

        .reports-hub-report-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .reports-hub-report-card {
            min-height: 6.2rem;
            padding: 0.9rem;
        }

        .reports-hub-report-icon {
            width: 2.7rem;
            height: 2.7rem;
            font-size: 1.15rem;
        }

        .reports-hub-report-card--blue .reports-hub-report-icon {
            background: #2563eb;
        }

        .reports-hub-report-card--green .reports-hub-report-icon {
            background: #16a34a;
        }

        .reports-hub-report-card--amber .reports-hub-report-icon {
            background: #d97706;
        }

        .reports-hub-report-card--cyan .reports-hub-report-icon {
            background: #0891b2;
        }

        .reports-hub-report-card--red .reports-hub-report-icon {
            background: #dc2626;
        }

        .reports-hub-report-card--slate .reports-hub-report-icon {
            background: #475569;
        }

        @media (max-width: 1100px) {
            .reports-hub-primary-grid,
            .reports-hub-report-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .reports-hub-filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .reports-hub-filter-actions {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .reports-hub-primary-grid,
            .reports-hub-report-grid,
            .reports-hub-filter-form {
                grid-template-columns: 1fr;
            }

            .reports-hub-primary-card {
                align-items: flex-start;
            }
        }
    </style>
@endpush
