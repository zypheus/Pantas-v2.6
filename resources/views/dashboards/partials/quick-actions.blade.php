<div class="card border-0 shadow-sm h-100">
    <div class="card-body">
        <h2 class="h6 mb-3">{{ $title ?? 'Quick Actions' }}</h2>
        <div class="d-grid gap-2">
            @foreach ($actions as $action)
                @if (Route::has($action['route']))
                    <a href="{{ route($action['route'], $action['parameters'] ?? []) }}" class="btn btn-outline-primary text-start">
                        <i class="bi {{ $action['icon'] ?? 'bi-arrow-right' }} me-2"></i>{{ $action['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
