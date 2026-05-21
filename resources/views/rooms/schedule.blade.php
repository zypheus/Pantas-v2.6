@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
@php
    $pendingCount = $reservations->where('status', 'pending')->count();
    $approvedCount = $reservations->where('status', 'approved')->count();
    $visible = $reservations->where('status', '!=', 'rejected');
@endphp
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Reservation schedule</h1>
            <p class="rooms-page__subtitle">All upcoming and past bookings (rejected hidden).</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('book.index') }}" class="rooms-btn rooms-btn--outline">← Catalog</a>
            @if($pendingCount > 0)
                <a href="{{ route('rooms.pending') }}" class="rooms-btn rooms-btn--warning">
                    Pending ({{ $pendingCount }})
                </a>
            @endif
        </div>
    </header>

    @include('rooms.partials.subnav')
    @include('rooms.partials.alerts')

    <div class="rooms-stats">
        <div class="rooms-stat">
            <div class="rooms-stat__value">{{ $visible->count() }}</div>
            <div class="rooms-stat__label">Total shown</div>
        </div>
        <div class="rooms-stat">
            <div class="rooms-stat__value">{{ $approvedCount }}</div>
            <div class="rooms-stat__label">Approved</div>
        </div>
        <div class="rooms-stat">
            <div class="rooms-stat__value">{{ $pendingCount }}</div>
            <div class="rooms-stat__label">Pending</div>
        </div>
        <div class="rooms-stat">
            <div class="rooms-stat__value">{{ $rooms->count() }}</div>
            <div class="rooms-stat__label">Rooms</div>
        </div>
    </div>

    <div class="rooms-card rooms-card--flush-table">
        @if($visible->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visible as $res)
                            @php
                                $isPast = false;
                                if ($res->start_time && $res->date) {
                                    try {
                                        $isPast = \Carbon\Carbon::parse($res->date.' '.$res->start_time)->isPast();
                                    } catch (Exception $e) {
                                        $isPast = false;
                                    }
                                }
                            @endphp
                            <tr class="{{ $isPast ? 'rooms-row--past' : '' }}">
                                <td><strong>{{ $res->room->name ?? 'N/A' }}</strong></td>
                                <td>{{ $res->date ? \Carbon\Carbon::parse($res->date)->format('M j, Y') : '—' }}</td>
                                <td>
                                    @if($res->start_time && $res->end_time)
                                        {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}
                                        –
                                        {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($res->status === 'approved')
                                        <span class="rooms-badge rooms-badge--approved">Approved</span>
                                    @elseif($res->status === 'pending')
                                        <span class="rooms-badge rooms-badge--pending">Pending</span>
                                    @else
                                        <span class="rooms-badge rooms-badge--muted">{{ ucfirst($res->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('rooms.show', $res->id) }}" class="rooms-btn rooms-btn--outline rooms-btn--sm">View</a>
                                    <form action="{{ route('resrooms.destroy', $res->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Remove this reservation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rooms-btn rooms-btn--danger rooms-btn--sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rooms-empty">
                <div class="rooms-empty__icon">📅</div>
                <p class="mb-0">No reservations on the schedule yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
