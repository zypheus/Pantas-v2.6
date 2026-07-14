@extends('layouts.sidebar')

@section('title', 'Branding Version History')

@section('content')
<div class="container-fluid py-3" id="branding-versions">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-muted mb-1">Developer workspace</p>
            <h1 class="h3 mb-1">Branding Version History</h1>
            <p class="text-muted mb-0">Browse previous branding configurations and restore a past state.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('developer.branding.edit') }}" class="btn btn-outline-primary">Branding Settings</a>
            <a href="{{ route('developer.branding.activity') }}" class="btn btn-outline-secondary">Activity Log</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($versions->isEmpty())
                <p class="text-muted mb-0">No version history yet. Branding version snapshots are created automatically whenever settings are saved or restored.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Snapshot Date</th>
                                <th>Changed By</th>
                                <th>Fields Included</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($versions as $version)
                                <tr>
                                    <td>{{ $version->id }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $version->created_at->format('M j, Y') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $version->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td>{{ $version->changer?->name ?? 'System' }}</td>
                                    <td>
                                        @php
                                            $nonNullFields = collect($version->snapshot)->filter()->keys()->take(5)->implode(', ');
                                            $total = count(array_filter($version->snapshot ?? []));
                                        @endphp
                                        <span class="small">{{ $nonNullFields ?: 'All defaults' }}</span>
                                        @if ($total > 5)
                                            <span class="badge bg-secondary ms-1">+{{ $total - 5 }} more</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('developer.branding.restore-version', $version) }}" onsubmit="return confirm('Restore branding to the state from {{ $version->created_at->format('M j, Y g:i A') }}? This will create a new snapshot of the current state first.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Restore</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $versions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection