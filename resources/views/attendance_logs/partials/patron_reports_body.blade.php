{{-- Expects: $programNameByCode, $topStudentsByIns, $topStudentsByDistinctInDays, $programAttendanceTotals, $weeklyInsTrend, $monthlyInsTrend, $busiestHours --}}
<div class="row g-3">
    @if(empty($only) || $only === 'top-ins')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-primary text-white" id="report-top-ins">Top 10 Patrons by IN Scans</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartTopIns"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-right">INs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByIns as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-base-content">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-base-content/60 text-sm">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-neutral badge-sm">{{ number_format($row->ins_count) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-base-content/60 text-center py-3">No IN scans yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'distinct-days')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-success text-white" id="report-distinct-days">Top 10 Patrons by Distinct IN Days</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartDistinctDays"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-right">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByDistinctInDays as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-base-content">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-base-content/60 text-sm">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-neutral badge-sm">{{ number_format($row->distinct_in_days) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-base-content/60 text-center py-3">No IN scans yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'program-totals')
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-warning text-dark" id="report-program-totals">Program Attendance Totals</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 280px;">
                        <canvas id="chartProgramTotals"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Program / course</th>
                                <th class="text-right">Registered patrons</th>
                                <th class="text-right">IN scans (all time)</th>
                                <th class="text-right">Avg INs / patron</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programAttendanceTotals as $row)
                                <tr>
                                    <td class="text-base-content">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-right">{{ number_format($row->student_count) }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-warning badge-sm">{{ number_format($row->ins_count) }}</span>
                                    </td>
                                    <td class="text-right">{{ number_format($row->avg_ins_per_student ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-base-content/60 text-center py-3">No program/course data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'weekly')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-info text-dark" id="report-weekly">Weekly IN Scan Trend</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartWeeklyTrend"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto" style="max-height: 280px;">
                    <table class="table table-zebra table-sm table-pin-rows mb-0">
                        <thead><tr><th>Week</th><th class="text-right">INs</th></tr></thead>
                        <tbody>
                            @forelse($weeklyInsTrend as $row)
                                <tr><td class="text-base-content/70 text-sm">{{ $row->label }}</td><td class="text-right"><span class="badge badge-info badge-sm">{{ number_format($row->count) }}</span></td></tr>
                            @empty
                                <tr><td colspan="2" class="text-base-content/60 text-center py-3">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'monthly')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-danger text-white" id="report-monthly">Monthly IN Scan Trend</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartMonthlyTrend"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto" style="max-height: 280px;">
                    <table class="table table-zebra table-sm table-pin-rows mb-0">
                        <thead><tr><th>Month</th><th class="text-right">INs</th></tr></thead>
                        <tbody>
                            @forelse($monthlyInsTrend as $row)
                                <tr><td class="text-base-content/70 text-sm">{{ $row->label }}</td><td class="text-right"><span class="badge badge-error badge-sm">{{ number_format($row->count) }}</span></td></tr>
                            @empty
                                <tr><td colspan="2" class="text-base-content/60 text-center py-3">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'busiest-hour')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small bg-secondary text-white" id="report-busiest-hour">Busiest Library Hours</div>
            <div class="card-body p-0">
                <p class="text-muted small px-3 pt-2 mb-0">Uses <code>HOUR(scanned_at)</code> as stored in the database (app timezone {{ config('app.timezone') }}).</p>
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartBusiestHour"></canvas>
                    </div>
                </div>
                <div class="overflow-x-auto" style="max-height: 260px;">
                    <table class="table table-zebra table-sm table-pin-rows mb-0">
                        <thead><tr><th>Hour</th><th class="text-right">INs</th></tr></thead>
                        <tbody>
                            @forelse($busiestHours->take(12) as $row)
                                <tr><td class="text-base-content">{{ $row->label }}</td><td class="text-right"><span class="badge badge-secondary badge-sm">{{ number_format($row->count) }}</span></td></tr>
                            @empty
                                <tr><td colspan="2" class="text-base-content/60 text-center py-3">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                if (!window.Chart) return;

                const topInsLabels = @json(collect($topStudentsByIns)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
                const topInsCounts = @json(collect($topStudentsByIns)->map(fn($r) => (int) ($r->ins_count ?? 0))->values());

                const distinctLabels = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
                const distinctCounts = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => (int) ($r->distinct_in_days ?? 0))->values());

                const progLabels = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => $r->course ? ($programNameByCode->get($r->course, $r->course)) : '—')->values());
                const progIns = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => (int) ($r->ins_count ?? 0))->values());

                const weeklyLabels = @json(collect($weeklyInsTrend)->map(fn($r) => (string) ($r->label ?? ''))->values());
                const weeklyCounts = @json(collect($weeklyInsTrend)->map(fn($r) => (int) ($r->count ?? 0))->values());

                const monthlyLabels = @json(collect($monthlyInsTrend)->map(fn($r) => (string) ($r->label ?? ''))->values());
                const monthlyCounts = @json(collect($monthlyInsTrend)->map(fn($r) => (int) ($r->count ?? 0))->values());

                const hourLabels = @json(collect($busiestHours)->take(12)->map(fn($r) => (string) ($r->label ?? ''))->values());
                const hourCounts = @json(collect($busiestHours)->take(12)->map(fn($r) => (int) ($r->count ?? 0))->values());

                function makeChart(canvasId, config) {
                    const el = document.getElementById(canvasId);
                    if (!el) return;
                    const ctx = el.getContext('2d');
                    // eslint-disable-next-line no-new
                    new Chart(ctx, config);
                }

                makeChart('chartTopIns', {
                    type: 'bar',
                    data: { labels: topInsLabels, datasets: [{ label: 'IN scans', data: topInsCounts, backgroundColor: 'rgba(13,110,253,0.55)', borderColor: 'rgba(13,110,253,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 } } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartDistinctDays', {
                    type: 'bar',
                    data: { labels: distinctLabels, datasets: [{ label: 'Days with IN', data: distinctCounts, backgroundColor: 'rgba(25,135,84,0.55)', borderColor: 'rgba(25,135,84,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 } } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartProgramTotals', {
                    type: 'bar',
                    data: { labels: progLabels, datasets: [{ label: 'IN scans', data: progIns, backgroundColor: 'rgba(255,193,7,0.65)', borderColor: 'rgba(255,193,7,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartWeeklyTrend', {
                    type: 'line',
                    data: { labels: weeklyLabels, datasets: [{ label: 'IN scans', data: weeklyCounts, borderColor: 'rgba(13,202,240,1)', backgroundColor: 'rgba(13,202,240,0.2)', tension: 0.25, fill: true, pointRadius: 3 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartMonthlyTrend', {
                    type: 'line',
                    data: { labels: monthlyLabels, datasets: [{ label: 'IN scans', data: monthlyCounts, borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.22)', tension: 0.25, fill: true, pointRadius: 3 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartBusiestHour', {
                    type: 'bar',
                    data: { labels: hourLabels, datasets: [{ label: 'IN scans', data: hourCounts, backgroundColor: 'rgba(111,66,193,0.55)', borderColor: 'rgba(111,66,193,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });
            })();
        </script>
    @endpush
@endonce
