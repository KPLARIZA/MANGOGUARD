@extends('layouts.app')

@section('title', $farm->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $farm->name }}</h1>
        <a href="{{ route('farms.index') }}" class="btn btn-secondary">Back to Farms</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Upload Farm Image</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('farms.images.upload', $farm->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" required accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-info">Upload</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Farm Image Gallery</h5>
        </div>
        <div class="card-body">
            @if($farm->images->count())
                <div class="row g-3">
                    @foreach($farm->images as $image)
                        <div class="col-md-3 col-6">
                            <div class="card h-100">
                                <img src="{{ asset('storage/' . $image->image) }}" class="card-img-top" alt="Farm Image">
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No images uploaded yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection 