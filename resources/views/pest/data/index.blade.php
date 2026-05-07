@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pest Monitoring Data</h5>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDataModal">
                            Add New Data
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Real-time Updates Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6>Latest Updates</h6>
                            <div id="latestData" class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Trap</th>
                                            <th>Location</th>
                                            <th>Pest Type</th>
                                            <th>Count</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Historical Data Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Historical Data</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Trap</th>
                                            <th>Pest Type</th>
                                            <th>Count</th>
                                            <th>Recorded At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pestData as $data)
                                        <tr>
                                            <td>{{ $data->trap->name }}</td>
                                            <td>{{ str_replace('_', ' ', $data->pest_type) }}</td>
                                            <td>{{ $data->count }}</td>
                                            <td>{{ $data->recorded_at->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <a href="{{ route('pest.data.show', $data) }}" 
                                                   class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $pestData->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Data Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addDataForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Pest Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Trap</label>
                        <select class="form-control" name="trap_id" required>
                            @foreach(\App\Models\Trap::active()->get() as $trap)
                            <option value="{{ $trap->id }}">{{ $trap->name }} ({{ $trap->location }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pest Type</label>
                        <select class="form-control" name="pest_type" required>
                            <option value="cecid_fly">Cecid Fly</option>
                            <option value="fruit_fly">Fruit Fly</option>
                            <option value="unknown">Unknown</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Count</label>
                        <input type="number" class="form-control" name="count" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Function to update latest data
function updateLatestData() {
    fetch('/pest-data/latest')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#latestData tbody');
            tbody.innerHTML = '';
            
            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.trap_name}</td>
                    <td>${item.location}</td>
                    <td>${item.pest_type.replace('_', ' ')}</td>
                    <td>${item.count}</td>
                    <td>${item.recorded_at}</td>
                `;
                tbody.appendChild(row);
            });
        });
}

// Update data every 30 seconds
setInterval(updateLatestData, 30000);
updateLatestData();

// Handle form submission
document.getElementById('addDataForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/pest-data', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            // Close modal and refresh page
            bootstrap.Modal.getInstance(document.getElementById('addDataModal')).hide();
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the data.');
    });
});
</script>
@endpush
@endsection 