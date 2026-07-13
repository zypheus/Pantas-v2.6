@extends('layouts.sidebar')

@section('title', $title)

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            <div class="dashboard-section-header">
                <div>
                    <p class="dashboard-section-kicker">Developer workspace</p>
                    <h1>{{ $title }}</h1>
                    <p>{{ $summary }}</p>
                </div>
                <span class="sidebar-role-badge">Developer only</span>
            </div>

            <div class="dashboard-content-grid">
                <article class="dashboard-panel" id="branding-settings">
                    <div class="dashboard-panel-header">
                        <div>
                            <span class="dashboard-panel-kicker">Current identity</span>
                            <h2>Branding Settings</h2>
                        </div>
                        <span class="dashboard-panel-meta">{{ $branding['is_customized'] ? 'Customized' : 'Original' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 my-3">
                        <img src="{{ $logoUrl }}" alt="Current sidebar logo" style="width:72px;height:72px;object-fit:contain">
                        <div>
                            <strong>{{ $branding['updated_by'] ?? 'Pantas defaults' }}</strong>
                            <p class="mb-0 text-muted">{{ $branding['updated_at']?->format('M j, Y g:i A') ?? 'No custom update yet' }}</p>
                        </div>
                    </div>
                    <img src="{{ $bannerUrl }}" alt="Current banner" class="img-fluid rounded border mb-3" style="max-height:180px;width:100%;object-fit:cover">
                    <a href="{{ route('developer.branding.edit') }}" class="btn btn-primary">Open Branding Settings</a>
                </article>
            </div>
        </section>
    </div>
@endsection
