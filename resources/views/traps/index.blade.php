@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Smart Traps</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrapModal">
                        Add New Trap
                    </button>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach($traps as $trap)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ $trap->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Location:</strong> {{ $trap->location }}</p>
                                    <p><strong>Type:</strong> {{ $trap->type }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $trap->status === 'active' ? 'success' : ($trap->status === 'maintenance' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($trap->status) }}
                                        </span>
                                    </p>
                                    
                                    <!-- Battery Status -->
                                    <div class="mb-3">
                                        <label class="form-label">Battery Level</label>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $trap->battery_status['color'] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $trap->battery_level }}%"
                                                 aria-valuenow="{{ $trap->battery_level }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $trap->battery_level }}%
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Storage Status -->
                                    <div class="mb-3">
                                        <label class="form-label">Storage Volume</label>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $trap->storage_status['color'] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($trap->storage_volume / $trap->storage_threshold) * 100 }}%"
                                                 aria-valuenow="{{ $trap->storage_volume }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $trap->storage_threshold }}">
                                                {{ $trap->storage_volume }}kg / {{ $trap->storage_threshold }}kg
                                            </div>
                                        </div>
                                    </div>

                                    @if($trap->last_maintenance)
                                    <p><strong>Last Maintenance:</strong> {{ $trap->last_maintenance->format('M d, Y H:i') }}</p>
                                    @endif

                                    @if($trap->notes)
                                    <p><strong>Notes:</strong> {{ $trap->notes }}</p>
                                    @endif

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editTrapModal{{ $trap->id }}">
                                            Edit
                                        </button>
                                        @if($trap->status === 'maintenance')
                                        <form action="{{ route('traps.maintenance', $trap) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Complete Maintenance
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Trap Modal -->
<div class="modal fade" id="addTrapModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('traps.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Trap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control" name="type" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Battery Level (%)</label>
                        <input type="number" class="form-control" name="battery_level" min="0" max="100" value="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Storage Volume (kg)</label>
                        <input type="number" class="form-control" name="storage_volume" min="0" step="0.1" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Storage Threshold (kg)</label>
                        <input type="number" class="form-control" name="storage_threshold" min="0" step="0.1" value="1.0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Trap</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Trap Modals -->
@foreach($traps as $trap)
<div class="modal fade" id="editTrapModal{{ $trap->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('traps.update', $trap) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Trap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $trap->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="{{ $trap->location }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control" name="type" value="{{ $trap->type }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active" {{ $trap->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ $trap->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="inactive" {{ $trap->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Battery Level (%)</label>
                        <input type="number" class="form-control" name="battery_level" min="0" max="100" value="{{ $trap->battery_level }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Storage Volume (kg)</label>
                        <input type="number" class="form-control" name="storage_volume" min="0" step="0.1" value="{{ $trap->storage_volume }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Storage Threshold (kg)</label>
                        <input type="number" class="form-control" name="storage_threshold" min="0" step="0.1" value="{{ $trap->storage_threshold }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes">{{ $trap->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Trap</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection 