@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Pending requests</h1>
            <p class="rooms-page__subtitle">Review and approve or reject booking requests.</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('rooms.schedule') }}" class="rooms-btn rooms-btn--outline">View schedule</a>
            <a href="{{ route('book.index') }}" class="rooms-btn rooms-btn--outline">← Catalog</a>
        </div>
    </header>

    @include('rooms.partials.subnav')
    @include('rooms.partials.alerts')

    @if($pending->count() > 0)
        <div class="rooms-card rooms-card--flush-table">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>When</th>
                            <th>Contact</th>
                            <th>Students</th>
                            <th class="text-end">Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $res)
                            <tr>
                                <td>
                                    <strong>{{ $res->room->name ?? 'N/A' }}</strong>
                                    <div class="small text-muted">{{ $res->number_of_students }} attendee(s)</div>
                                </td>
                                <td>
                                    <div>{{ $res->date ? \Carbon\Carbon::parse($res->date)->format('D, M j, Y') : '—' }}</div>
                                    <div class="small text-muted">
                                        @if($res->start_time && $res->end_time)
                                            {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $res->patron_email }}">{{ $res->patron_email ?? '—' }}</a>
                                </td>
                                <td>
                                    @if($res->students && $res->students->count() > 0)
                                        <ul class="rooms-student-list mb-0">
                                            @foreach($res->students as $s)
                                                <li>{{ $s->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap justify-content-end gap-1">
                                        <a href="{{ route('rooms.show', $res->id) }}" class="rooms-btn rooms-btn--outline rooms-btn--sm">Details</a>
                                        <form action="{{ route('rooms.approve', $res->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="rooms-btn rooms-btn--success rooms-btn--sm">Approve</button>
                                        </form>
                                        <form action="{{ route('rooms.reject', $res->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Reject this reservation?');">
                                            @csrf
                                            <button type="submit" class="rooms-btn rooms-btn--danger rooms-btn--sm">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rooms-card rooms-empty">
            <div class="rooms-empty__icon">✓</div>
            <p class="mb-0">No pending requests — you're all caught up.</p>
            <a href="{{ route('rooms.schedule') }}" class="rooms-btn rooms-btn--outline mt-3">View schedule</a>
        </div>
    @endif
</div>
@endsection
