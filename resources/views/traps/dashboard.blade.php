@extends('layouts.app')

@section('title', 'Trap Insects — Detection Log')

@section('content')
<div class="container-fluid px-4 dashboard-page trap-detect-page">
    <div class="row g-4 dashboard-shell">
        @include('partials.dashboard-sidebar')

        <main class="col-12 col-lg-9 col-xl-10 dashboard-main">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
                <div class="flex-grow-1">
                    <h1 class="h3 mb-1">Trap Insect Detections</h1>
                    <p class="text-muted mb-0 small">Live data collection</p>
                </div>
                <div class="d-flex flex-wrap gap-2 w-100 w-lg-auto justify-content-lg-end">
                    <a href="{{ route('traps.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cogs me-1"></i> Manage traps
                    </a>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>

    @if($firestoreError ?? null)
        <div class="alert alert-warning shadow-sm">
            <strong>Firestore:</strong> {{ $firestoreError }}
            <div class="small mt-2 mb-0">
                Ensure MySQL is running, Firebase credentials are valid, and the collection exists. Override name with <code>FIRESTORE_DETECTED_LOGS_COLLECTION</code> in <code>.env</code> if your collection differs.
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 trap-stat-card">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Total detections</div>
                    <div class="display-6 fw-bold text-primary">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 trap-stat-card">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Last 24 hours</div>
                    <div class="display-6 fw-bold text-success">{{ $stats['last_24h'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 trap-stat-card trap-stat-accent">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold">Documents loaded</div>
                    <div class="display-6 fw-bold">{{ count($logs) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-primary">Detected logs</span>
            <span class="small text-muted">{{ count($columnKeys) }} field types found</span>
        </div>
        <div class="card-body p-0">
            @if(empty($logs))
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-spider fa-3x mb-3 opacity-50"></i>
                    <p class="mb-0">No documents in <code>detectedLogs</code> yet, or Firestore could not be reached.</p>
                </div>
            @else
                <div class="table-responsive">
                    @php
                        $showKeys = array_slice($dynamicKeys ?? $columnKeys, 0, 8);
                        $trapCandidates = ['trapId', 'trap_id', 'trapID'];
                        $weightCandidates = ['weight', 'Weight', 'wt'];
                    @endphp
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Document ID</th>
                                <th scope="col">Trap ID</th>
                                <th scope="col">Weight</th>
                                @foreach($showKeys as $key)
                                    <th scope="col">{{ $key }}</th>
                                @endforeach
                                @if(count($dynamicKeys ?? $columnKeys) > count($showKeys))
                                    <th scope="col">…</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $row)
                                @php
                                    $fields = $row['fields'] ?? [];
                                    $trapVal = \App\Services\DetectedLogsService::pickField($fields, $trapCandidates);
                                    $weightVal = \App\Services\DetectedLogsService::pickField($fields, $weightCandidates);
                                @endphp
                                <tr>
                                    <td><code class="small">{{ $row['id'] }}</code></td>
                                    <td class="small fw-semibold">
                                        @if($trapVal !== null && !is_array($trapVal))
                                            {{ \Illuminate\Support\Str::limit((string) $trapVal, 80) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if($weightVal !== null && !is_array($weightVal))
                                            @if(is_numeric($weightVal))
                                                {{ $weightVal }}
                                            @else
                                                {{ \Illuminate\Support\Str::limit((string) $weightVal, 40) }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    @foreach($showKeys as $key)
                                        <td class="small">
                                            @php $v = $fields[$key] ?? null; @endphp
                                            @if(is_array($v))
                                                <pre class="mb-0 trap-json-snippet">{{ json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @elseif(is_bool($v))
                                                {{ $v ? 'true' : 'false' }}
                                            @else
                                                {{ $v !== null ? \Illuminate\Support\Str::limit((string) $v, 120) : '—' }}
                                            @endif
                                        </td>
                                    @endforeach
                                    @if(count($dynamicKeys ?? $columnKeys) > count($showKeys))
                                        <td class="text-muted small">extra fields</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
        </main>
    </div>
</div>

<style>
/* Align with main dashboard sidebar layout */
.dashboard-page {
    padding-top: 1rem;
    padding-bottom: 1.25rem;
}
.dashboard-shell {
    align-items: flex-start;
}
.btn-icon-split {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 0.35rem;
    transition: all 0.3s ease;
    font-weight: 500;
}
.btn-icon-split .icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    margin-right: 0.5rem;
}
.dashboard-sidebar {
    position: static;
    border: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
}
.dashboard-sidebar .card-header {
    border-bottom: 1px solid #eef2f7;
}
.dashboard-sidebar .card-body {
    padding: 0.65rem !important;
}
.sidebar-action {
    border-radius: 0.65rem;
    font-weight: 600;
}
.sidebar-action-active {
    box-shadow: inset 0 0 0 2px rgba(13, 110, 253, 0.35), 0 0.25rem 0.75rem rgba(255, 193, 7, 0.35);
}
.dashboard-main > .d-flex:first-child {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 0.85rem;
    padding: 1rem 1rem 1.05rem;
    box-shadow: 0 0.2rem 1rem rgba(17, 24, 39, 0.05);
}
@media (min-width: 992px) {
    .dashboard-sidebar {
        position: sticky;
        top: 1rem;
    }
}
.sidebar-link {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.7rem 0.75rem;
    border-radius: 0.65rem;
    color: #4b5563;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease, transform 0.2s ease;
}
.sidebar-link:hover {
    background-color: #f3f6fb;
    color: #0d6efd;
    transform: translateX(2px);
}
.sidebar-link.active {
    background: linear-gradient(90deg, rgba(13, 110, 253, 0.16), rgba(13, 110, 253, 0.05));
    color: #0d6efd;
    box-shadow: inset 3px 0 0 #0d6efd;
}
@media (max-width: 991.98px) {
    .dashboard-main > .d-flex:first-child {
        padding: 0.85rem;
    }
}

.trap-detect-page .trap-stat-card {
    border-radius: 0.85rem;
    border: 1px solid #eef2f7 !important;
}
.trap-detect-page .trap-stat-accent {
    background: linear-gradient(135deg, #fff9ed 0%, #ffffff 100%);
}
.trap-json-snippet {
    font-size: 0.7rem;
    white-space: pre-wrap;
    max-width: 220px;
    max-height: 120px;
    overflow: auto;
    margin: 0;
    background: #f8f9fc;
    padding: 0.35rem;
    border-radius: 0.35rem;
}
</style>
@endsection
