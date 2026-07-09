<div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div>
                    <p class="text-muted small text-uppercase fw-semibold mb-1">{{ $stat['label'] }}</p>
                    <div class="h3 mb-0">{{ $stat['value'] }}</div>
                </div>
                <span class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem;">
                    <i class="bi {{ $stat['icon'] ?? 'bi-bar-chart' }}"></i>
                </span>
            </div>
        </div>
    </div>
</div>
