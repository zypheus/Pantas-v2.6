<div class="dashboard-section-header">
    <div>
        <span class="dashboard-section-kicker">{{ $kicker ?? 'Overview' }}</span>
        <h2>{{ $title }}</h2>
        @if (! empty($description))
            <p>{{ $description }}</p>
        @endif
    </div>

    @if (! empty($meta))
        <span class="dashboard-section-meta">{{ $meta }}</span>
    @endif
</div>
