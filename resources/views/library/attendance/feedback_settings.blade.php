@extends('layouts.sidebar')

@section('title', 'Library Feedback Settings')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-1">Library Feedback Settings</h1>
            <p class="text-muted mb-0">Control the logout feedback prompt for library attendance scans.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card" style="max-width: 560px;">
            <div class="card-body">
                <form method="POST" action="{{ route('library.attendance.feedback.settings.update') }}">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="enabled" value="0">
                        <input class="form-check-input" id="enabled" type="checkbox" name="enabled" value="1" @checked($enabled)>
                        <label class="form-check-label" for="enabled">Enable library logout feedback</label>
                    </div>
                    <button class="btn btn-primary" type="submit">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
@endsection
