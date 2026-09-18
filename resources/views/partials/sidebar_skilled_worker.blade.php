<div class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <img src="{{ asset('image/MP_Profile.png') }}" alt="Profile" class="sidebar-avatar" onerror="this.outerHTML='<div class=\'sidebar-avatar-fallback\'><i class=\'fa-solid fa-user\'></i></div>'">
        <p class="name">{{ session('full_name') ?? 'Juan Dela Cruz' }}</p>
        <p class="role"><span class="role-badge" style="background: #10b981;">ACCREDITED SKILLED WORKER</span></p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.SkilledWorker') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="{{ route('skilled_worker.tracking_service') }}" class="{{ ($active ?? '') === 'tracking' ? 'active' : '' }}"><i class="fa-solid fa-route"></i> Tracking Service</a></li>
        <li><a href="{{ route('skilled_worker.my_services') }}" class="{{ ($active ?? '') === 'services' ? 'active' : '' }}"><i class="fa-solid fa-wrench"></i> My Services</a></li>
        <li><a href="{{ route('skilled_worker.profile') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="{{ route('skilled_worker.settings') }}" class="{{ ($active ?? '') === 'settings' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>