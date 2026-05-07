@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-muted">Here's what's happening with your mango farm today.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-icon-split" onclick="window.location.href='{{ route('pest-reports.create') }}'">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">New Report</span>
            </button>
            <button class="btn btn-info btn-icon-split" onclick="window.location.href='{{ route('dashboard.export') }}'">
                <span class="icon text-white-50">
                    <i class="fas fa-download"></i>
                </span>
                <span class="text">Export Data</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4">
        <!-- Total Reports Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalReports">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="mt-2 text-success text-sm">
                                <i class="fas fa-arrow-up"></i> <span id="reportsTrend">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-primary-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cecid Fly Alerts Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-left-danger h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cecid Fly Alerts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="cecidFlyAlerts">
                                <div class="spinner-border spinner-border-sm text-danger" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="mt-2 text-danger text-sm">
                                <i class="fas fa-exclamation-triangle"></i> <span id="cecidFlyTrend">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bug fa-2x text-danger-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fruit Fly Alerts Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Fruit Fly Alerts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="fruitFlyAlerts">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="mt-2 text-warning text-sm">
                                <i class="fas fa-exclamation-circle"></i> <span id="fruitFlyTrend">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fly fa-2x text-warning-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Harvest Volume Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pest Volume</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalHarvestVolume">
                                <div class="spinner-border spinner-border-sm text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="mt-2 text-success text-sm">
                                <i class="fas fa-chart-line"></i> <span id="harvestTrend">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-success-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4 mt-2">
        <!-- Pest Trend Analysis -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pest Trend Analysis</h6>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                            <input type="radio" name="options" id="weekly" autocomplete="off" checked> Weekly
                        </label>
                        <label class="btn btn-outline-primary">
                            <input type="radio" name="options" id="monthly" autocomplete="off"> Monthly
                        </label>
                        <label class="btn btn-outline-primary">
                            <input type="radio" name="options" id="yearly" autocomplete="off"> Yearly
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="pestTrendChart"></canvas>
                    </div>
                    <div class="mt-3 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Cecid Fly
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Fruit Fly
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Leaf Hopper
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Reports</h6>
                    <a href="#" class="btn btn-link btn-sm text-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <!-- Reports will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Farm Map and Pest Distribution -->
    <div class="row g-4 mt-2">
        <!-- Farm Map -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Farm Map</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <span class="mr-3"><i class="fas fa-circle text-danger"></i> High Risk</span>
                        <span class="mr-3"><i class="fas fa-circle text-warning"></i> Medium Risk</span>
                        <span><i class="fas fa-circle text-success"></i> Low Risk</span>
                    </div>
                    <div class="farm-map-grid">
                        <!-- Farm blocks will be generated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Harvest Statistics -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Harvest Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="harvestChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard stats
    fetchDashboardStats();
    
    // Initialize charts
    initializeCharts();
    
    // Initialize recent reports
    fetchRecentReports();
    
    // Initialize farm map
    initializeFarmMap();
    
    // Add event listeners for chart period buttons
    document.querySelectorAll('[name="options"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateChart(this.id);
        });
    });
});

function fetchDashboardStats() {
    fetch('{{ route("dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalReports').textContent = data.totalReports;
            document.getElementById('cecidFlyAlerts').textContent = data.cecidFlyAlerts;
            document.getElementById('fruitFlyAlerts').textContent = data.fruitFlyAlerts;
            document.getElementById('totalHarvestVolume').textContent = data.totalHarvestVolume + ' tons';
            
            // Update trends
            document.getElementById('reportsTrend').textContent = data.reportsTrend;
            document.getElementById('cecidFlyTrend').textContent = data.cecidFlyTrend;
            document.getElementById('fruitFlyTrend').textContent = data.fruitFlyTrend;
            document.getElementById('harvestTrend').textContent = data.harvestTrend;
        });
}

let pestTrendChart, pestDistributionChart;

function initializeCharts() {
    // Initialize Pest Trend Chart
    const trendCtx = document.getElementById('pestTrendChart').getContext('2d');
    pestTrendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Cecid Fly',
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    data: [],
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Fruit Fly',
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    data: [],
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Leaf Hopper',
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    data: [],
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Initialize Pest Distribution Chart
    const distributionCtx = document.getElementById('pestDistributionChart').getContext('2d');
    pestDistributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cecid Fly', 'Fruit Fly', 'Leaf Hopper'],
            datasets: [{
                data: [30, 50, 20],
                backgroundColor: ['#dc3545', '#ffc107', '#17a2b8'],
                hoverBackgroundColor: ['#c82333', '#e0a800', '#138496'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '80%'
        }
    });

    updateChart('weekly');
}

function updateChart(period) {
    fetch(`{{ route("dashboard.chart") }}/${period}`)
        .then(response => response.json())
        .then(data => {
            const dates = [...new Set(Object.values(data).flatMap(dataset => 
                dataset.map(item => item.date)
            ))].sort();
            
            pestTrendChart.data.labels = dates;
            
            Object.entries(data).forEach(([pestType, values]) => {
                const dataset = pestTrendChart.data.datasets.find(ds => 
                    ds.label.toLowerCase().includes(pestType.toLowerCase())
                );
                if (dataset) {
                    dataset.data = dates.map(date => {
                        const point = values.find(v => v.date === date);
                        return point ? point.count : 0;
                    });
                }
            });
            
            pestTrendChart.update();
        });
}

