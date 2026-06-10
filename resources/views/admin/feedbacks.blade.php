@extends('layouts.sidebar')

@section('title', 'Attendance Feedback Report')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .attendance-feedback-report,
        .attendance-feedback-report .card,
        .attendance-feedback-report .stats,
        .attendance-feedback-report .table {
            color: var(--color-base-content) !important;
        }

        .attendance-feedback-report .table th,
        .attendance-feedback-report .table td {
            color: var(--color-base-content) !important;
        }

        .attendance-feedback-report .badge {
            border-color: color-mix(in oklch, currentColor 28%, transparent) !important;
            color: var(--color-base-content) !important;
            font-weight: 700;
            text-decoration: none !important;
        }

        .attendance-feedback-report .badge-primary {
            background-color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
            color: var(--color-primary-content) !important;
        }

        .attendance-feedback-report .badge-success {
            background-color: var(--color-success) !important;
            border-color: var(--color-success) !important;
            color: var(--color-success-content) !important;
        }

        .attendance-feedback-report .badge-info {
            background-color: var(--color-info) !important;
            border-color: var(--color-info) !important;
            color: var(--color-info-content) !important;
        }

        .attendance-feedback-report .badge-warning {
            background-color: var(--color-warning) !important;
            border-color: var(--color-warning) !important;
            color: var(--color-warning-content) !important;
        }

        .attendance-feedback-report .badge-error {
            background-color: var(--color-error) !important;
            border-color: var(--color-error) !important;
            color: var(--color-error-content) !important;
        }

        .attendance-feedback-report .badge-neutral {
            background-color: var(--color-neutral) !important;
            border-color: var(--color-neutral) !important;
            color: var(--color-neutral-content) !important;
        }

        .attendance-feedback-report .badge-secondary {
            background-color: var(--color-secondary) !important;
            border-color: var(--color-secondary) !important;
            color: var(--color-secondary-content) !important;
        }

        .attendance-feedback-report .badge-ghost {
            background-color: var(--color-base-200) !important;
            border-color: var(--color-base-300) !important;
            color: var(--color-base-content) !important;
        }

        .attendance-feedback-report a,
        .attendance-feedback-report a:hover,
        .attendance-feedback-report a:focus {
            text-decoration: none !important;
        }

        .attendance-feedback-report .text-muted,
        .attendance-feedback-report .stat-title,
        .attendance-feedback-report .stat-desc {
            color: color-mix(in oklch, var(--color-base-content) 70%, transparent) !important;
        }
    </style>
@endpush

@php
    $active = request('rating');
    $activeLabel = $active ? str_replace('_', ' ', $active) : null;
    $ratings = [
        [
            'key' => 'excellent',
            'label' => 'Excellent',
            'count' => $excellent,
            'badge' => 'badge-success',
            'progress' => 'progress-success',
            'desc' => 'Highest satisfaction',
        ],
        [
            'key' => 'good',
            'label' => 'Good',
            'count' => $good,
            'badge' => 'badge-info',
            'progress' => 'progress-info',
            'desc' => 'Positive experience',
        ],
        [
            'key' => 'medium',
            'label' => 'Medium',
            'count' => $medium,
            'badge' => 'badge-warning',
            'progress' => 'progress-warning',
            'desc' => 'Needs attention',
        ],
        [
            'key' => 'poor',
            'label' => 'Poor',
            'count' => $poor,
            'badge' => 'badge-error',
            'progress' => 'progress-error',
            'desc' => 'Negative experience',
        ],
        [
            'key' => 'very_bad',
            'label' => 'Very Bad',
            'count' => $veryBad,
            'badge' => 'badge-neutral',
            'progress' => 'progress-neutral',
            'desc' => 'Critical response',
        ],
        [
            'key' => 'declined',
            'label' => 'Declined',
            'count' => $declined,
            'badge' => 'badge-secondary',
            'progress' => 'progress-secondary',
            'desc' => 'No rating given',
        ],
    ];
@endphp

