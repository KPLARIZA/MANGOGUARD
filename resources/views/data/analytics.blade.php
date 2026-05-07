@extends('layouts.app')

@section('title', 'Data Analytics')

@section('content')
<div class="container-fluid">
    <!-- Time Period Selector and Export -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-period="day">Daily</button>
                        <button type="button" class="btn btn-outline-primary" data-period="week">Weekly</button>
                        <button type="button" class="btn btn-outline-primary" data-period="month">Monthly</button>
                    </div>
                    <div class="export-buttons">
                        <button class="btn btn-success" onclick="exportData('csv')">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                        <button class="btn btn-danger" onclick="exportData('pdf')">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Total Insects Trapped</h6>
                    <h2 class="mb-0" id="totalInsects">80</h2>
                    <small>Current Period</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Average Daily Catch</h6>
                    <h2 class="mb-0" id="avgDailyCatch">50</h2>
                    <small>Insects per day</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Most Common Pest</h6>
                    <h2 class="mb-0" id="mostCommonPest">-</h2>
                    <small>By volume</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Active Traps</h6>
                    <h2 class="mb-0" id="activeTraps">0</h2>
                    <small>Currently reporting</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts -->
    <div class="row mt-4">
        <!-- Volume Over Time Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Insect Volume Over Time</h5>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleChartType('volumeChart')">
                            <i class="fas fa-chart-line"></i> Toggle View
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="volumeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pest Distribution Chart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pest Distribution</h5>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleChartType('distributionChart')">
                            <i class="fas fa-chart-pie"></i> Toggle View
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="row mt-4">
        <!-- Temperature Correlation -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Temperature vs. Pest Activity</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trap Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Trap Performance Comparison</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="trapPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row mt-4">
        <!-- Daily Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daily Statistics</h5>
                    <div class="table-controls">
                        <input type="text" class="form-control form-control-sm" id="dailyStatsSearch" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="dailyStatsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Insects</th>
                                    <th>Unique Species</th>
                                    <th>Avg. Temperature</th>
                                </tr>
                            </thead>
                            <tbody id="dailyStats">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trap Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Trap Performance</h5>
                    <div class="table-controls">
                        <input type="text" class="form-control form-control-sm" id="trapStatsSearch" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="trapStatsTable">
                            <thead>
                                <tr>
                                    <th>Trap Location</th>
                                    <th>Total Catch</th>
                                    <th>Last Updated</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="trapStats">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let volumeChart, distributionChart, temperatureChart, trapPerformanceChart;
    const ctx = document.getElementById('volumeChart').getContext('2d');
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    const trapCtx = document.getElementById('trapPerformanceChart').getContext('2d');

    // Initialize charts
    function initCharts() {
        // Volume Chart
        volumeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Insects',
                    data: [],
                    borderColor: '#3498db',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Distribution Chart
        distributionChart = new Chart(distCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#e74c3c',
                        '#f1c40f',
                        '#9b59b6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Temperature Chart
        temperatureChart = new Chart(tempCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Temperature vs. Pest Count',
                    data: [],
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Temperature (°C)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Pest Count'
                        }
                    }
                }
            }
        });

        // Trap Performance Chart
        trapPerformanceChart = new Chart(trapCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Catch',
                    data: [],
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Update data based on selected period
    function updateData(period = 'day') {
        fetch(`/data/analytics/${period}`)
            .then(response => response.json())
            .then(data => {
                updateCharts(data);
                updateStatistics(data);
                updateTables(data);
            });
    }

    // Update charts with new data
    function updateCharts(data) {
        // Update volume chart
        volumeChart.data.labels = data.map(item => item.date || item.week || item.month);
        volumeChart.data.datasets[0].data = data.map(item => item.total_pests);
        volumeChart.update();

        // Update distribution chart
        const pestDistribution = data.reduce((acc, curr) => {
            curr.pest_distribution.forEach(pest => {
                acc[pest.name] = (acc[pest.name] || 0) + pest.count;
            });
            return acc;
        }, {});

        distributionChart.data.labels = Object.keys(pestDistribution);
        distributionChart.data.datasets[0].data = Object.values(pestDistribution);
        distributionChart.update();

        // Update temperature chart
        const temperatureData = data.map(item => ({
            x: item.avg_temperature,
            y: item.total_pests
        }));
        temperatureChart.data.datasets[0].data = temperatureData;
        temperatureChart.update();

        // Update trap performance chart
        const trapData = data.trap_performance;
        trapPerformanceChart.data.labels = trapData.map(trap => trap.location);
        trapPerformanceChart.data.datasets[0].data = trapData.map(trap => trap.total_catch);
        trapPerformanceChart.update();
    }

    // Update summary statistics
    function updateStatistics(data) {
        const totalInsects = data.reduce((sum, item) => sum + item.total_pests, 0);
        const avgDaily = totalInsects / data.length;
        const mostCommon = Object.entries(
            data.reduce((acc, curr) => {
                curr.pest_distribution.forEach(pest => {
                    acc[pest.name] = (acc[pest.name] || 0) + pest.count;
                });
                return acc;
            }, {})
        ).sort((a, b) => b[1] - a[1])[0];

        document.getElementById('totalInsects').textContent = totalInsects;
        document.getElementById('avgDailyCatch').textContent = Math.round(avgDaily);
        document.getElementById('mostCommonPest').textContent = mostCommon ? mostCommon[0] : '-';
    }

    // Update tables with detailed statistics
    function updateTables(data) {
        const dailyStatsHtml = data.map(item => `
            <tr>
                <td>${item.date || item.week || item.month}</td>
                <td>${item.total_pests}</td>
                <td>${item.unique_species}</td>
                <td>${item.avg_temperature}°C</td>
            </tr>
        `).join('');

        document.getElementById('dailyStats').innerHTML = dailyStatsHtml;

        const trapStatsHtml = data.trap_performance.map(trap => `
            <tr>
                <td>${trap.location}</td>
                <td>${trap.total_catch}</td>
                <td>${new Date(trap.last_updated).toLocaleString()}</td>
                <td><span class="badge bg-${trap.status === 'active' ? 'success' : 'warning'}">${trap.status}</span></td>
            </tr>
        `).join('');

        document.getElementById('trapStats').innerHTML = trapStatsHtml;
    }

    // Toggle chart type
    function toggleChartType(chartId) {
        const chart = eval(chartId);
        if (chart.config.type === 'line') {
            chart.config.type = 'bar';
        } else if (chart.config.type === 'pie') {
            chart.config.type = 'doughnut';
        } else if (chart.config.type === 'bar') {
            chart.config.type = 'line';
        } else if (chart.config.type === 'doughnut') {
            chart.config.type = 'pie';
        }
        chart.update();
    }

    // Export data
    function exportData(format) {
        const period = document.querySelector('[data-period].active').dataset.period;
        fetch(`/data/analytics/${period}/export/${format}`)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `pest-analytics-${period}-${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            });
    }

    // Initialize
    initCharts();
    updateData();

    // Event listeners for period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.dataset.period;
            updateData(period);
            
            // Update active button
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Search functionality
    document.getElementById('dailyStatsSearch').addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#dailyStatsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    document.getElementById('trapStatsSearch').addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#trapStatsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Auto-refresh every 5 minutes
    setInterval(() => {
        const activePeriod = document.querySelector('[data-period].active').dataset.period;
        updateData(activePeriod);
    }, 300000);
});
</script>
@endpush 