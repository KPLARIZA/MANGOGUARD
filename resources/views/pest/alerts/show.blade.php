@extends('layouts.app')

@section('title', $alert->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Alert Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-1">{{ $alert->title }}</h4>
                            <p class="text-muted mb-0">Reported by {{ $alert->user->name }} on {{ $alert->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $alert->pest_type === 'unknown' ? 'secondary' : ($alert->pest_type === 'cecid_fly' ? 'danger' : 'warning') }} me-2">
                                {{ ucfirst(str_replace('_', ' ', $alert->pest_type)) }}
                            </span>
                            <span class="badge bg-{{ $alert->severity_color }} me-2">
                                {{ ucfirst($alert->severity) }} Severity
                            </span>
                            <span class="badge bg-{{ $alert->status_badge }}">
                                {{ ucfirst($alert->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Location</h5>
                            <p class="mb-0">{{ $alert->location }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Description</h5>
                            <p class="mb-0">{{ $alert->description }}</p>
                        </div>
                    </div>

                    @if($alert->pest_type === 'cecid_fly')
                        <div class="alert alert-danger">
                            <h6>Cecid Fly Alert Information:</h6>
                            <ul class="mb-0">
                                <li>Check for galls on new growth</li>
                                <li>Monitor early morning activity</li>
                                <li>Inspect surrounding trees</li>
                                <li>Document infestation patterns</li>
                            </ul>
                        </div>
                    @elseif($alert->pest_type === 'fruit_fly')
                        <div class="alert alert-warning">
                            <h6>Fruit Fly Alert Information:</h6>
                            <ul class="mb-0">
                                <li>Check fruit damage</li>
                                <li>Monitor trap catches</li>
                                <li>Inspect ripening fruits</li>
                                <li>Document population density</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <h6>Unknown Species Alert Information:</h6>
                            <ul class="mb-0">
                                <li>Document physical characteristics</li>
                                <li>Note behavior patterns</li>
                                <li>Record damage symptoms</li>
                                <li>Collect samples if possible</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Management Actions -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Management Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pest.alerts.update', $alert) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="active" {{ $alert->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="resolved" {{ $alert->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                    </form>

                    @if($alert->pest_type === 'cecid_fly')
                        <div class="alert alert-info">
                            <h6>Recommended Actions:</h6>
                            <ul class="mb-0">
                                <li>Apply systemic insecticide</li>
                                <li>Prune affected areas</li>
                                <li>Monitor new growth</li>
                                <li>Maintain tree health</li>
                            </ul>
                        </div>
                    @elseif($alert->pest_type === 'fruit_fly')
                        <div class="alert alert-info">
                            <h6>Recommended Actions:</h6>
                            <ul class="mb-0">
                                <li>Deploy additional traps</li>
                                <li>Apply bait sprays</li>
                                <li>Bag ripening fruits</li>
                                <li>Remove fallen fruits</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h6>Recommended Actions:</h6>
                            <ul class="mb-0">
                                <li>Consult with experts</li>
                                <li>Document observations</li>
                                <li>Monitor affected area</li>
                                <li>Collect samples for identification</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pest.alerts') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Alerts
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteAlert({{ $alert->id }})">
                            <i class="fas fa-trash"></i> Delete Alert
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteAlert(id) {
    if (confirm('Are you sure you want to delete this alert?')) {
        fetch(`/pest/alerts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route('pest.alerts') }}';
            }
        });
    }
}
</script>
@endpush 