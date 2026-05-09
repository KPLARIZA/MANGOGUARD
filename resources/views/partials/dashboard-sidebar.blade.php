<aside class="col-12 col-lg-3 col-xl-2">
    <div class="card shadow-sm dashboard-sidebar">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dashboard Menu</h6>
        </div>
        <div class="card-body p-2">
            <a href="{{ route('traps.dashboard') }}" class="btn btn-warning text-dark btn-icon-split text-decoration-none d-flex align-items-center w-100 mb-2 sidebar-action {{ request()->routeIs('traps.dashboard') ? 'sidebar-action-active' : '' }}">
                <span class="icon text-body-secondary opacity-75">
                    <i class="fas fa-spider" aria-hidden="true"></i>
                </span>
                <span class="text">Trap Insects</span>
            </a>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line me-2" aria-hidden="true"></i> Dashboard Overview
            </a>
            <a href="{{ route('pest-reports.index') }}" class="sidebar-link {{ request()->routeIs('pest-reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt me-2" aria-hidden="true"></i> Pest Reports
            </a>
        </div>
    </div>
</aside>
