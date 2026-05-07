@extends('layouts.app')

@section('title', 'Pest Gallery')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Pest Gallery</h4>
                        <p class="text-muted">Documentation of pest species found in the farm</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload New Image
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
                            <select class="form-select" id="locationFilter">
                                <option value="">All Locations</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by title or description...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary w-100" id="resetFilters">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row" id="galleryGrid">
        @foreach($images as $image)
        <div class="col-md-4 col-lg-3 mb-4 gallery-item" 
             data-pest-type="{{ $image->pest_type }}"
             data-location="{{ $image->location }}"
             data-title="{{ strtolower($image->title) }}"
             data-description="{{ strtolower($image->description) }}">
            <div class="card h-100">
                <img src="{{ $image->image_url }}" class="card-img-top" alt="{{ $image->title }}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $image->title }}</h5>
                    <p class="card-text small text-muted">{{ Str::limit($image->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $image->pest_type === 'unknown' ? 'secondary' : ($image->pest_type === 'cecid_fly' ? 'danger' : 'warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $image->pest_type)) }}
                        </span>
                        <small class="text-muted">{{ $image->location }}</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100">
                        <a href="{{ route('gallery.show', $image) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteImage({{ $image->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $images->links() }}
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload New Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
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
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
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
    const locationFilter = document.getElementById('locationFilter');
    const searchInput = document.getElementById('searchInput');
    const resetButton = document.getElementById('resetFilters');
    const galleryItems = document.querySelectorAll('.gallery-item');

    // Populate location filter
    const locations = new Set();
    galleryItems.forEach(item => locations.add(item.dataset.location));
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location;
        option.textContent = location;
        locationFilter.appendChild(option);
    });

    function filterGallery() {
        const pestType = pestTypeFilter.value.toLowerCase();
        const location = locationFilter.value.toLowerCase();
        const search = searchInput.value.toLowerCase();

        galleryItems.forEach(item => {
            const matchesPestType = !pestType || item.dataset.pestType === pestType;
            const matchesLocation = !location || item.dataset.location === location;
            const matchesSearch = !search || 
                item.dataset.title.includes(search) || 
                item.dataset.description.includes(search);

            item.style.display = matchesPestType && matchesLocation && matchesSearch ? '' : 'none';
        });
    }

    pestTypeFilter.addEventListener('change', filterGallery);
    locationFilter.addEventListener('change', filterGallery);
    searchInput.addEventListener('input', filterGallery);
    resetButton.addEventListener('click', () => {
        pestTypeFilter.value = '';
        locationFilter.value = '';
        searchInput.value = '';
        filterGallery();
    });
});

function deleteImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/gallery/${id}`, {
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