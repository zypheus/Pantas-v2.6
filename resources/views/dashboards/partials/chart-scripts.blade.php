@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const charts = @json($charts ?? []);
            const palette = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997'];

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
                            borderColor: '#0d6efd',
                            backgroundColor: chart.type === 'line' ? 'rgba(13, 110, 253, 0.12)' : palette,
                            tension: 0.35,
                            fill: chart.type === 'line'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: chart.type === 'doughnut'
                            }
                        },
                        scales: chart.type === 'doughnut' ? {} : {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
