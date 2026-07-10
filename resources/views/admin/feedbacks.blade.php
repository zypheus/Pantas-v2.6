@extends('layouts.sidebar')

@section('title', 'Attendance Feedback Report')

@php
    $active = request('rating');
    $activeLabel = $active ? str_replace('_', ' ', $active) : null;
    $ratedTotal = max($total - $declined, 0);
    $positiveTotal = $excellent + $good;
    $attentionTotal = $medium + $poor + $veryBad;
    $positivePercent = $total > 0 ? round(($positiveTotal / $total) * 100) : 0;
    $shownCount = $feedbacks->count();
    $ratings = [
        [
            'key' => 'excellent',
            'label' => 'Excellent',
            'count' => $excellent,
            'tone' => 'green',
            'icon' => 'bi-emoji-smile-fill',
            'desc' => 'Highest satisfaction',
        ],
        [
            'key' => 'good',
            'label' => 'Good',
            'count' => $good,
            'tone' => 'blue',
            'icon' => 'bi-hand-thumbs-up-fill',
            'desc' => 'Positive experience',
        ],
        [
            'key' => 'medium',
            'label' => 'Medium',
            'count' => $medium,
            'tone' => 'amber',
            'icon' => 'bi-dash-circle-fill',
            'desc' => 'Needs attention',
        ],
        [
            'key' => 'poor',
            'label' => 'Poor',
            'count' => $poor,
            'tone' => 'red',
            'icon' => 'bi-emoji-frown-fill',
            'desc' => 'Negative experience',
        ],
        [
            'key' => 'very_bad',
            'label' => 'Very Bad',
            'count' => $veryBad,
            'tone' => 'slate',
            'icon' => 'bi-exclamation-octagon-fill',
            'desc' => 'Critical response',
        ],
        [
            'key' => 'declined',
            'label' => 'Declined',
            'count' => $declined,
            'tone' => 'gray',
            'icon' => 'bi-slash-circle-fill',
            'desc' => 'No rating given',
        ],
    ];
@endphp

