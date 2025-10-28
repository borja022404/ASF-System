@extends('layouts.Vet.app')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #6c757d;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.stat-card .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.card-header-gradient {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}
</style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header card-header-gradient text-white text-center py-4">
                <h3 class="mb-1">
                    <i class="bi bi-bar-chart-line-fill me-2"></i>
                    Report Analytics Dashboard
                </h3>
                <p class="mb-0">Comprehensive insights from all farmer reports in the system.</p>
            </div>

            <div class="card-body p-4">
                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-number" id="totalReports">{{ $totalReports ?? 0 }}</div>
                            <div class="stat-label">Total Reports</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                            <div class="stat-number">{{ $submittedCount ?? 0 }}</div>
                            <div class="stat-label">Submitted</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                            <div class="stat-number">{{ $underReviewCount ?? 0 }}</div>
                            <div class="stat-label">Under Review</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #333;">
                            <div class="stat-number">{{ $resolvedCount ?? 0 }}</div>
                            <div class="stat-label">Resolved</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Reports by Status -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i> Reports by Status</h6>
                                <div class="spinner-border spinner-border-sm text-secondary d-none" id="statusLoading"></div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="statusChart"></canvas>
                                </div>
                                <div id="statusChartError" class="chart-loading d-none">
                                    <div class="text-center">
                                        <i class="bi bi-exclamation-triangle fs-3"></i>
                                        <p>Unable to load chart</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports per Month -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i> Monthly Reports Trend</h6>
                                <div class="spinner-border spinner-border-sm text-secondary d-none" id="monthlyLoading"></div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                                <div id="monthlyChartError" class="chart-loading d-none">
                                    <div class="text-center">
                                        <i class="bi bi-exclamation-triangle fs-3"></i>
                                        <p>Unable to load chart</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Status Distribution -->
                <div class="row g-4 mt-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-heart-pulse me-2"></i> Health Status Distribution</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="healthChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mortality vs Affected -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i> Mortality vs Affected Pigs</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="mortalityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location-based Reports -->
                <div class="row g-4 mt-3">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2"></i> Reports by Province</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="locationChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js" 
        integrity="sha512-7U4rRB2aOHSB5Gx2fgpU6j4TBB7xKzKQhUOcYVBkdq1bwSjK0tS8vD5yGhkpjcZKGAfN2V/1V3aWj8kL8H7MYA=="
        crossorigin="anonymous"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Check if Chart.js loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load');
        showChartError();
        return;
    }

    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // Data from Laravel (with fallback values)
    const chartData = {
        status: {
            submitted: {{ $submittedCount ?? 0 }},
            underReview: {{ $underReviewCount ?? 0 }},
            resolved: {{ $resolvedCount ?? 0 }},
            closed: {{ $closedCount ?? 0 }}
        },
        monthly: {
            labels: {!! json_encode($months ?? []) !!},
            data: {!! json_encode($monthlyCounts ?? []) !!}
        },
        mortality: {
            labels: {!! json_encode($mortalityLabels ?? []) !!},
            affected: {!! json_encode($affectedData ?? []) !!},
            mortality: {!! json_encode($mortalityData ?? []) !!}
        },
        health: {
            symptomatic: {{ $symptomaticCount ?? 0 }},
            dead: {{ $deadCount ?? 0 }}
        },
        provinces: {
            labels: {!! json_encode($provinceLabels ?? []) !!},
            data: {!! json_encode($provinceData ?? []) !!}
        }
    };

    // Initialize charts
    try {
        initStatusChart();
        initMonthlyChart();
        initHealthChart();
        initMortalityChart();
        initLocationChart();
        
        console.log('All charts initialized successfully');
    } catch (error) {
        console.error('Chart initialization error:', error);
        showChartError();
    }

    function initStatusChart() {
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;

        const total = chartData.status.submitted + chartData.status.underReview + 
                     chartData.status.resolved + chartData.status.closed;
        
        if (total === 0) {
            showNoDataMessage('statusChart');
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Submitted', 'Under Review', 'Resolved', 'Closed'],
                datasets: [{
                    data: [
                        chartData.status.submitted,
                        chartData.status.underReview,
                        chartData.status.resolved,
                        chartData.status.closed
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#28a745',
                        '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function initMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx || !chartData.monthly.labels.length) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.monthly.labels,
                datasets: [{
                    label: 'Reports Submitted',
                    data: chartData.monthly.data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function initHealthChart() {
        const ctx = document.getElementById('healthChart');
        if (!ctx) return;

        const total = chartData.health.symptomatic + chartData.health.dead;
        
        if (total === 0) {
            showNoDataMessage('healthChart');
            return;
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Symptomatic', 'Dead'],
                datasets: [{
                    label: 'Number of Reports',
                    data: [chartData.health.symptomatic, chartData.health.dead],
                    backgroundColor: ['#ffc107', '#dc3545'],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    function initMortalityChart() {
        const ctx = document.getElementById('mortalityChart');
        if (!ctx || !chartData.mortality.labels.length) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.mortality.labels,
                datasets: [
                    {
                        label: 'Affected Pigs',
                        data: chartData.mortality.affected,
                        backgroundColor: '#17a2b8',
                        borderRadius: 5
                    },
                    {
                        label: 'Mortalities',
                        data: chartData.mortality.mortality,
                        backgroundColor: '#dc3545',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function initLocationChart() {
        const ctx = document.getElementById('locationChart');
        if (!ctx || !chartData.provinces.labels.length) return;

        new Chart(ctx, {
            type: 'horizontalBar' in Chart.defaults ? 'horizontalBar' : 'bar',
            data: {
                labels: chartData.provinces.labels,
                datasets: [{
                    label: 'Number of Reports',
                    data: chartData.provinces.data,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', 
                        '#17a2b8', '#6610f2', '#fd7e14', '#20c997'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    function showNoDataMessage(canvasId) {
        const canvas = document.getElementById(canvasId);
        const container = canvas.parentElement;
        container.innerHTML = `
            <div class="chart-loading">
                <div class="text-center">
                    <i class="bi bi-info-circle fs-3 text-muted"></i>
                    <p class="text-muted mt-2">No data available</p>
                </div>
            </div>
        `;
    }

    function showChartError() {
        const errorElements = document.querySelectorAll('[id$="Error"]');
        errorElements.forEach(element => {
            element.classList.remove('d-none');
        });
    }
});

// Update total reports counter
document.addEventListener("DOMContentLoaded", function() {
    const totalElement = document.getElementById('totalReports');
    if (totalElement) {
        const submitted = {{ $submittedCount ?? 0 }};
        const underReview = {{ $underReviewCount ?? 0 }};
        const resolved = {{ $resolvedCount ?? 0 }};
        const closed = {{ $closedCount ?? 0 }};
        
        totalElement.textContent = submitted + underReview + resolved + closed;
    }
});
</script>
@endpush