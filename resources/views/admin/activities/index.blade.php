@extends('layouts.sidebar')

@section('title', 'Admin Activity')

@section('header')
    <div>
        <h1 class="h4 mb-1">Admin Activity</h1>
        <p class="text-muted mb-0">Read-only audit trail for staff and module actions.</p>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Module</th>
                            <th>Activity</th>
                            <th>Actor</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="text-nowrap">{{ $activity->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                                <td><span class="badge text-bg-light">{{ str_replace('-', ' ', ucfirst($activity->module)) }}</span></td>
                                <td>
                                    <div class="fw-semibold">
                                        <i class="bi {{ $activity->icon ?: 'bi-activity' }} me-1 text-primary"></i>{{ $activity->title }}
                                    </div>
                                    <div class="small text-muted">{{ str_replace('_', ' ', ucfirst($activity->type)) }}</div>
                                </td>
                                <td>{{ $activity->user?->name ?? 'System' }}</td>
                                <td>{{ $activity->body ?? 'No additional details.' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No admin activity has been recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $activities->links() }}
        </div>
    </div>
@endsection