@section('content')
    <div data-theme="light" class="attendance-feedback-report flex w-full flex-col gap-6 bg-base-200 p-0 text-base-content">
        <header class="flex flex-col gap-4 rounded-box bg-base-100 p-5 text-base-content shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="badge badge-primary">Admin report</div>
                <div>
                    <h1 class="text-2xl font-bold tracking-normal sm:text-3xl">Attendance Feedback Report</h1>
                    <p class="mt-1 max-w-2xl text-sm text-base-content/70">
                        Review logout feedback responses and filter the report by rating.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($active)
                    <a href="{{ route('admin.attendance.feedbacks') }}" class="btn btn-ghost no-underline">
                        Clear filter
                    </a>
                @endif
                <a href="{{ route('book.index') }}" class="btn btn-primary no-underline">
                    Home
                </a>
            </div>
        </header>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="stats stats-vertical w-full bg-base-100 text-base-content shadow-sm sm:stats-horizontal">
                <a href="{{ route('admin.attendance.feedbacks') }}" class="stat no-underline hover:bg-base-200 {{ !$active ? 'bg-base-200' : '' }}">
                    <div class="stat-title">Total responses</div>
                    <div class="stat-value text-primary">{{ $total }}</div>
                    <div class="stat-desc">Click to reset filters</div>
                </a>

                <div class="stat">
                    <div class="stat-title">Current view</div>
                    <div class="stat-value text-base-content text-lg sm:text-xl">
                        {{ $activeLabel ? 'Filtered: ' . ucwords($activeLabel) : 'All responses' }}
                    </div>
                    <div class="stat-desc">{{ $feedbacks->count() }} shown in table</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Rated responses</div>
                    <div class="stat-value text-lg sm:text-xl">{{ max($total - $declined, 0) }}</div>
                    <div class="stat-desc">{{ $declined }} declined</div>
                </div>
            </div>

            <div class="card bg-base-100 text-base-content shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Report status</h2>
                    @if($total > 0)
                        <div role="alert" class="alert alert-success alert-soft">
                            <span>Feedback data is available for review.</span>
                        </div>
                    @else
                        <div role="alert" class="alert alert-info alert-soft">
                            <span>No attendance feedback has been submitted yet.</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach($ratings as $rating)
                @php
                    $isActive = $active === $rating['key'];
                    $percent = $total > 0 ? round(($rating['count'] / $total) * 100) : 0;
                @endphp
                <a href="{{ route('admin.attendance.feedbacks', ['rating' => $rating['key']]) }}"
                   class="card bg-base-100 text-base-content no-underline shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $isActive ? 'ring-2 ring-primary' : '' }}">
                    <div class="card-body gap-3 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="badge {{ $rating['badge'] }}">{{ $rating['label'] }}</span>
                            <span class="text-xs text-base-content/60">{{ $percent }}%</span>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">{{ $rating['count'] }}</div>
                            <p class="text-xs text-base-content/60">{{ $rating['desc'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>

        @if($total > 0)
            <section class="card bg-base-100 text-base-content shadow-sm">
                <div class="card-body">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="card-title">Overall Distribution</h2>
                            <p class="text-sm text-base-content/70">Each bar shows its share of all feedback responses.</p>
                        </div>
                        <span class="badge badge-neutral">{{ $total }} total</span>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @foreach($ratings as $rating)
                            @php
                                $percent = $total > 0 ? round(($rating['count'] / $total) * 100) : 0;
                            @endphp
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="font-medium">{{ $rating['label'] }}</span>
                                    <span class="text-base-content/60">{{ $rating['count'] }} responses &middot; {{ $percent }}%</span>
                                </div>
                                <progress class="progress {{ $rating['progress'] }} w-full" value="{{ $rating['count'] }}" max="{{ $total }}"></progress>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="card bg-base-100 text-base-content shadow-sm">
            <div class="card-body">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="card-title">
                            {{ $activeLabel ? ucwords($activeLabel) . ' Responses' : 'All Responses' }}
                        </h2>
                        <p class="text-sm text-base-content/70">Latest submissions appear first.</p>
                    </div>

                    @if($active)
                        <a href="{{ route('admin.attendance.feedbacks') }}" class="btn btn-sm btn-outline no-underline">
                            Show all
                        </a>
                    @endif
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="table table-zebra text-base-content">
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
                                    <th>{{ $index + 1 }}</th>
                                    <td>
                                        <div class="font-medium">{{ $studentName !== '' ? $studentName : 'Unknown student' }}</div>
                                    </td>
                                    <td>
                                        @if($feedback->declined)
                                            <span class="badge badge-secondary">Declined</span>
                                        @elseif($feedback->rating)
                                            <span class="badge {{ $ratingMeta['badge'] ?? 'badge-neutral' }}">
                                                {{ ucwords(str_replace('_', ' ', $feedback->rating)) }}
                                            </span>
                                        @else
                                            <span class="badge badge-ghost">No rating</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $feedback->declined ? 'badge-secondary' : 'badge-ghost' }}">
                                            {{ $feedback->declined ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $feedback->created_at?->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="py-8 text-center text-base-content/70">
                                            No feedback found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
