<div class="dashboard-card dashboard-actions-card h-100">
    <div class="dashboard-card-body">
        <div class="dashboard-panel-header">
            <div>
                <span class="dashboard-panel-kicker">Next steps</span>
                <h2>{{ $title ?? 'Quick Actions' }}</h2>
            </div>
        </div>
        <div class="dashboard-action-list">
            @foreach ($actions as $action)
                @if (Route::has($action['route']))
                    <a href="{{ route($action['route'], $action['parameters'] ?? []) }}" class="dashboard-action-link">
                        <span class="dashboard-action-icon" aria-hidden="true">
                            <i class="bi {{ $action['icon'] ?? 'bi-arrow-right' }}"></i>
                        </span>
                        <span>{{ $action['label'] }}</span>
                        <i class="bi bi-arrow-right-short ms-auto" aria-hidden="true"></i>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
