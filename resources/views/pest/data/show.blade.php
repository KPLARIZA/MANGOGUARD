@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pest Data Details</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Trap Information</h6>
                            <p><strong>Name:</strong> {{ $pestData->trap->name }}</p>
                            <p><strong>Location:</strong> {{ $pestData->trap->location }}</p>
                            <p><strong>Type:</strong> {{ $pestData->trap->type }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $pestData->trap->status === 'active' ? 'success' : ($pestData->trap->status === 'maintenance' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pestData->trap->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Pest Information</h6>
                            <p><strong>Type:</strong> {{ str_replace('_', ' ', $pestData->pest_type) }}</p>
                            <p><strong>Count:</strong> {{ $pestData->count }}</p>
                            <p><strong>Recorded At:</strong> {{ $pestData->recorded_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>

                    @if($pestData->trap->pestAlerts->where('pest_type', $pestData->pest_type)->count() > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Related Alerts</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Severity</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pestData->trap->pestAlerts->where('pest_type', $pestData->pest_type) as $alert)
                                        <tr>
                                            <td>{{ $alert->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $alert->severity === 'high' ? 'danger' : ($alert->severity === 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($alert->severity) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $alert->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($alert->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $alert->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('pest.data.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 