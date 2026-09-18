<div class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <img src="{{ asset('image/MP_Profile.png') }}" alt="Profile" class="sidebar-avatar" onerror="this.outerHTML='<div class=\'sidebar-avatar-fallback\'><i class=\'fa-solid fa-user\'></i></div>'">
        <p class="name">{{ session('full_name') ?? 'Khane Hendrix Torres' }}</p>
        <p class="role"><span class="role-badge" style="background: #10b981;">VERIFIED RESIDENTIAL</span></p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.Residential') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="{{ route('residential.hiring_history') }}" class="{{ ($active ?? '') === 'hiring_history' ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Hiring History</a></li>
        <li><a href="{{ route('residential.saved_workers') }}" class="{{ ($active ?? '') === 'saved_workers' ? 'active' : '' }}"><i class="fa-solid fa-bookmark"></i> Saved Workers</a></li>
        <li><a href="{{ route('residential.job_posts') }}" class="{{ ($active ?? '') === 'job_posts' ? 'active' : '' }}"><i class="fa-solid fa-briefcase"></i> My Job Posts</a></li>
        <li><a href="{{ route('residential.profile') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="{{ route('residential.settings') }}" class="{{ ($active ?? '') === 'settings' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>