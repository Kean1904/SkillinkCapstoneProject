<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKILLINK - Administrator Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #0033a0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-icon {
            font-size: 22px;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .menu-icon:hover {
            background: rgba(255,255,255,0.15);
        }

        .header-left img.logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .header-left h1 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .header-left p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-badge {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #6ee7b7;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

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

        /* PAGE CONTENT */
        .page-content {
            padding: 85px 25px 50px 25px;
            position: relative;
            min-height: 100vh;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 35, 90, 0.65);
            backdrop-filter: blur(2px);
            z-index: 0;
            pointer-events: none;
        }

        .page-inner {
            position: relative;
            z-index: 2;
            max-width: 1240px;
            margin: 0 auto;
        }

        .hero-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px 25px;
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        .hero-title h2 {
            color: white;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .hero-title p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            margin-top: 4px;
        }

        /* 6 STATS CARDS (Matching Mobile 2x3 Grid, 3x2 on Web) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 550px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: rgba(15, 45, 110, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 18px 22px;
            color: white;
            backdrop-filter: blur(8px);
            transition: transform 0.2s, border-color 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.45);
        }

        .stat-card .label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-card .value-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: white;
        }

        .stat-card .stat-icon {
            font-size: 26px;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
        }

        /* SECTION TITLE */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 25px 0 15px 0;
            color: white;
        }

        .section-header h3 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* CHARTS / PROGRESS CARDS */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        @media (max-width: 800px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: rgba(15, 45, 110, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 20px 24px;
            color: white;
            backdrop-filter: blur(8px);
        }

        .chart-card .title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .chart-card .title {
            font-size: 14px;
            font-weight: 700;
            color: #93c5fd;
        }

        .chart-card .subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .progress-track {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* LEADERBOARD RANKING CARDS */
        .leaderboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 800px) {
            .leaderboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .ranking-card {
            background: rgba(15, 45, 110, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 20px 22px;
            color: white;
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
        }

        .ranking-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: #93c5fd;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ranking-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .ranking-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .ranking-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rank-badge {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rank-badge.top-1 { background: #f59e0b; color: black; }
        .rank-badge.top-2 { background: #94a3b8; color: black; }
        .rank-badge.top-3 { background: #b45309; color: white; }

        .ranking-title {
            font-size: 13px;
            font-weight: 600;
        }

        .ranking-subtitle {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.65);
        }

        .ranking-card .action-btn {
            margin-top: 14px;
            text-align: right;
        }

        .ranking-card .action-btn a {
            color: #60a5fa;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ranking-card .action-btn a:hover {
            text-decoration: underline;
        }

        /* ADMINISTRATIVE MODULES SHORTCUT GRID */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        @media (max-width: 1000px) {
            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 550px) {
            .modules-grid {
                grid-template-columns: 1fr;
            }
        }

        .module-shortcut {
            background: rgba(15, 45, 110, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 18px 20px;
            color: white;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }

        .module-shortcut:hover {
            transform: translateY(-4px);
            background: rgba(25, 60, 140, 0.8);
            border-color: #60a5fa;
        }

        .module-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .module-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #93c5fd;
        }

        .module-name {
            font-size: 14px;
            font-weight: bold;
            color: white;
        }

        .module-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.4;
            margin-bottom: 14px;
        }

        .module-link {
            font-size: 11.5px;
            font-weight: bold;
            color: #60a5fa;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga &bull; Municipal Administrator</p>
            </div>
        </div>

        <div class="header-right">
            <div class="admin-badge">
                <i class="fa-solid fa-shield-halved"></i> VERIFIED SYSTEM ADMINISTRATOR
            </div>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR (PARTIAL) -->
    @include('partials.sidebar_admin', ['active' => 'dashboard'])

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <!-- HERO BANNER -->
            <div class="hero-banner">
                <div class="hero-title">
                    <h2>ADMINISTRATOR DASHBOARD</h2>
                    <p>Municipal Employment & Service Oversight Panel &bull; Municipality of Magalang, Pampanga</p>
                </div>
                <div>
                    <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #4ade80; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                        Live Cloud Sync
                    </span>
                </div>
            </div>

            <!-- 6 CORE STATS CARDS (Matching Mobile 2x3 Grid) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-briefcase" style="color: #60a5fa;"></i> TOTAL JOBS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminTotalJobs">{{ $numberOfJobs ?? 0 }}</span>
                        <div class="stat-icon" style="color: #60a5fa;"><i class="fa-solid fa-briefcase"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-circle-check" style="color: #4ade80;"></i> DONE JOBS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminDoneJobs">{{ $doneJobs ?? 0 }}</span>
                        <div class="stat-icon" style="color: #4ade80;"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-user-gear" style="color: #38bdf8;"></i> SKILLED WORKERS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminSkilledWorkers">{{ $skilledWorkers ?? 0 }}</span>
                        <div class="stat-icon" style="color: #38bdf8;"><i class="fa-solid fa-user-gear"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-house-chimney" style="color: #34d399;"></i> RESIDENTS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminResidents">{{ $residential ?? 0 }}</span>
                        <div class="stat-icon" style="color: #34d399;"><i class="fa-solid fa-house-chimney"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-triangle-exclamation" style="color: #f87171;"></i> COMPLAINTS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminComplaints">{{ $complaints ?? 0 }}</span>
                        <div class="stat-icon" style="color: #f87171;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                </div>

                <div class="stat-card">
                    <p class="label"><i class="fa-solid fa-file-shield" style="color: #c084fc;"></i> AUDIT TRAIL LOGS</p>
                    <div class="value-row">
                        <span class="stat-number" id="statAdminAuditLogs">{{ $auditLogsCount ?? 0 }}</span>
                        <div class="stat-icon" style="color: #c084fc;"><i class="fa-solid fa-file-shield"></i></div>
                    </div>
                </div>
            </div>

            <!-- PERFORMANCE OVERVIEW (Matching Mobile) -->
            <div class="section-header">
                <h3><i class="fa-solid fa-chart-line" style="color: black;"></i> WEEKLY PERFORMANCE OVERVIEW</h3>
            </div>

            @php
                $jobProgress = ($numberOfJobs ?? 0) > 0 ? min(100, round((($doneJobs ?? 0) / $numberOfJobs) * 100)) : 0;
                $activeDemands = max(0, ($numberOfJobs ?? 0) - ($doneJobs ?? 0));
            @endphp

            <div class="analytics-grid">
                <div class="chart-card">
                    <div class="title-row">
                        <span class="title"><i class="fa-solid fa-percent"></i> Jobs Completed Rate</span>
                        <span class="subtitle">{{ $doneJobs ?? 0 }} of {{ $numberOfJobs ?? 0 }} Completed</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar" style="width: {{ $jobProgress }}%;"></div>
                    </div>
                    <p style="font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 6px;">
                        Municipal service completion efficiency across Magalang.
                    </p>
                </div>

                <div class="chart-card">
                    <div class="title-row">
                        <span class="title"><i class="fa-solid fa-bolt"></i> Active Job Demands</span>
                        <span class="subtitle">{{ $activeDemands }} Active Postings in Database</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar" style="width: {{ ($numberOfJobs ?? 0) > 0 ? 100 : 0 }}%; background: #38bdf8;"></div>
                    </div>
                    <p style="font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 6px;">
                        Live service requests waiting for applicant matching & dispatch.
                    </p>
                </div>
            </div>

            <!-- LEADERBOARDS (Matching Mobile TOP IN-DEMAND JOBS & TOP ACTIVE WORKERS) -->
            <div class="leaderboard-grid">
                <!-- TOP IN-DEMAND JOBS -->
                <div class="ranking-card">
                    <h4><i class="fa-solid fa-fire-flame-curved" style="color: #fbbf24;"></i> TOP IN-DEMAND JOBS IN MAGALANG</h4>
                    <ul class="ranking-list">
                        @forelse($topCategories as $index => $cat)
                            <li class="ranking-item">
                                <div class="ranking-left">
                                    <span class="rank-badge top-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                    <div>
                                        <div class="ranking-title">{{ $cat->category }}</div>
                                        <div class="ranking-subtitle">Certified Municipal Trade</div>
                                    </div>
                                </div>
                                <span style="background: rgba(37, 99, 235, 0.3); border: 1px solid #3b82f6; color: #93c5fd; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                    {{ $cat->total }} posted
                                </span>
                            </li>
                        @empty
                            <li class="ranking-item" style="justify-content: center; color: rgba(255,255,255,0.5);">
                                No job postings in database yet.
                            </li>
                        @endforelse
                    </ul>
                    <div class="action-btn">
                        <a href="{{ route('admin.categories') }}">Manage Categories &rarr;</a>
                    </div>
                </div>

                <!-- TOP ACTIVE SKILLED WORKERS -->
                <div class="ranking-card">
                    <h4><i class="fa-solid fa-award" style="color: #4ade80;"></i> TOP ACTIVE SKILLED WORKERS</h4>
                    <ul class="ranking-list">
                        @forelse($topWorkers as $index => $w)
                            <li class="ranking-item">
                                <div class="ranking-left">
                                    <span class="rank-badge top-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                    <div>
                                        <div class="ranking-title">{{ $w->first_name ? ($w->first_name . ' ' . $w->last_name) : $w->name }}</div>
                                        <div class="ranking-subtitle"><i class="fa-solid fa-location-dot"></i> {{ $w->barangay ?? 'Magalang' }} &bull; {{ $w->skills ?? 'General' }}</div>
                                    </div>
                                </div>
                                <span style="color: #fbbf24; font-size: 12px; font-weight: bold;">
                                    <i class="fa-solid fa-star"></i> {{ number_format($w->rating ?? 5.0, 1) }}
                                </span>
                            </li>
                        @empty
                            <li class="ranking-item" style="justify-content: center; color: rgba(255,255,255,0.5);">
                                No accredited workers yet.
                            </li>
                        @endforelse
                    </ul>
                    <div class="action-btn">
                        <a href="{{ route('admin.users') }}">View Citizen Directory &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- ADMINISTRATIVE MODULES SHORTCUT HUB -->
            <div class="section-header">
                <h3><i class="fa-solid fa-cubes" style="color: black"></i> ADMINISTRATIVE MANAGEMENT MODULES</h3>
            </div>

            <div class="modules-grid">
                <a href="{{ route('admin.users') }}" class="module-shortcut">
                    <div>
                        <div class="module-top">
                            <div class="module-icon"><i class="fa-solid fa-users"></i></div>
                            <span class="module-name">User Management</span>
                        </div>
                        <p class="module-desc">Citizen master directory with 5 role filters, live search, and accreditation badges.</p>
                    </div>
                    <span class="module-link">Open Citizen Registry &rarr;</span>
                </a>

                <a href="{{ route('admin.staff') }}" class="module-shortcut">
                    <div>
                        <div class="module-top">
                            <div class="module-icon"><i class="fa-solid fa-user-shield"></i></div>
                            <span class="module-name">Staff Management</span>
                        </div>
                        <p class="module-desc">PESO officers registry, station designations, and operational accounts.</p>
                    </div>
                    <span class="module-link">Open Staff Roster &rarr;</span>
                </a>

                <a href="{{ route('admin.categories') }}" class="module-shortcut">
                    <div>
                        <div class="module-top">
                            <div class="module-icon"><i class="fa-solid fa-tags"></i></div>
                            <span class="module-name">Job Categories</span>
                        </div>
                        <p class="module-desc">Recognized municipal trade skills, TESDA credentials, and classifications.</p>
                    </div>
                    <span class="module-link">Manage Categories &rarr;</span>
                </a>

                <a href="{{ route('admin.audit_logs') }}" class="module-shortcut">
                    <div>
                        <div class="module-top">
                            <div class="module-icon"><i class="fa-solid fa-list-check"></i></div>
                            <span class="module-name">Audit Logs</span>
                        </div>
                        <p class="module-desc">Tamper-proof system security trails, authentication events, and audit logs.</p>
                    </div>
                    <span class="module-link">View Audit Logs &rarr;</span>
                </a>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        }

        // Live Cloudbase Stats Synchronizer for Admin Dashboard (5-second interval)
        function pollAdminLiveStats() {
            fetch("{{ route('dashboard.live_stats') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) return;

                const updateStat = (id, newVal, highlightColor) => {
                    const el = document.getElementById(id);
                    if (el && newVal !== undefined) {
                        if (parseInt(el.textContent.trim(), 10) !== newVal) {
                            el.textContent = newVal;
                            if (el.parentElement && el.parentElement.parentElement) {
                                const card = el.parentElement.parentElement;
                                card.style.transition = 'all 0.3s ease';
                                card.style.boxShadow = `0 0 20px ${highlightColor}`;
                                setTimeout(() => { card.style.boxShadow = ''; }, 1200);
                            }
                        }
                    }
                };

                updateStat('statAdminTotalJobs', data.totalJobs, '#60a5fa');
                updateStat('statAdminDoneJobs', data.completedJobs, '#4ade80');
                updateStat('statAdminSkilledWorkers', data.totalWorkers, '#38bdf8');
                updateStat('statAdminResidents', data.residential, '#34d399');
                updateStat('statAdminComplaints', data.complaints, '#f87171');
                updateStat('statAdminAuditLogs', data.auditLogsCount, '#c084fc');
            })
            .catch(err => {
                console.debug('Admin live sync error:', err);
            });
        }

        setInterval(pollAdminLiveStats, 5000);
    </script>

    @include('partials.privacy_consent_modal')
</body>
</html>