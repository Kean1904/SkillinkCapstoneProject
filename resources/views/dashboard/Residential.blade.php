<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Residential Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('{{ asset('image/MP_Background.JPG') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
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
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-icon {
            font-size: 20px;
            cursor: pointer;
        }

        .header-left img.logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
        }

        .header-left h1 {
            font-size: 18px;
        }

        .header-left p {
            font-size: 11px;
            font-weight: normal;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 6px 15px;
            gap: 10px;
        }

        .search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            font-size: 13px;
            width: 150px;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box i {
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        /* PAGE WRAPPER */
        .page-content {
            padding: 80px 25px 40px 25px;
            position: relative;
            min-height: 100vh;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(20, 55, 130, 0.55);
            z-index: 0;
            pointer-events: none;
        }

        .page-inner {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: white;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        /* MAIN GRID LAYOUT */
        .dashboard-grid {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .main-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .side-column {
            flex: 1;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* STAT CARDS */
        .stats-row {
            display: flex;
            gap: 20px;
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 15px;
        }

        .stat-card {
            flex: 1;
            max-width: 500px;
            background: rgba(20, 60, 130, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 15px 20px;
            color: white;
        }

        .stat-card .label {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-card .value-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        /* CREATE A JOBS BUTTON */
        .create-offer-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(20, 60, 130, 0.7);
            border: 2px solid #7c4dff;
            border-radius: 10px;
            padding: 18px 20px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .create-offer-btn:hover {
            background: rgba(30, 75, 150, 0.8);
        }

        .plus-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* PANELS (Available Skilled Worker, Notification, Service Request) */
        .panel {
            background: rgba(20, 60, 130, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px 20px 30px 20px;
            color: white;
            min-height: 180px;
            max-height: 280px;
            overflow-y: auto;
        }

        .panel h3 {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .panel hr {
            border: none;
            border-top: 2px solid rgba(255, 255, 255, 0.5);
            width: 60px;
            margin-bottom: 20px;
        }

        .no-data {
            text-align: center;
            color: rgba(255, 255, 255, 0.75);
            font-size: 18px;
            font-weight: 500;
            padding: 30px 0;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .dashboard-grid {
                flex-direction: column;
            }

            .side-column {
                max-width: 100%;
            }

            .stats-row {
                flex-direction: column;
            }

            .stat-card {
                max-width: 100%;
            }

            .search-box input {
                width: 100px;
            }
        }

        
        /* MOBILE-PARITY STANDARDIZED SIDEBAR DRAWER (EXACT MATCH TO MOBILE APP) */
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

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga</p>
            </div>
        </div>

        <div class="search-box">
            <input type="text" placeholder="Search here">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>

    <!-- UNIFIED RESIDENTIAL SIDEBAR (PARTIAL) -->
    @include('partials.sidebar_residential', ['active' => 'dashboard']) 

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <h2 class="page-title">RESIDENTIAL</h2>

            <div class="dashboard-grid">

                <!-- MAIN COLUMN -->
                <div class="main-column">

                    <!-- STAT CARDS -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <p class="label">AVAILABLE SKILLED WORKER</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-user-group"></i></div>
                                <span class="stat-number">{{ $availableWorkers ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('residential.job_posts') }}'" title="View Your Posted Jobs">
                            <p class="label">POST JOB</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                                <span class="stat-number">{{ $postedJobs ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CREATE A JOBS (INTERACTIVE) -->
                    <div class="create-offer-btn" onclick="window.location.href='{{ route('residential.job_posts') }}?action=create'" style="cursor: pointer;" title="Click to Post a Job Need in Magalang">
                        <div class="plus-icon"><i class="fa-solid fa-plus"></i></div>
                        <span>CREATE A JOBS</span>
                    </div>

                    <!-- AVAILABLE SKILLED WORKER LIST -->
                    <div class="panel">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3>AVAILABLE SKILLED WORKER</h3>
                            <a href="{{ route('residential.saved_workers') }}" style="color: #93c5fd; font-size: 12px; text-decoration: none;"><i class="fa-solid fa-bookmark"></i> View Saved</a>
                        </div>
                        <hr>
                        @if(isset($workersList) && count($workersList) > 0)
                            @foreach($workersList as $worker)
                                <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 12px; margin-bottom: 10px; border-left: 4px solid #10b981; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <div>
                                        <strong style="font-size: 14px;">{{ $worker->full_name }}</strong>
                                        <p style="font-size: 12px; opacity: 0.9; margin: 3px 0;"><i class="fa-solid fa-wrench"></i> {{ $worker->skills ?? 'General Handyman' }}</p>
                                        <span style="font-size: 11px; opacity: 0.75;"><i class="fa-solid fa-location-dot"></i> {{ $worker->barangay }}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <form method="POST" action="{{ route('residential.worker.toggle_save', $worker->user_id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" title="Save / Bookmark Worker" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 6px; padding: 5px 9px; cursor: pointer; font-size: 12px;">
                                                <i class="fa-regular fa-bookmark"></i>
                                            </button>
                                        </form>
                                        <div style="text-align: right;">
                                            <span style="background: #10b981; color: white; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 10px;">★ {{ number_format($worker->rating ?? 5.0, 1) }}</span>
                                            <p style="font-size: 10px; color: #a7f3d0; margin-top: 4px;">{{ $worker->is_verified ? 'Verified PESO' : 'Pending Verification' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-data">No Data</div>
                        @endif
                    </div>

                </div>

                <!-- SIDE COLUMN -->
                <div class="side-column">
                    <div class="panel">
                        <h3>Notification</h3>
                        <hr>
                        <div class="no-data">No Data</div>
                    </div>

                    <div class="panel">
                        <h3>Service Request</h3>
                        <hr>
                        <div class="no-data">No Data</div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    </script>

</body>
</html>