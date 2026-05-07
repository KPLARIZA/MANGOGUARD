@extends('layouts.app')

@section('title', $image->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Image and Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $image->image_url }}" class="img-fluid rounded" alt="{{ $image->title }}" style="max-height: 500px;">
                    </div>
                    
                    <h4 class="card-title">{{ $image->title }}</h4>
                    <p class="text-muted">{{ $image->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Pest Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-{{ $image->pest_type === 'unknown' ? 'secondary' : ($image->pest_type === 'cecid_fly' ? 'danger' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $image->pest_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $image->location }}</td>
                                </tr>
                                <tr>
                                    <th>Uploaded:</th>
                                    <td>{{ $image->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Species Details</h5>
                            @if($image->pest_type === 'cecid_fly')
                                <div class="alert alert-danger">
                                    <h6>Cecid Fly Characteristics:</h6>
                                    <ul class="mb-0">
                                        <li>Small, delicate flies (2-3mm)</li>
                                        <li>Long antennae and legs</li>
                                        <li>Forms galls on mango leaves</li>
                                        <li>Active during early morning</li>
                                    </ul>
                                </div>
                            @elseif($image->pest_type === 'fruit_fly')
                                <div class="alert alert-warning">
                                    <h6>Fruit Fly Characteristics:</h6>
                                    <ul class="mb-0">
                                        <li>Medium-sized flies (5-8mm)</li>
                                        <li>Distinctive wing patterns</li>
                                        <li>Attracted to ripening fruits</li>
                                        <li>Active during daylight hours</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <h6>Unknown Species</h6>
                                    <p class="mb-0">This pest has not been identified yet. Please consult with an expert for proper identification.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Tips -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Management Tips</h5>
                </div>
                <div class="card-body">
                    @if($image->pest_type === 'cecid_fly')
                        <div class="alert alert-info">
                            <h6>Control Measures:</h6>
                            <ul class="mb-0">
                                <li>Regular monitoring of new growth</li>
                                <li>Prune and destroy infested leaves</li>
                                <li>Apply systemic insecticides during early infestation</li>
                                <li>Maintain proper tree spacing</li>
                            </ul>
                        </div>
                    @elseif($image->pest_type === 'fruit_fly')
                        <div class="alert alert-info">
                            <h6>Control Measures:</h6>
                            <ul class="mb-0">
                                <li>Use fruit fly traps</li>
                                <li>Bagging of fruits</li>
                                <li>Sanitation of fallen fruits</li>
                                <li>Apply bait sprays</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h6>General Tips:</h6>
                            <ul class="mb-0">
                                <li>Document the pest's behavior</li>
                                <li>Note the time of day observed</li>
                                <li>Record any damage patterns</li>
                                <li>Consult with pest management experts</li>
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
                        <a href="{{ route('gallery.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Gallery
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteImage({{ $image->id }})">
                            <i class="fas fa-trash"></i> Delete Image
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
function deleteImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/gallery/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route('gallery.index') }}';
            }
        });
    }
}
</script>
@endpush 