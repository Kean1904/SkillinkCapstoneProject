<div class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <img src="{{ session('profile_image_uri') ? asset(session('profile_image_uri')) : asset('image/MP_Profile.png') }}" alt="Profile" class="sidebar-avatar" onerror="this.outerHTML='<div class=\'sidebar-avatar-fallback\'><i class=\'fa-solid fa-user\'></i></div>'">
        <p class="name">{{ session('full_name') ?? 'Juan Dela Cruz' }}</p>
        <p class="role"><span class="role-badge" style="background: #10b981;">ACCREDITED SKILLED WORKER</span></p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.SkilledWorker') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="{{ route('skilled_worker.tracking_service') }}" class="{{ ($active ?? '') === 'tracking' ? 'active' : '' }}"><i class="fa-solid fa-route"></i> Tracking Service</a></li>
        <li><a href="{{ route('skilled_worker.messages') }}" class="{{ ($active ?? '') === 'messages' ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;"><span style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-comments"></i> Messages</span><span class="sidebar-chat-unread-badge" style="display: none; background: #ef4444; color: white; border-radius: 10px; font-size: 10px; font-weight: bold; padding: 2px 7px;">0</span></a></li>
        <li><a href="{{ route('skilled_worker.declined_bookings') }}" class="{{ ($active ?? '') === 'declined_bookings' ? 'active' : '' }}"><i class="fa-solid fa-calendar-xmark"></i> Declined Bookings</a></li>
        <li><a href="{{ route('skilled_worker.my_services') }}" class="{{ ($active ?? '') === 'services' ? 'active' : '' }}"><i class="fa-solid fa-wrench"></i> My Services</a></li>
        <li><a href="{{ route('skilled_worker.profile') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="{{ route('skilled_worker.settings') }}" class="{{ ($active ?? '') === 'settings' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>