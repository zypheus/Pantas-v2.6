@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const charts = @json($charts ?? []);
            const palette = ['#1e3a8a', '#0f766e', '#b45309', '#6d28d9', '#b91c1c', '#047857'];

            charts.forEach(function (chart) {
                const canvas = document.getElementById(chart.id);
                if (!canvas || typeof Chart === 'undefined') {
                    return;
                }

                new Chart(canvas, {
                    type: chart.type || 'bar',
                    data: {
                        labels: chart.labels || [],
                        datasets: [{
                            label: chart.title,
                            data: chart.data || [],
                            borderColor: '#1e3a8a',
                            backgroundColor: chart.type === 'line' ? 'rgba(30, 58, 138, 0.08)' : palette,
                            borderWidth: 2,
                            pointRadius: chart.type === 'line' ? 3 : 0,
                            pointBackgroundColor: '#1e3a8a',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            tension: 0.35,
                            fill: chart.type === 'line'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: chart.type === 'doughnut',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    color: '#475569',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 12
                                }
                            }
                        },
                        scales: chart.type === 'doughnut' ? {} : {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    font: {
                                        size: 12,
                                        weight: '400'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.22)'
                                },
                                ticks: {
                                    precision: 0,
                                    color: '#64748b',
                                    font: {
                                        size: 12,
                                        weight: '400'
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
