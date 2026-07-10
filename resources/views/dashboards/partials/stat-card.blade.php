<div class="dashboard-card dashboard-stat-card">
    <div class="dashboard-card-body">
        <div class="dashboard-stat-topline">
            <span class="dashboard-stat-label">{{ $stat['label'] }}</span>
            <span class="dashboard-icon-chip" aria-hidden="true">
                <i class="bi {{ $stat['icon'] ?? 'bi-bar-chart' }}"></i>
            </span>
        </div>
        <div class="dashboard-stat-value">{{ $stat['value'] }}</div>
        <div class="dashboard-stat-caption">
            <span class="dashboard-status-dot" aria-hidden="true"></span>
            Current snapshot
        </div>
    </div>
</div>
