@extends('layouts.app')

@section('title', 'Pest Alerts')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Pest Alerts</h4>
                        <p class="text-muted">Monitor and manage pest alerts across the farm</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAlertModal">
                        <i class="fas fa-plus"></i> Create New Alert
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-select" id="pestTypeFilter">
                                <option value="">All Pest Types</option>
                                <option value="cecid_fly">Cecid Fly</option>
                                <option value="fruit_fly">Fruit Fly</option>
                                <option value="unknown">Unknown Species</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="severityFilter">
                                <option value="">All Severities</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary w-100" id="resetFilters">
                                <i class="fas fa-redo"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Pest Type</th>
                                    <th>Location</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Reported By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alerts as $alert)
                                <tr>
                                    <td>
                                        <a href="{{ route('pest.alerts.show', $alert) }}" class="text-decoration-none">
                                            {{ $alert->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $alert->pest_type === 'unknown' ? 'secondary' : ($alert->pest_type === 'cecid_fly' ? 'danger' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $alert->pest_type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $alert->location }}</td>
                                    <td>
                                        <span class="badge bg-{{ $alert->severity_color }}">
                                            {{ ucfirst($alert->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $alert->status_badge }}">
                                            {{ ucfirst($alert->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $alert->user->name }}</td>
                                    <td>{{ $alert->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('pest.alerts.show', $alert) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAlert({{ $alert->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $alerts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Alert Modal -->
<div class="modal fade" id="createAlertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pest.alerts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pest Type</label>
                        <select class="form-select" name="pest_type" required>
                            <option value="cecid_fly">Cecid Fly</option>
                            <option value="fruit_fly">Fruit Fly</option>
                            <option value="unknown">Unknown Species</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Severity</label>
                        <select class="form-select" name="severity" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active">Active</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Alert</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pestTypeFilter = document.getElementById('pestTypeFilter');
    const severityFilter = document.getElementById('severityFilter');
    const statusFilter = document.getElementById('statusFilter');
    const resetButton = document.getElementById('resetFilters');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterAlerts() {
        const pestType = pestTypeFilter.value.toLowerCase();
        const severity = severityFilter.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const rowPestType = row.querySelector('td:nth-child(2) .badge').textContent.toLowerCase();
            const rowSeverity = row.querySelector('td:nth-child(4) .badge').textContent.toLowerCase();
            const rowStatus = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();

            const matchesPestType = !pestType || rowPestType.includes(pestType);
            const matchesSeverity = !severity || rowSeverity === severity;
            const matchesStatus = !status || rowStatus === status;

            row.style.display = matchesPestType && matchesSeverity && matchesStatus ? '' : 'none';
        });
    }

    pestTypeFilter.addEventListener('change', filterAlerts);
    severityFilter.addEventListener('change', filterAlerts);
    statusFilter.addEventListener('change', filterAlerts);
    resetButton.addEventListener('click', () => {
        pestTypeFilter.value = '';
        severityFilter.value = '';
        statusFilter.value = '';
        filterAlerts();
    });
});

function deleteAlert(id) {
    if (confirm('Are you sure you want to delete this alert?')) {
        fetch(`/pest/alerts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}
</script>
@endpush 