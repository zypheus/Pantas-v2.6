@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms/rooms.css') }}">
@endsection

@section('content')
<div class="rooms-page">
    <header class="rooms-page__hero">
        <div>
            <p class="rooms-page__eyebrow">Room reservations</p>
            <h1 class="rooms-page__title">Add room</h1>
            <p class="rooms-page__subtitle">Create a new bookable study room.</p>
        </div>
        <div class="rooms-page__hero-actions">
            <a href="{{ route('rooms.index') }}" class="rooms-btn rooms-btn--outline">← Back to rooms</a>
        </div>
    </header>

    @include('rooms.partials.subnav')

    <div class="rooms-card rooms-form">
        <form action="{{ route('rooms.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Room name</label>
                <input type="text" name="name" id="name" class="form-control" required
                       value="{{ old('name') }}" placeholder="e.g. Group Study Room A">
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"
                          placeholder="Optional notes about equipment, location, etc.">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="form-label" for="capacity">Capacity</label>
                <input type="number" name="capacity" id="capacity" class="form-control" required
                       min="1" value="{{ old('capacity', 10) }}">
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="rooms-btn rooms-btn--success">Save room</button>
                <a href="{{ route('rooms.index') }}" class="rooms-btn rooms-btn--outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
