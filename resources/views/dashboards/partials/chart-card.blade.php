<div class="dashboard-card dashboard-chart-card h-100">
    <div class="dashboard-card-body">
        <div class="dashboard-panel-header">
            <div>
                <span class="dashboard-panel-kicker">{{ ucfirst($chart['type'] ?? 'Chart') }}</span>
                <h2>{{ $chart['title'] }}</h2>
            </div>
            <span class="dashboard-panel-meta">Live data</span>
        </div>
        <div class="dashboard-chart-frame">
            <canvas id="{{ $chart['id'] }}" height="120"></canvas>
        </div>
    </div>
</div>
