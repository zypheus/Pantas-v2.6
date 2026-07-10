@php
    $routeName = request()->route()?->getName() ?? '';
    $moduleLabel = str_contains($routeName, 'attendance') ? 'Attendance' : (str_contains($routeName, 'library') ? 'Library' : 'Unified');
    $roleLabel = str(auth()->user()?->role ?? 'staff')->replace('_', ' ')->title();
@endphp

<div class="dashboard-hero">
    <div class="dashboard-hero-copy">
        <div class="dashboard-eyebrow">
            <span class="dashboard-live-dot" aria-hidden="true"></span>
            {{ $moduleLabel }} operations
        </div>
        <h1>{{ $title }}</h1>
        <p>{{ $summary }}</p>
    </div>

    <div class="dashboard-context" aria-label="Dashboard context">
        <div class="dashboard-context-item">
            <span>Role</span>
            <strong>{{ $roleLabel }}</strong>
        </div>
        <div class="dashboard-context-item">
            <span>Local date</span>
            <strong>{{ now('Asia/Manila')->format('M j, Y') }}</strong>
        </div>
    </div>

    @if (! empty($quickActions ?? []))
        <div class="dashboard-header-actions" aria-label="Quick actions">
            @foreach (array_slice($quickActions, 0, 2) as $action)
                @if (Route::has($action['route']))
                    <a href="{{ route($action['route'], $action['parameters'] ?? []) }}" class="dashboard-header-action {{ $loop->first ? 'primary' : '' }}">
                        <i class="bi {{ $action['icon'] ?? 'bi-arrow-right' }}" aria-hidden="true"></i>
                        <span>{{ $action['label'] }}</span>
                    </a>
                @endif
            @endforeach

            @if (count($quickActions) > 2)
                <div class="dropdown">
                    <button class="dashboard-header-action" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-lightning-charge" aria-hidden="true"></i>
                        <span>More</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end topbar-dropdown">
                        @foreach (array_slice($quickActions, 2) as $action)
                            @if (Route::has($action['route']))
                                <a href="{{ route($action['route'], $action['parameters'] ?? []) }}" class="dropdown-item topbar-dropdown-item">
                                    <i class="bi {{ $action['icon'] ?? 'bi-arrow-right' }}" aria-hidden="true"></i>
                                    {{ $action['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