function fetchRecentReports() {
    fetch('{{ route("dashboard.recent-reports") }}')
        .then(response => response.json())
        .then(reports => {
            const container = document.querySelector('.list-group');
            container.innerHTML = reports.map(report => `
                <a href="/pest-reports/${report.id}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${report.title}</h6>
                        <small class="badge badge-${getSeverityClass(report.severity)}">${report.severity}</small>
                    </div>
                    <p class="mb-1 text-muted">${report.location}</p>
                    <small class="text-gray-500">Reported ${report.time_ago}</small>
                </a>
            `).join('');
        });
}

function initializeFarmMap() {
    const grid = document.querySelector('.farm-map-grid');
    const blocks = ['A', 'B', 'C', 'D'];
    const sections = [1, 2, 3, 4];
    // Example coordinates for each block-section (replace with real ones as needed)
    const blockCoords = {
        'A1': { lat: 14.5995, lng: 120.9842 },
        'A2': { lat: 14.6000, lng: 120.9850 },
        'A3': { lat: 14.6005, lng: 120.9860 },
        'A4': { lat: 14.6010, lng: 120.9870 },
        'B1': { lat: 14.6020, lng: 120.9880 },
        'B2': { lat: 14.6025, lng: 120.9890 },
        'B3': { lat: 14.6030, lng: 120.9900 },
        'B4': { lat: 14.6035, lng: 120.9910 },
        'C1': { lat: 14.6040, lng: 120.9920 },
        'C2': { lat: 14.6045, lng: 120.9930 },
        'C3': { lat: 14.6050, lng: 120.9940 },
        'C4': { lat: 14.6055, lng: 120.9950 },
        'D1': { lat: 14.6060, lng: 120.9960 },
        'D2': { lat: 14.6065, lng: 120.9970 },
        'D3': { lat: 14.6070, lng: 120.9980 },
        'D4': { lat: 14.6075, lng: 120.9990 },
    };
    grid.innerHTML = blocks.map(block =>
        sections.map(section => {
            const risk = Math.random() < 0.3 ? 'danger' : Math.random() < 0.6 ? 'warning' : 'success';
            const blockId = `${block}${section}`;
            const coords = blockCoords[blockId];
            return `<div class="farm-block bg-${risk}" data-block="${blockId}" data-lat="${coords.lat}" data-lng="${coords.lng}">${blockId}</div>`;
        }).join('')
    ).join('');
    // Add click event listeners
    document.querySelectorAll('.farm-block').forEach(block => {
        block.addEventListener('click', function() {
            const blockId = this.getAttribute('data-block');
            const url = `/farms/map/${blockId}`;
            window.location.href = url;
        });
    });
}

function getSeverityClass(severity) {
    switch (severity.toLowerCase()) {
        case 'high': return 'danger';
        case 'medium': return 'warning';
        case 'low': return 'info';
        default: return 'success';
    }
}
</script>

<style>
/* Card Styles */
.card {
    border: none;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.stat-card {
    border-left-width: 4px !important;
}

/* Farm Map Styles */
.farm-map-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 20px;
}

.farm-block {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-size: 1.1rem;
}

.farm-block:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Button Styles */
.btn-icon-split {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 0.35rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-icon-split .icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    margin-right: 0.5rem;
}

.btn-icon-split:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Chart Styles */
.chart-area {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 260px;
    width: 100%;
}

/* List Group Styles */
.list-group-item {
    border-left: none;
    border-right: none;
    padding: 1rem;
    transition: all 0.2s ease;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: #f8f9fc;
    transform: translateX(4px);
}

/* Badge Styles */
.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    border-radius: 0.35rem;
}

/* Color Utilities */
.text-primary-50 { color: rgba(13, 110, 253, 0.5); }
.text-danger-50 { color: rgba(220, 53, 69, 0.5); }
.text-warning-50 { color: rgba(255, 193, 7, 0.5); }
.text-success-50 { color: rgba(25, 135, 84, 0.5); }

.bg-success { background-color: #28a745; }
.bg-warning { background-color: #ffc107; }
.bg-danger { background-color: #dc3545; }

/* Spacing Utilities */
.gap-2 { gap: 0.5rem; }
.g-4 { gap: 1.5rem; }

/* Loading Spinner */
.spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

/* Typography */
.text-xs {
    font-size: 0.75rem;
}

.text-sm {
    font-size: 0.875rem;
}

/* Card Header */
.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* Dropdown Menu */
.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fc;
    transform: translateX(4px);
}

/* Button Group */
.btn-group-toggle .btn {
    border-radius: 0;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn-group-toggle .btn:first-child {
    border-top-left-radius: 0.35rem;
    border-bottom-left-radius: 0.35rem;
}

.btn-group-toggle .btn:last-child {
    border-top-right-radius: 0.35rem;
    border-bottom-right-radius: 0.35rem;
}

/* Container Padding */
.px-4 {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}
</style>
@endsection 