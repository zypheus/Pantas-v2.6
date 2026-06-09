@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tailwind-build.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_logs/index.css') }}">
@endsection

@section('content')

    <!-- Feedbacks list content (no footer) -->
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📋 Submitted Feedbacks</h2>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($feedbacks->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>No feedbacks submitted yet.</p>
            </div>
        @else
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:48px">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Comments</th>
                                    <th class="text-center" style="width:170px">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feedbacks as $index => $feedback)
                                    <tr>
                                        <td>{{ $feedbacks->firstItem() + $index }}</td>
                                        <td>{{ $feedback->name ?: 'Anonymous' }}</td>
                                        <td>{{ $feedback->email ?: '—' }}</td>
                                        <td style="max-width:520px; white-space:pre-line;">{{ $feedback->comments }}</td>
                                        <td class="text-center">{{ $feedback->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $feedbacks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
    
@endsection
