@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">{{ $reservation->room->name ?? 'Reservation' }}</h1>
            <p class="rooms-page__subtitle">
                @if($reservation->date)
                    {{ \Carbon\Carbon::parse($reservation->date)->format('l, F j, Y') }}
                @endif
                @if($reservation->start_time && $reservation->end_time)
                    · {{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }}
                    – {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}
                @endif
            </p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('rooms.schedule') }}" class="rooms-btn rooms-btn--outline">← Schedule</a>
            @if($reservation->status === 'pending')
                <form action="{{ route('rooms.approve', $reservation->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="rooms-btn rooms-btn--success">Approve</button>
                </form>
                <form action="{{ route('rooms.reject', $reservation->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Reject this reservation?');">
                    @csrf
                    <button type="submit" class="rooms-btn rooms-btn--danger">Reject</button>
                </form>
            @endif
        </div>
    </header>

    @include('rooms.partials.subnav')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="rooms-card">
                <h2 class="rooms-card__title">Reservation details</h2>
                <dl class="rooms-detail-grid">
                    <div class="rooms-detail-item">
                        <dt>Status</dt>
                        <dd>
                            @if($reservation->status === 'approved')
                                <span class="rooms-badge rooms-badge--approved">Approved</span>
                            @elseif($reservation->status === 'pending')
                                <span class="rooms-badge rooms-badge--pending">Pending</span>
                            @elseif($reservation->status === 'rejected')
                                <span class="rooms-badge rooms-badge--rejected">Rejected</span>
                            @else
                                <span class="rooms-badge rooms-badge--muted">{{ ucfirst($reservation->status ?? '—') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="rooms-detail-item">
                        <dt>Patron email</dt>
                        <dd>
                            @if($reservation->patron_email)
                                <a href="mailto:{{ $reservation->patron_email }}">{{ $reservation->patron_email }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="rooms-detail-item">
                        <dt>Number of students</dt>
                        <dd>{{ $reservation->number_of_students ?? '—' }}</dd>
                    </div>
                    <div class="rooms-detail-item">
                        <dt>Room</dt>
                        <dd>{{ $reservation->room->name ?? '—' }}</dd>
                    </div>
                </dl>

                <h3 class="rooms-card__title mt-4">Attendees</h3>
                @if($reservation->students->isNotEmpty())
                    <ul class="rooms-student-list">
                        @foreach($reservation->students as $s)
                            <li>{{ $s->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No student names on file.</p>
                @endif
            </div>
        </div>

        @if($reservation->logs && $reservation->logs->count() > 0)
        <div class="col-lg-4">
            <div class="rooms-card">
                <h2 class="rooms-card__title">Activity</h2>
                <ul class="list-unstyled mb-0">
                    @foreach($reservation->logs->sortByDesc('created_at') as $log)
                        <li class="py-2 border-bottom">
                            <span class="rooms-badge rooms-badge--muted">{{ ucfirst($log->action) }}</span>
                            <div class="small text-muted mt-1">{{ $log->created_at->format('M j, Y g:i A') }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
