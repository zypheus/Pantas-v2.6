@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Reservation logs</h1>
            <p class="rooms-page__subtitle">Audit trail of approvals, creations, and changes.</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('rooms.schedule') }}" class="rooms-btn rooms-btn--outline">← Schedule</a>
        </div>
    </header>

    @include('rooms.partials.subnav')

    <div class="rooms-card rooms-card--flush-table">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Room</th>
                            <th>Reservation</th>
                            <th>Action</th>
                            <th>By</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $loop->index }}</td>
                                <td>{{ $log->reservation->room->name ?? '—' }}</td>
                                <td>
                                    @if($log->reservation)
                                        <div>{{ $log->reservation->date ? \Carbon\Carbon::parse($log->reservation->date)->format('M j, Y') : '—' }}</div>
                                        <div class="small text-muted">
                                            @if($log->reservation->start_time && $log->reservation->end_time)
                                                {{ \Carbon\Carbon::parse($log->reservation->start_time)->format('g:i A') }}
                                                –
                                                {{ \Carbon\Carbon::parse($log->reservation->end_time)->format('g:i A') }}
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @switch($log->action)
                                        @case('approved')
                                            <span class="rooms-badge rooms-badge--approved">Approved</span>
                                            @break
                                        @case('created')
                                            <span class="rooms-badge rooms-badge--created">Created</span>
                                            @break
                                        @case('rejected')
                                            <span class="rooms-badge rooms-badge--rejected">Rejected</span>
                                            @break
                                        @case('cancelled')
                                            <span class="rooms-badge rooms-badge--cancelled">Cancelled</span>
                                            @break
                                        @default
                                            <span class="rooms-badge rooms-badge--muted">{{ ucfirst($log->action) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td class="text-nowrap">{{ $log->created_at->format('M j, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="rooms-empty">
                <div class="rooms-empty__icon">📋</div>
                <p class="mb-0">No reservation activity logged yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
