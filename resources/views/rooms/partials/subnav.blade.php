@php
    $pendingCount = \App\Models\RoomReservation::where('status', 'pending')->count();
@endphp
<nav class="rooms-subnav" aria-label="Room reservations">
    <a href="{{ route('rooms.index') }}"
       class="rooms-subnav__link {{ request()->routeIs('rooms.index', 'rooms.create', 'rooms.edit') ? 'active' : '' }}">
        Manage rooms
    </a>
    <a href="{{ route('rooms.book') }}"
       class="rooms-subnav__link {{ request()->routeIs('rooms.book') ? 'active' : '' }}"
       @if(!auth()->check()) target="_blank" rel="noopener" @endif>
        Book a room
    </a>
    <a href="{{ route('rooms.schedule') }}"
       class="rooms-subnav__link {{ request()->routeIs('rooms.schedule', 'rooms.show') ? 'active' : '' }}">
        View schedule
    </a>
    <a href="{{ route('rooms.pending') }}"
       class="rooms-subnav__link {{ request()->routeIs('rooms.pending') ? 'active' : '' }}">
        Pending requests
        @if($pendingCount > 0)
            <span class="rooms-subnav__badge">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('rooms.logs') }}"
       class="rooms-subnav__link {{ request()->routeIs('rooms.logs') ? 'active' : '' }}">
        Reservation logs
    </a>
</nav>
