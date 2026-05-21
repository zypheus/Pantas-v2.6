@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Edit room</h1>
            <p class="rooms-page__subtitle">{{ $room->name }}</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('rooms.index') }}" class="rooms-btn rooms-btn--outline">← Back to rooms</a>
        </div>
    </header>

    @include('rooms.partials.subnav')

    <div class="rooms-card rooms-form">
        <form action="{{ route('rooms.update', $room->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="name">Room name</label>
                <input type="text" name="name" id="name" class="form-control" required
                       value="{{ old('name', $room->name) }}">
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="form-label" for="capacity">Capacity</label>
                <input type="number" name="capacity" id="capacity" class="form-control" required
                       min="1" value="{{ old('capacity', $room->capacity) }}">
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="rooms-btn rooms-btn--success">Update room</button>
                <a href="{{ route('rooms.index') }}" class="rooms-btn rooms-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
