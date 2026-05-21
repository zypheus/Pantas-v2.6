{{-- Expects: $programNameByCode, $topStudentsByIns, $topStudentsByDistinctInDays, $programAttendanceTotals, $weeklyInsTrend, $monthlyInsTrend, $busiestHours --}}
<div class="row g-3">
    @if(empty($only) || $only === 'top-ins')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-top-ins">Top 10 — most IN scans</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartTopIns"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-end">INs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByIns as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-start small">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->ins_count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No IN scans yet.</td></tr>
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
            <div class="card-header py-2 fw-semibold small" id="report-distinct-days">Top 10 — most distinct days with an IN</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartDistinctDays"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-end">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByDistinctInDays as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-start small">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->distinct_in_days) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No IN scans yet.</td></tr>
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
            <div class="card-header py-2 fw-semibold small" id="report-program-totals">Totals by program / course (registered patrons &amp; IN scans)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 280px;">
                        <canvas id="chartProgramTotals"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Program / course</th>
                                <th class="text-end">Registered patrons</th>
                                <th class="text-end">IN scans (all time)</th>
                                <th class="text-end">Avg INs / patron</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programAttendanceTotals as $row)
                                <tr>
                                    <td class="text-start">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->student_count) }}</td>
                                    <td class="text-end">{{ number_format($row->ins_count) }}</td>
                                    <td class="text-end">{{ number_format($row->avg_ins_per_student ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No program/course data yet.</td></tr>
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
            <div class="card-header py-2 fw-semibold small" id="report-weekly">IN scans by week (last 12 weeks, Asia/Manila)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartWeeklyTrend"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Week</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($weeklyInsTrend as $row)
                                <tr><td class="text-start small">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
            <div class="card-header py-2 fw-semibold small" id="report-monthly">IN scans by month (last 12 months, Asia/Manila)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartMonthlyTrend"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Month</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($monthlyInsTrend as $row)
                                <tr><td class="text-start small">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
            <div class="card-header py-2 fw-semibold small" id="report-busiest-hour">Busiest hour (IN scans, all time)</div>
            <div class="card-body p-0">
                <p class="text-muted small px-3 pt-2 mb-0">Uses <code>HOUR(scanned_at)</code> as stored in the database (app timezone {{ config('app.timezone') }}).</p>
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartBusiestHour"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 260px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Hour</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($busiestHours->take(12) as $row)
                                <tr><td class="text-start">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
                    data: { labels: topInsLabels, datasets: [{ label: 'IN scans', data: topInsCounts, backgroundColor: 'rgba(13,110,253,0.45)', borderColor: 'rgba(13,110,253,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartDistinctDays', {
                    type: 'bar',
                    data: { labels: distinctLabels, datasets: [{ label: 'Days with IN', data: distinctCounts, backgroundColor: 'rgba(25,135,84,0.45)', borderColor: 'rgba(25,135,84,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartProgramTotals', {
                    type: 'bar',
                    data: { labels: progLabels, datasets: [{ label: 'IN scans', data: progIns, backgroundColor: 'rgba(255,193,7,0.5)', borderColor: 'rgba(255,193,7,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartWeeklyTrend', {
                    type: 'line',
                    data: { labels: weeklyLabels, datasets: [{ label: 'IN scans', data: weeklyCounts, borderColor: 'rgba(13,110,253,1)', backgroundColor: 'rgba(13,110,253,0.2)', tension: 0.25, fill: true, pointRadius: 2 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartMonthlyTrend', {
                    type: 'line',
                    data: { labels: monthlyLabels, datasets: [{ label: 'IN scans', data: monthlyCounts, borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.2)', tension: 0.25, fill: true, pointRadius: 2 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                makeChart('chartBusiestHour', {
                    type: 'bar',
                    data: { labels: hourLabels, datasets: [{ label: 'IN scans', data: hourCounts, backgroundColor: 'rgba(108,117,125,0.45)', borderColor: 'rgba(108,117,125,1)', borderWidth: 1 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });
            })();
        </script>
    @endpush
@endonce
