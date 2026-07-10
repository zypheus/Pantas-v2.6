<div class="dashboard-card dashboard-recent-card h-100">
    <div class="dashboard-card-body">
        <div class="dashboard-panel-header">
            <div>
                <span class="dashboard-panel-kicker">Activity</span>
                <h2>{{ $title }}</h2>
            </div>
            <span class="dashboard-panel-meta">{{ count($items) }} shown</span>
        </div>

        @forelse ($items as $item)
            @php
                $icon = 'bi-activity';
                $headline = 'Activity';
                $detail = null;
                $time = $item->created_at;

                if ($item instanceof \App\Models\AdminActivity) {
                    $icon = $item->icon ?: 'bi-activity';
                    $headline = $item->title;
                    $detail = $item->body ?: ($item->user?->name ? 'By '.$item->user->name : null);
                } elseif ($item instanceof \App\Models\BookLog) {
                    $icon = $item->status === 'Checked In' ? 'bi-box-arrow-in-down-left' : 'bi-box-arrow-up-right';
                    $headline = $item->book?->title_statement ?: 'Book transaction';
                    $detail = trim($item->status.' - '.$item->patronLabel(), ' -');
                    $time = $item->timestamp ?? $item->created_at;
                } elseif ($item instanceof \App\Models\AttendanceLog) {
                    $icon = $item->status === 'OUT' ? 'bi-box-arrow-right' : 'bi-box-arrow-in-right';
                    $patron = $item->student
                        ? trim($item->student->firstname.' '.$item->student->lastname)
                        : ($item->employee ? trim($item->employee->firstname.' '.$item->employee->lastname) : 'Attendance patron');
                    $headline = $patron;
                    $detail = 'Scanned '.$item->status;
                    $time = $item->scanned_at ?? $item->created_at;
                }
            @endphp

            <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
                <div class="dashboard-activity-copy">
                    <div class="dashboard-activity-title">{{ $headline }}</div>
                    @if ($detail)
                        <div class="dashboard-activity-detail">{{ $detail }}</div>
                    @endif
                    @if ($time)
                        <div class="dashboard-activity-time">{{ $time->timezone('Asia/Manila')->diffForHumans() }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="dashboard-empty-state">{{ $empty ?? 'No recent activity yet.' }}</div>
        @endforelse
    </div>
</div>
