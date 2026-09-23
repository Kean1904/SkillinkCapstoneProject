<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Admin User Management</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        .header {
            position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
            background-color: #0033a0; color: white; display: flex;
            align-items: center; justify-content: space-between; padding: 10px 25px;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .menu-icon { font-size: 20px; cursor: pointer; }
        .header-left img.logo { width: 42px; height: 42px; border-radius: 50%; }
        .header-left h1 { font-size: 18px; }
        .header-left p { font-size: 11px; }

        .page-content { padding: 80px 25px 40px 25px; position: relative; min-height: 100vh; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 25, 70, 0.75); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1250px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .filter-container { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin: 15px 0; }
        .filter-btn {
            background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3);
            color: white; padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: bold;
            cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .filter-btn:hover, .filter-btn.active {
            background: #2563eb; border-color: #60a5fa; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.5);
        }
        .filter-badge {
            background: rgba(0, 0, 0, 0.3); padding: 2px 8px; border-radius: 12px; font-size: 11px;
        }

        .search-input {
            padding: 9px 15px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15); color: white; outline: none; font-size: 13px; min-width: 250px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
        th { background: rgba(0, 51, 160, 0.6); color: #93c5fd; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        tr:hover { background: rgba(255, 255, 255, 0.05); }

        .role-pill { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .role-skilled { background: rgba(2, 132, 199, 0.3); color: #7dd3fc; border: 1px solid #0284c7; }
        .role-residential { background: rgba(16, 185, 129, 0.3); color: #a7f3d0; border: 1px solid #10b981; }
        .role-staff { background: rgba(139, 92, 246, 0.3); color: #ddd6fe; border: 1px solid #8b5cf6; }
        .role-admin { background: rgba(239, 68, 68, 0.3); color: #fca5a5; border: 1px solid #ef4444; }

        /* STANDARDIZED COMPACT SIDEBAR (ADMIN-STYLE PROPORTIONS) */
        .sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 260px;
            height: 100vh;
            background: #0033a0;
            z-index: 2000;
            transition: left 0.3s ease;
            padding-top: 65px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.35);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar.active {
            left: 0;
        }

        /* Compact Header with 54px Avatar (Fits all screens cleanly) */
        .sidebar-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 15px 10px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .sidebar-avatar, .sidebar-profile img {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: white;
            padding: 3px;
            margin-bottom: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            object-fit: cover;
        }

        .sidebar-avatar-fallback {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            color: white;
            font-size: 24px;
        }

        .sidebar-profile .name {
            color: white;
            font-weight: bold;
            font-size: 13.5px;
            text-align: center;
            line-height: 1.3;
        }

        .sidebar-profile .role {
            margin-top: 3px;
            text-align: center;
        }

        .role-badge {
            background: #10b981;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* Compact Menu Items */
        .sidebar-menu {
            list-style: none;
            padding: 2px 0;
            margin: 0;
            flex: 1 0 auto;
        }

        .sidebar-menu li {
            padding: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 22px;
            color: white;
            text-decoration: none;
            font-size: 13.5px;
            transition: background 0.2s ease;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.18);
            font-weight: bold;
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 14px;
            color: white;
        }

        /* Bottom Pinned Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 10px 22px 18px 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            flex-shrink: 0;
        }

        .sidebar-footer .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: bold;
            transition: opacity 0.2s ease;
            color: lightcoral;
        }

        .sidebar-footer .logout-btn:hover {
            opacity: 0.8;
        }

        .sidebar-footer .logout-btn i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.45);
            z-index: 1500;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga &bull; Municipal Administrator</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.Admin') }}" class="back-link"><i class="fa-solid fa-house"></i> Admin Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_admin', ['active' => 'users'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-users"></i> CITIZEN & USER DIRECTORY MANAGEMENT</span>
                <a href="{{ route('dashboard.Admin') }}" class="back-link">&larr; Back to Admin Dashboard</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-id-card-clip"></i> Master Citizen List & Role Segregation</h3>
                <p style="font-size: 13px; opacity: 0.85;">I-filter ang mga gumagamit batay sa kanilang tungkulin (Skilled Worker, Residential, PESO Staff, Admin).</p>
                <hr>

                <!-- ROLE FILTER BUTTONS & LIVE SEARCH -->
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 15px;">
                    <div class="filter-container">
                        <button type="button" class="filter-btn active" onclick="filterRole('ALL', this)">
                            <i class="fa-solid fa-users"></i> ALL <span class="filter-badge">{{ count($users ?? []) }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('SKILLED', this)">
                            <i class="fa-solid fa-screwdriver-wrench"></i> SKILLED WORKERS <span class="filter-badge">{{ $users->filter(fn($u) => str_contains(strtolower($u->role), 'skilled'))->count() }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('RESIDENTIAL', this)">
                            <i class="fa-solid fa-house-user"></i> RESIDENTIAL <span class="filter-badge">{{ $users->filter(fn($u) => str_contains(strtolower($u->role), 'resident'))->count() }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('STAFF', this)">
                            <i class="fa-solid fa-building-user"></i> PESO STAFF <span class="filter-badge">{{ $users->filter(fn($u) => str_contains(strtolower($u->role), 'staff'))->count() }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('ADMIN', this)">
                            <i class="fa-solid fa-shield-halved"></i> ADMIN <span class="filter-badge">{{ $users->filter(fn($u) => str_contains(strtolower($u->role), 'admin'))->count() }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('ACTIVE', this)">
                            <i class="fa-solid fa-circle" style="color: #4ade80; font-size: 10px;"></i> ACTIVE <span class="filter-badge">{{ $users->filter(fn($u) => $u->is_online)->count() }}</span>
                        </button>
                        <button type="button" class="filter-btn" onclick="filterRole('OFFLINE', this)">
                            <i class="fa-regular fa-circle" style="color: #94a3b8; font-size: 10px;"></i> OFFLINE <span class="filter-badge">{{ $users->filter(fn($u) => !$u->is_online)->count() }}</span>
                        </button>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search name, role, barangay, skills..." onkeyup="filterSearch()" onkeypress="if(event.key === 'Enter') filterSearch();">
                        <button type="button" class="btn-search" onclick="filterSearch()" style="background: #2563eb; color: white; border: none; padding: 9px 18px; border-radius: 20px; font-size: 13px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37,99,235,0.4); transition: all 0.2s;">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                    </div>
                </div>

                <!-- USER TABLE -->
                <div style="overflow-x: auto;">
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>Citizen Name / Username</th>
                                <th>System Role</th>
                                <th>Activity Status</th>
                                <th>Account Created</th>
                                <th>Barangay & Address</th>
                                <th>Contact Number</th>
                                <th>Trade Skills / Cert</th>
                                <th>Accreditation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($users) && count($users) > 0)
                                @foreach($users as $u)
                                    @php
                                        $r = strtolower($u->role);
                                        $roleClass = 'role-residential';
                                        if (str_contains($r, 'skilled')) $roleClass = 'role-skilled';
                                        elseif (str_contains($r, 'staff')) $roleClass = 'role-staff';
                                        elseif (str_contains($r, 'admin')) $roleClass = 'role-admin';
                                    @endphp
                                    <tr class="user-row" data-role="{{ strtoupper($u->role) }}" data-online="{{ $u->is_online ? '1' : '0' }}" data-search="{{ strtolower($u->full_name . ' ' . $u->name . ' ' . $u->role . ' ' . $u->barangay . ' ' . ($u->skills ?? '') . ' ' . ($u->certificate_proof ?? '')) }}">
                                        <td>
                                            <strong>{{ $u->full_name }}</strong>
                                            <div style="font-size: 11px; opacity: 0.75;">@ {{ $u->name }} &bull; {{ $u->email }}</div>
                                        </td>
                                        <td>
                                            <span class="role-pill {{ $roleClass }}">{{ $u->role_display }}</span>
                                        </td>
                                        <td>
                                            @if($u->is_online)
                                                <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #4ade80; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981;"></span> Active
                                                </span>
                                                <div style="font-size: 10px; color: #86efac; margin-top: 3px;">Active now</div>
                                            @else
                                                <span style="background: rgba(148, 163, 184, 0.15); border: 1px solid rgba(148, 163, 184, 0.35); color: #cbd5e1; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;"></span> Offline
                                                </span>
                                                <div style="font-size: 10px; opacity: 0.65; margin-top: 3px;">{{ $u->last_seen_display }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #ffffff; font-size: 12px; white-space: nowrap;">
                                                <i class="fa-regular fa-calendar-check" style="color: #93c5fd; margin-right: 4px;"></i> {{ $u->created_at_display }}
                                            </div>
                                            <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">
                                                {{ $u->created_at ? $u->created_at->diffForHumans() : ($u->date_created ? \Carbon\Carbon::parse($u->date_created)->diffForHumans() : '') }}
                                            </div>
                                        </td>
                                        <td>{{ $u->barangay }}</td>
                                        <td>{{ $u->contact_number }}</td>
                                        <td>
                                            @if(!empty($u->skills))
                                                <span style="color: #93c5fd; font-size: 12px;"><i class="fa-solid fa-wrench"></i> {{ $u->skills }}</span>
                                                @if(!empty($u->certificate_proof))
                                                    <div style="font-size: 10px; color: #86efac;"><i class="fa-solid fa-certificate"></i> {{ $u->certificate_proof }}</div>
                                                @endif
                                            @else
                                                <span style="opacity: 0.5;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($u->is_verified)
                                                <span style="color: #4ade80; font-size: 11px; font-weight: bold;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                            @else
                                                <span style="color: #facc15; font-size: 11px;"><i class="fa-solid fa-clock"></i> Unverified</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        let currentRoleFilter = 'ALL';

        function filterRole(role, btn) {
            currentRoleFilter = role;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilters();
        }

        function filterSearch() {
            applyFilters();
        }

        function applyFilters() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.user-row');

            rows.forEach(row => {
                const userRole = row.getAttribute('data-role');
                const isOnline = row.getAttribute('data-online') === '1';
                const searchData = row.getAttribute('data-search');

                let matchesRole = false;
                if (currentRoleFilter === 'ALL') {
                    matchesRole = true;
                } else if (currentRoleFilter === 'SKILLED' && userRole.includes('SKILLED')) {
                    matchesRole = true;
                } else if (currentRoleFilter === 'RESIDENTIAL' && userRole.includes('RESIDENT')) {
                    matchesRole = true;
                } else if (currentRoleFilter === 'STAFF' && (userRole.includes('STAFF') || userRole.includes('PESO'))) {
                    matchesRole = true;
                } else if (currentRoleFilter === 'ADMIN' && userRole.includes('ADMIN')) {
                    matchesRole = true;
                } else if (currentRoleFilter === 'ACTIVE' && isOnline) {
                    matchesRole = true;
                } else if (currentRoleFilter === 'OFFLINE' && !isOnline) {
                    matchesRole = true;
                }

                const matchesQuery = query === '' || searchData.includes(query);

                if (matchesRole && matchesQuery) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
