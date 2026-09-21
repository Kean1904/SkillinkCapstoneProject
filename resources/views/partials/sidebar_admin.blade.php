<!-- ADMIN SIDEBAR PARTIAL (COMPACT ADMIN STYLE) -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <img src="{{ session('profile_image_uri') ? asset(session('profile_image_uri')) : asset('image/MP_Profile.png') }}" alt="Profile" class="sidebar-avatar" onerror="this.outerHTML='<div class=\'sidebar-avatar-fallback\'><i class=\'fa-solid fa-user-shield\'></i></div>'">
        <p class="name">{{ session('full_name') ?? 'System Administrator' }}</p>
        <p class="role"><span class="role-badge" style="background: #10b981;">VERIFIED ADMINISTRATOR</span></p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.Admin') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.users') }}" class="{{ ($active ?? '') === 'users' ? 'active' : '' }}"><i class="fa-solid fa-users"></i> User Management</a></li>
        <li><a href="{{ route('admin.staff') }}" class="{{ ($active ?? '') === 'staff' ? 'active' : '' }}"><i class="fa-solid fa-user-shield"></i> Staff Management</a></li>
        <li><a href="{{ route('admin.categories') }}" class="{{ ($active ?? '') === 'categories' ? 'active' : '' }}"><i class="fa-solid fa-tags"></i> Job Categories</a></li>
        <li><a href="{{ route('admin.audit_logs') }}" class="{{ ($active ?? '') === 'audit_logs' ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> Audit Logs</a></li>
        <li><a href="{{ route('admin.profile') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="{{ route('admin.settings') }}" class="{{ ($active ?? '') === 'settings' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
@include('partials.idle_timeout')
