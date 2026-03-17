<div class="text-uppercase small fw-bold sidebar-label px-2 mb-2">Main Menu</div>
<nav class="nav flex-column gap-2 mb-4">
    <a href="{{ route('panel.dashboard') }}" class="nav-link {{ request()->routeIs('panel.dashboard') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-house"></i>
        </span>
        <span class="fw-semibold">Dashboard</span>
    </a>
    <a href="{{ route('panel.appointments') }}" class="nav-link {{ request()->routeIs('panel.appointments') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-calendar-check"></i>
        </span>
        <span class="fw-semibold">Appointments</span>
    </a>
    <a href="{{ route('panel.services') }}" class="nav-link {{ request()->routeIs('panel.services') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-car-side"></i>
        </span>
        <span class="fw-semibold">Services & Prices</span>
    </a>
    <a href="{{ route('panel.sales') }}" class="nav-link {{ request()->routeIs('panel.sales') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-chart-line"></i>
        </span>
        <span class="fw-semibold">Sales Reports</span>
    </a>
</nav>

<div class="text-uppercase small fw-bold sidebar-label px-2 mb-2">Management</div>
<nav class="nav flex-column gap-2">
    <a href="{{ route('panel.inventory') }}" class="nav-link {{ request()->routeIs('panel.inventory') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-boxes-stacked"></i>
        </span>
        <span class="fw-semibold">Inventory</span>
    </a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('panel.users') }}" class="nav-link {{ request()->routeIs('panel.users') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-users"></i>
        </span>
        <span class="fw-semibold">Users</span>
    </a>
    @endif
    <a href="{{ route('panel.notifications') }}" class="nav-link {{ request()->routeIs('panel.notifications') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-bell"></i>
        </span>
        <span class="fw-semibold">Notifications</span>
    </a>
    <a href="{{ route('panel.settings') }}" class="nav-link {{ request()->routeIs('panel.settings') ? 'active' : '' }} d-flex align-items-center gap-3 rounded-4 px-3 py-3">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-gear"></i>
        </span>
        <span class="fw-semibold">Settings</span>
    </a>
    <a href="#" class="nav-link d-flex align-items-center gap-3 rounded-4 px-3 py-3"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <span class="nav-icon rounded-4 d-inline-flex align-items-center justify-content-center">
            <i class="fa-solid fa-right-from-bracket"></i>
        </span>
        <span class="fw-semibold">Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
