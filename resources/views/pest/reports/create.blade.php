@extends('layouts.app')

@section('title', 'Create Pest Report')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">Create New Pest Report</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pest-reports.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="pest_type" class="form-label">Pest Type</label>
                            <select class="form-select @error('pest_type') is-invalid @enderror" id="pest_type" name="pest_type" required>
                                <option value="">Select Pest Type</option>
                                <option value="Cecid Fly" {{ old('pest_type') == 'Cecid Fly' ? 'selected' : '' }}>Cecid Fly</option>
                                <option value="Fruit Fly" {{ old('pest_type') == 'Fruit Fly' ? 'selected' : '' }}>Fruit Fly</option>
                                <option value="Leaf Hopper" {{ old('pest_type') == 'Leaf Hopper' ? 'selected' : '' }}>Leaf Hopper</option>
                            </select>
                            @error('pest_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="severity" class="form-label">Severity</label>
                            <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                <option value="">Select Severity</option>
                                <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="e.g., Block A, Section 3" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional observations or details">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 