@section('header')
    <div class="feedback-admin-header">
        <div>
            <span class="feedback-admin-kicker">Attendance feedback</span>
            <h1>Feedback Responses</h1>
            <p>Review logout feedback from attendance scanner sessions.</p>
            <div class="feedback-admin-header-meta" aria-label="Current feedback view">
                <span><i class="bi bi-funnel" aria-hidden="true"></i>{{ $activeLabel ? ucwords($activeLabel) : 'All ratings' }}</span>
                <span><i class="bi bi-list-check" aria-hidden="true"></i>{{ number_format($shownCount) }} records shown</span>
                <span><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ number_format($ratedTotal) }} rated</span>
            </div>
        </div>

        <div class="feedback-admin-actions">
            <a href="{{ route('attendance.feedback.settings') }}" class="feedback-admin-action feedback-admin-action--light">
                <i class="bi bi-gear" aria-hidden="true"></i>
                Settings
            </a>
            <a href="{{ route('attendance.scan') }}" class="feedback-admin-action feedback-admin-action--primary" target="_blank" rel="noopener">
                <i class="bi bi-upc-scan" aria-hidden="true"></i>
                Open scanner
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="feedback-admin-shell">
        <section class="feedback-admin-summary" aria-label="Feedback summary">
            <a href="{{ route('admin.attendance.feedbacks') }}" class="feedback-admin-stat {{ !$active ? 'is-active' : '' }}">
                <span class="feedback-admin-stat-icon feedback-admin-stat-icon--blue">
                    <i class="bi bi-chat-square-text-fill" aria-hidden="true"></i>
                </span>
                <span>
                    <small>Total responses</small>
                    <strong>{{ number_format($total) }}</strong>
                    <em>Click to reset filters</em>
                </span>
            </a>

            <div class="feedback-admin-stat">
                <span class="feedback-admin-stat-icon feedback-admin-stat-icon--green">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                </span>
                <span>
                    <small>Positive responses</small>
                    <strong>{{ number_format($positiveTotal) }}</strong>
                    <em>{{ $positivePercent }}% of all responses</em>
                </span>
            </div>

            <div class="feedback-admin-stat">
                <span class="feedback-admin-stat-icon feedback-admin-stat-icon--amber">
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                </span>
                <span>
                    <small>Needs attention</small>
                    <strong>{{ number_format($attentionTotal) }}</strong>
                    <em>Medium, poor, and very bad</em>
                </span>
            </div>

            <div class="feedback-admin-stat">
                <span class="feedback-admin-stat-icon feedback-admin-stat-icon--slate">
                    <i class="bi bi-clipboard2-check-fill" aria-hidden="true"></i>
                </span>
                <span>
                    <small>Rated responses</small>
                    <strong>{{ number_format($ratedTotal) }}</strong>
                    <em>{{ number_format($declined) }} declined feedback prompt</em>
                </span>
            </div>
        </section>

        <section class="feedback-admin-panel">
            <div class="feedback-admin-panel-heading">
                <div>
                    <span class="feedback-admin-kicker">Rating filters</span>
                    <h2>Filter by response</h2>
                </div>
                @if($active)
                    <a href="{{ route('admin.attendance.feedbacks') }}" class="feedback-admin-clear">
                        Clear filter
                    </a>
                @endif
            </div>

            <div class="feedback-admin-rating-grid">
                @foreach($ratings as $rating)
                    @php
                        $isActive = $active === $rating['key'];
                        $percent = $total > 0 ? round(($rating['count'] / $total) * 100) : 0;
                    @endphp
                    <a href="{{ route('admin.attendance.feedbacks', ['rating' => $rating['key']]) }}"
                       class="feedback-admin-rating feedback-admin-rating--{{ $rating['tone'] }} {{ $isActive ? 'is-active' : '' }}">
                        <span class="feedback-admin-rating-icon">
                            <i class="bi {{ $rating['icon'] }}" aria-hidden="true"></i>
                        </span>
                        <span class="feedback-admin-rating-copy">
                            <strong>{{ $rating['label'] }}</strong>
                            <small>{{ $rating['desc'] }}</small>
                        </span>
                        <span class="feedback-admin-rating-count">
                            {{ number_format($rating['count']) }}
                            <small>{{ $percent }}%</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        @if($total > 0)
            <section class="feedback-admin-panel">
                <div class="feedback-admin-panel-heading">
                    <div>
                        <span class="feedback-admin-kicker">Distribution</span>
                        <h2>Overall response mix</h2>
                    </div>
                    <span class="feedback-admin-total-chip">{{ number_format($total) }} total</span>
                </div>

                <div class="feedback-admin-distribution">
                    @foreach($ratings as $rating)
                        @php
                            $percent = $total > 0 ? round(($rating['count'] / $total) * 100) : 0;
                        @endphp
                        <div class="feedback-admin-bar-row feedback-admin-bar-row--{{ $rating['tone'] }}">
                            <div class="feedback-admin-bar-label">
                                <span>{{ $rating['label'] }}</span>
                                <strong>{{ number_format($rating['count']) }} responses &middot; {{ $percent }}%</strong>
                            </div>
                            <div class="feedback-admin-bar-track" aria-hidden="true">
                                <span style="width: {{ $percent }}%"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="feedback-admin-panel">
            <div class="feedback-admin-panel-heading">
                <div>
                    <span class="feedback-admin-kicker">Responses</span>
                    <h2>{{ $activeLabel ? ucwords($activeLabel) . ' Responses' : 'All Responses' }}</h2>
                    <p>Latest submissions appear first.</p>
                </div>
            </div>

            <div class="feedback-admin-table-wrap">
                <table class="feedback-admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Rating</th>
                            <th>Declined</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($feedbacks as $index => $feedback)
                            @php
                                $ratingKey = $feedback->declined ? 'declined' : $feedback->rating;
                                $ratingMeta = collect($ratings)->firstWhere('key', $ratingKey);
                                $studentName = trim((optional($feedback->student)->lastname ?? '') . ', ' . (optional($feedback->student)->firstname ?? ''), ' ,');
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $studentName !== '' ? $studentName : 'Unknown student' }}</strong>
                                </td>
                                <td>
                                    @if($feedback->declined)
                                        <span class="feedback-admin-badge feedback-admin-badge--gray">Declined</span>
                                    @elseif($feedback->rating)
                                        <span class="feedback-admin-badge feedback-admin-badge--{{ $ratingMeta['tone'] ?? 'gray' }}">
                                            {{ ucwords(str_replace('_', ' ', $feedback->rating)) }}
                                        </span>
                                    @else
                                        <span class="feedback-admin-badge feedback-admin-badge--gray">No rating</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="feedback-admin-badge {{ $feedback->declined ? 'feedback-admin-badge--gray' : 'feedback-admin-badge--light' }}">
                                        {{ $feedback->declined ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $feedback->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="feedback-admin-empty">
                                        <i class="bi bi-inbox" aria-hidden="true"></i>
                                        <strong>No feedback found</strong>
                                        <span>Feedback appears here after patrons respond at scanner logout.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .feedback-admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .feedback-admin-kicker {
            color: #71717a;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1rem;
            text-transform: uppercase;
        }

        .feedback-admin-header h1,
        .feedback-admin-panel-heading h2 {
            margin: 0.2rem 0 0;
            color: #18181b;
            font-weight: 800;
            letter-spacing: 0;
        }

        .feedback-admin-header h1 {
            font-size: 1.65rem;
            line-height: 2.1rem;
        }

        .feedback-admin-header p,
        .feedback-admin-panel-heading p {
            margin: 0.35rem 0 0;
            color: #71717a;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .feedback-admin-header-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-top: 0.75rem;
        }

        .feedback-admin-header-meta span {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 2rem;
            padding: 0.35rem 0.65rem;
            border: 1px solid #e4e4e7;
            border-radius: 999px;
            background: #ffffff;
            color: #52525b;
            font-size: 0.78rem;
            font-weight: 800;
            line-height: 1;
        }

        .feedback-admin-actions {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .feedback-admin-action,
        .feedback-admin-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 2.4rem;
            padding: 0.5rem 0.85rem;
            border-radius: 0.5rem;
            font-weight: 800;
            text-decoration: none;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .feedback-admin-action--light,
        .feedback-admin-clear {
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #18181b;
        }

        .feedback-admin-action--primary {
            border: 1px solid #18181b;
            background: #18181b;
            color: #ffffff;
        }

        .feedback-admin-action:hover,
        .feedback-admin-action:focus,
        .feedback-admin-clear:hover,
        .feedback-admin-clear:focus {
            border-color: #d4d4d8;
            background: #f4f4f5;
            color: #18181b;
            text-decoration: none;
        }

        .feedback-admin-action--primary:hover,
        .feedback-admin-action--primary:focus {
            border-color: #27272a;
            background: #27272a;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(24, 24, 27, 0.18);
        }

        .feedback-admin-shell {
            display: grid;
            gap: 1rem;
            max-width: 1240px;
            margin: 0 auto;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .feedback-admin-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .feedback-admin-stat,
        .feedback-admin-panel {
            border: 1px solid #e4e4e7;
            border-radius: 0.5rem;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(24, 24, 27, 0.06);
        }

        .feedback-admin-stat {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-height: 6rem;
            padding: 1rem;
            color: #18181b;
            text-decoration: none;
        }

        .feedback-admin-stat:hover,
        .feedback-admin-stat:focus {
            border-color: #d4d4d8;
            color: #18181b;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(24, 24, 27, 0.08);
        }

        .feedback-admin-stat.is-active {
            border-color: #18181b;
            box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.08);
        }

        .feedback-admin-stat-icon {
            display: grid;
            place-items: center;
            width: 2.9rem;
            height: 2.9rem;
            border-radius: 0.5rem;
            color: #ffffff;
            font-size: 1.2rem;
            flex: 0 0 auto;
        }

        .feedback-admin-stat-icon--blue { background: #2563eb; }
        .feedback-admin-stat-icon--green { background: #16a34a; }
        .feedback-admin-stat-icon--amber { background: #d97706; }
        .feedback-admin-stat-icon--slate { background: #475569; }

        .feedback-admin-stat small,
        .feedback-admin-stat em {
            display: block;
            color: #71717a;
            font-size: 0.78rem;
            font-style: normal;
            font-weight: 800;
        }

        .feedback-admin-stat strong {
            display: block;
            margin-top: 0.2rem;
            color: #18181b;
            font-size: 1.25rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .feedback-admin-stat em {
            margin-top: 0.15rem;
            font-weight: 700;
        }

        .feedback-admin-panel {
            padding: 1.15rem;
        }

        .feedback-admin-panel-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .feedback-admin-panel-heading h2 {
            font-size: 1.05rem;
            line-height: 1.35rem;
        }

        .feedback-admin-total-chip {
            display: inline-flex;
            align-items: center;
            min-height: 2rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            border: 1px solid #e4e4e7;
            background: #f4f4f5;
            color: #18181b;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .feedback-admin-rating-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .feedback-admin-rating {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 0.75rem;
            min-height: 5.8rem;
            padding: 0.9rem;
            border: 1px solid #e4e4e7;
            border-radius: 0.5rem;
            background: #ffffff;
            color: #18181b;
            text-decoration: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .feedback-admin-rating:hover,
        .feedback-admin-rating:focus,
        .feedback-admin-rating.is-active {
            border-color: #18181b;
            color: #18181b;
            text-decoration: none;
            box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.08);
            transform: translateY(-2px);
        }

        .feedback-admin-rating.is-active::after {
            content: "";
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 999px;
            background: #18181b;
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
        }

        .feedback-admin-rating {
            position: relative;
        }

        .feedback-admin-rating-icon {
            display: grid;
            place-items: center;
            width: 2.7rem;
            height: 2.7rem;
            border-radius: 0.5rem;
            color: #ffffff;
            font-size: 1.15rem;
        }

        .feedback-admin-rating-copy strong,
        .feedback-admin-rating-count {
            display: block;
            color: #18181b;
            font-weight: 900;
        }

        .feedback-admin-rating-copy small,
        .feedback-admin-rating-count small {
            display: block;
            margin-top: 0.2rem;
            color: #71717a;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .feedback-admin-rating-count {
            text-align: right;
            font-size: 1.25rem;
        }

        .feedback-admin-rating--green .feedback-admin-rating-icon,
        .feedback-admin-badge--green { background: #16a34a; }
        .feedback-admin-rating--blue .feedback-admin-rating-icon,
        .feedback-admin-badge--blue { background: #2563eb; }
        .feedback-admin-rating--amber .feedback-admin-rating-icon,
        .feedback-admin-badge--amber { background: #d97706; }
        .feedback-admin-rating--red .feedback-admin-rating-icon,
        .feedback-admin-badge--red { background: #dc2626; }
        .feedback-admin-rating--slate .feedback-admin-rating-icon,
        .feedback-admin-badge--slate { background: #475569; }
        .feedback-admin-rating--gray .feedback-admin-rating-icon,
        .feedback-admin-badge--gray { background: #64748b; }

        .feedback-admin-distribution {
            display: grid;
            gap: 0.9rem;
        }

        .feedback-admin-bar-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.4rem;
            color: #18181b;
            font-size: 0.88rem;
            font-weight: 800;
        }

        .feedback-admin-bar-label strong {
            color: #71717a;
            font-size: 0.78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .feedback-admin-bar-track {
            height: 0.75rem;
            overflow: hidden;
            border-radius: 999px;
            background: #f4f4f5;
        }

        .feedback-admin-bar-track span {
            display: block;
            height: 100%;
            min-width: 0.25rem;
            border-radius: inherit;
        }

        .feedback-admin-bar-row--green .feedback-admin-bar-track span { background: #16a34a; }
        .feedback-admin-bar-row--blue .feedback-admin-bar-track span { background: #2563eb; }
        .feedback-admin-bar-row--amber .feedback-admin-bar-track span { background: #d97706; }
        .feedback-admin-bar-row--red .feedback-admin-bar-track span { background: #dc2626; }
        .feedback-admin-bar-row--slate .feedback-admin-bar-track span { background: #475569; }
        .feedback-admin-bar-row--gray .feedback-admin-bar-track span { background: #64748b; }

        .feedback-admin-table-wrap {
            overflow-x: auto;
            border: 1px solid #e4e4e7;
            border-radius: 0.5rem;
        }

        .feedback-admin-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            color: #18181b;
            font-size: 0.9rem;
        }

        .feedback-admin-table th {
            padding: 0.85rem 0.9rem;
            color: #71717a;
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.03em;
            text-align: left;
            text-transform: uppercase;
            background: #fafafa;
            border-bottom: 1px solid #e4e4e7;
        }

        .feedback-admin-table td {
            padding: 0.85rem 0.9rem;
            border-bottom: 1px solid #f4f4f5;
            vertical-align: middle;
        }

        .feedback-admin-table tr:nth-child(even) td {
            background: #fafafa;
        }

        .feedback-admin-table tbody tr:hover td {
            background: #f4f4f5;
        }

        .feedback-admin-table tr:last-child td {
            border-bottom: 0;
        }

        .feedback-admin-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 1.75rem;
            padding: 0.28rem 0.6rem;
            border-radius: 0.375rem;
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .feedback-admin-badge--light {
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #52525b;
        }

        .feedback-admin-empty {
            display: grid;
            justify-items: center;
            gap: 0.35rem;
            padding: 2.5rem 1rem;
            color: #71717a;
            text-align: center;
        }

        .feedback-admin-empty i {
            font-size: 2rem;
            color: #a1a1aa;
        }

        .feedback-admin-empty strong {
            color: #18181b;
            font-weight: 900;
        }

        @media (max-width: 1100px) {
            .feedback-admin-summary,
            .feedback-admin-rating-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .feedback-admin-summary,
            .feedback-admin-rating-grid {
                grid-template-columns: 1fr;
            }

            .feedback-admin-actions,
            .feedback-admin-action {
                width: 100%;
            }

            .feedback-admin-bar-label {
                align-items: flex-start;
                flex-direction: column;
                gap: 0.2rem;
            }
        }
    </style>
@endpush
