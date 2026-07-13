@extends('layouts.sidebar')

@section('title', 'Branding Activity')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-muted mb-1">Developer workspace</p>
            <h1 class="h3 mb-1">Branding Activity</h1>
            <p class="text-muted mb-0">Audit trail of branding changes and restorations.</p>
        </div>
        <a href="{{ route('developer.branding.edit') }}" class="btn btn-outline-primary">Back to Branding Settings</a>
    </div>

    @if ($activities->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">No branding activity recorded yet.</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Developer</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td class="text-nowrap">{{ $activity->created_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $activity->user?->name ?? 'Unknown' }}</td>
                                <td>
                                    @if ($activity->type === 'branding_update')
                                        <span class="badge bg-primary">Update</span>
                                    @elseif ($activity->type === 'branding_restore')
                                        <span class="badge bg-warning text-dark">Partial Restore</span>
                                    @elseif ($activity->type === 'branding_restore_all')
                                        <span class="badge bg-danger">Full Restore</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $activity->type }}</span>
                                    @endif
                                </td>
                                <td>{{ $activity->body ?? $activity->title }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection
