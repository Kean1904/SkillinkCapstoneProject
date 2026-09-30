<!-- PESO STAFF SIDEBAR PARTIAL (COMPACT ADMIN STYLE) -->
<style>
    #sidebar.sidebar {
        width: 260px !important;
        max-width: 260px !important;
        overflow-x: hidden !important;
    }
    #sidebar .sidebar-profile img.sidebar-avatar,
    #sidebar .sidebar-avatar {
        width: 54px !important;
        height: 54px !important;
        max-width: 54px !important;
        max-height: 54px !important;
        min-width: 54px !important;
        min-height: 54px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        display: block !important;
        margin: 0 auto 6px auto !important;
    }
    #sidebar .sidebar-avatar-fallback {
        width: 54px !important;
        height: 54px !important;
        max-width: 54px !important;
        max-height: 54px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 6px auto !important;
    }
</style>
<div class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <img src="{{ session('profile_image_uri') ? asset(session('profile_image_uri')) : asset('image/MP_Profile.png') }}" alt="Profile" class="sidebar-avatar" style="width: 54px; height: 54px; max-width: 54px; max-height: 54px; border-radius: 50%; object-fit: cover;" onerror="this.outerHTML='<div class=\'sidebar-avatar-fallback\' style=\'width:54px;height:54px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 6px auto;color:white;\'><i class=\'fa-solid fa-user\'></i></div>'">
        <p class="name">{{ session('full_name') ?? 'PESO Officer' }}</p>
        <p class="role"><span class="role-badge" style="background: #10b981;">VERIFIED PESO OFFICIAL</span></p>
    </div>

    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.PesoStaff') }}" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="{{ route('peso_staff.users') }}" class="{{ ($active ?? '') === 'users' ? 'active' : '' }}"><i class="fa-solid fa-users"></i> User Management</a></li>
        <li><a href="{{ route('peso_staff.accreditation') }}" class="{{ ($active ?? '') === 'accreditation' ? 'active' : '' }}"><i class="fa-solid fa-certificate"></i> Worker Accreditation</a></li>
        <li><a href="{{ route('peso_staff.job_tracking') }}" class="{{ ($active ?? '') === 'job_tracking' ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i> Job Tracking</a></li>
        <li><a href="{{ route('peso_staff.complaints') }}" class="{{ ($active ?? '') === 'complaints' ? 'active' : '' }}"><i class="fa-solid fa-triangle-exclamation"></i> Complaints</a></li>
        <li><a href="{{ route('peso_staff.reports') }}" class="{{ ($active ?? '') === 'reports' ? 'active' : '' }}"><i class="fa-solid fa-certificate"></i> TESDA Reports</a></li>
        <li><a href="{{ route('peso_staff.announcements') }}" class="{{ ($active ?? '') === 'announcements' ? 'active' : '' }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
        <li><a href="{{ route('peso_staff.profile') }}" class="{{ ($active ?? '') === 'profile' ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="{{ route('peso_staff.settings') }}" class="{{ ($active ?? '') === 'settings' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
@include('partials.idle_timeout')
