@extends('layouts.sec')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Logout feedback (attendance scanner)</h3>
        <a href="{{ route('attendance.scan') }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
            Open scanner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <p class="text-muted">
                When enabled, students who scan <strong>OUT</strong> at the attendance kiosk see a short popup asking
                how their library visit was. Responses appear under
                <a href="{{ route('admin.attendance.feedbacks') }}">View Feedback Responses</a>.
            </p>

            <form method="POST" action="{{ route('attendance.feedback.settings.update') }}">
                @csrf
                <input type="hidden" name="enabled" value="0">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="logoutFeedbackEnabled"
                           name="enabled" value="1" {{ $enabled ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="logoutFeedbackEnabled">
                        Show feedback prompt on logout (scan OUT)
                    </label>
                </div>
                <p class="small text-muted mb-3">
                    Status: <strong>{{ $enabled ? 'Enabled' : 'Disabled' }}</strong>
                </p>
                <button type="submit" class="btn btn-primary">Save setting</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold">Preview</div>
        <div class="card-body text-center">
            <p class="small text-muted mb-3">Rating options shown on the scanner:</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <span class="badge bg-light text-dark border">😊 Excellent</span>
                <span class="badge bg-light text-dark border">🙂 Good</span>
                <span class="badge bg-light text-dark border">😐 Medium</span>
                <span class="badge bg-light text-dark border">🙁 Poor</span>
                <span class="badge bg-light text-dark border">😠 Very Bad</span>
            </div>
            <p class="small text-muted mt-3 mb-0">Students can also tap <strong>Skip</strong>.</p>
        </div>
    </div>
</div>
@endsection
