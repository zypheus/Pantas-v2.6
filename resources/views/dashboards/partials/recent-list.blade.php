<div class="card border-0 shadow-sm h-100">
    <div class="card-body">
        <h2 class="h6 mb-3">{{ $title }}</h2>

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

            <div class="d-flex gap-3 py-2 border-bottom">
                <span class="text-primary"><i class="bi {{ $icon }}"></i></span>
                <div class="min-w-0">
                    <div class="fw-semibold">{{ $headline }}</div>
                    @if ($detail)
                        <div class="small text-muted">{{ $detail }}</div>
                    @endif
                    @if ($time)
                        <div class="small text-muted">{{ $time->timezone('Asia/Manila')->diffForHumans() }}</div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">{{ $empty ?? 'No recent activity yet.' }}</p>
        @endforelse
    </div>
</div>
