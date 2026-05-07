@extends('layouts.app')

@section('title', 'Farm Block Map')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Block {{ $block }} Location</h1>
        <a href="{{ route('farms.index') }}" class="btn btn-secondary">Back to Farms</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="farmMap" style="height: 500px; width: 100%;"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('farmMap').setView([{{ $lat }}, {{ $lng }}], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        var marker = L.marker([{{ $lat }}, {{ $lng }}]).addTo(map);
        marker.bindPopup('<b>Block {{ $block }}</b><br>Davao del Sur').openPopup();
    });
</script>
@endsection 