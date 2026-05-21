@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Manage rooms</h1>
            <p class="rooms-page__subtitle">Add study rooms, set capacity, and maintain room details.</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('book.index') }}" class="rooms-btn rooms-btn--outline">← Catalog</a>
            <a href="{{ route('rooms.create') }}" class="rooms-btn rooms-btn--primary">+ Add room</a>
        </div>
    </header>

    @include('rooms.partials.subnav')
    @include('rooms.partials.alerts')

    @if($rooms->count() > 0)
        <div class="rooms-grid">
            @foreach($rooms as $room)
                <article class="rooms-room-card">
                    <h2 class="rooms-room-card__name">{{ $room->name }}</h2>
                    <p class="rooms-room-card__desc">{{ $room->description ?: 'No description provided.' }}</p>
                    <span class="rooms-room-card__meta">Capacity: {{ $room->capacity }}</span>
                    <div class="rooms-room-card__actions">
                        <a href="{{ route('rooms.edit', $room->id) }}" class="rooms-btn rooms-btn--outline rooms-btn--sm">Edit</a>
                        <form action="{{ route('rooms.destroy', $room->id) }}" method="POST"
                              onsubmit="return confirm('Delete this room?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rooms-btn rooms-btn--danger rooms-btn--sm">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="rooms-card rooms-empty">
            <div class="rooms-empty__icon">🏫</div>
            <p class="mb-2">No rooms configured yet.</p>
            <a href="{{ route('rooms.create') }}" class="rooms-btn rooms-btn--primary">Add your first room</a>
        </div>
    @endif
</div>
@endsection
