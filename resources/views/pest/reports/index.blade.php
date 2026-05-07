@extends('layouts.app')

@section('title', 'Pest Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pest Reports</h1>
        <a href="{{ route('pest-reports.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> New Report
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pest Type</th>
                            <th>Severity</th>
                            <th>Location</th>
                            <th>Reported By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->pest_type }}</td>
                            <td><span class="badge bg-{{ $report->severity === 'high' ? 'danger' : ($report->severity === 'medium' ? 'warning' : 'info') }}">{{ ucfirst($report->severity) }}</span></td>
                            <td>{{ $report->location }}</td>
                            <td>{{ $report->user->name ?? 'N/A' }}</td>
                            <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route('pest-reports.show', $report) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('pest-reports.edit', $report) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('pest-reports.destroy', $report) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No pest reports found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection 