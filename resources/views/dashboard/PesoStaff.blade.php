<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - PESO Staff Dashboard</title>
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

        /* STAT CARDS - 2x2 GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 15px;
        }

        .stat-card {
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

        /* PANELS */
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .search-box input {
                width: 100px;
            }
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

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_peso_staff', ['active' => 'dashboard'])

    <!-- PAGE CONTENT -->
    <div class="page-content" id="dashboard">
        <div class="overlay"></div>
        <div class="page-inner">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h2 class="page-title" style="margin-bottom: 4px;">PESO Staff Dashboard</h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 13px;">Public Employment Service Office — Municipality of Magalang</p>
                </div>
                <div>
                    <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #4ade80; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                        <i class="fa-solid fa-shield-halved"></i> Official PESO Portal
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.9); color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-grid">

                <!-- MAIN COLUMN -->
                <div class="main-column" style="flex: 2;">

                    <!-- STAT CARDS 2x2 -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <p class="label">AVAILABLE JOBS</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                                <span class="stat-number">{{ $availableJobs ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <p class="label">PENDING JOBS</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                                <span class="stat-number">{{ $pendingJobs ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <p class="label">SKILLED WORKERS</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-user-gear"></i></div>
                                <span class="stat-number">{{ $skilledWorkers ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <p class="label">RESIDENTIAL</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-house-chimney"></i></div>
                                <span class="stat-number">{{ $residential ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ACCREDITATION QUEUE -->
                    <div class="panel" id="accreditation" style="max-height: none; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="color: #60a5fa; font-size: 16px;"><i class="fa-solid fa-id-card"></i> WORKER ACCREDITATION QUEUE</h3>
                                <p style="font-size: 12px; color: rgba(255,255,255,0.7);">Citizens requesting official PESO accreditation with TESDA certificates</p>
                            </div>
                            <span style="background: #f59e0b; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                {{ $accreditationQueue->count() }} Pending
                            </span>
                        </div>
                        <hr style="width: 100%; border-color: rgba(255,255,255,0.2); margin: 12px 0;">

                        @if($accreditationQueue->isEmpty())
                            <div class="no-data" style="padding: 20px 0; font-size: 14px;">
                                <i class="fa-solid fa-circle-check" style="color: #4ade80; font-size: 24px; display: block; margin-bottom: 6px;"></i>
                                All registered skilled workers are verified and accredited!
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach($accreditationQueue as $worker)
                                    <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                        <div>
                                            <p style="font-weight: bold; font-size: 15px; color: white;">
                                                {{ $worker->full_name }} <span style="font-size: 12px; color: #93c5fd; font-weight: normal;">(@ {{ $worker->name }})</span>
                                            </p>
                                            <p style="font-size: 12px; color: rgba(255,255,255,0.8); margin: 3px 0;">
                                                <i class="fa-solid fa-location-dot" style="color: #f87171;"></i> Brgy. {{ $worker->barangay }} • Contact: {{ $worker->contact_number }}
                                            </p>
                                            <p style="font-size: 12px; color: #fde047; font-weight: 500;">
                                                <i class="fa-solid fa-award"></i> Certificate Proof: <strong>{{ $worker->certificate_proof ?? 'TESDA NC II Submitted for review' }}</strong>
                                            </p>
                                            <p style="font-size: 11px; color: rgba(255,255,255,0.6);">
                                                Trade Skills: {{ $worker->skills }}
                                            </p>
                                        </div>
                                        <div>
                                            <form action="{{ route('peso.accredit', $worker->user_id) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px;">
                                                    <i class="fa-solid fa-check"></i> Accredit Worker
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- MUNICIPAL JOB TRACKING -->
                    <div class="panel" id="jobtracking" style="max-height: none; margin-bottom: 25px;">
                        <h3 style="color: #38bdf8; font-size: 16px;"><i class="fa-solid fa-map-location-dot"></i> MUNICIPAL JOB TRACKING</h3>
                        <p style="font-size: 12px; color: rgba(255,255,255,0.7);">Live monitoring of residential repair postings and worker applications</p>
                        <hr style="width: 100%; border-color: rgba(255,255,255,0.2); margin: 12px 0;">

                        @if($jobsList->isEmpty())
                            <div class="no-data" style="padding: 20px 0; font-size: 14px;">No active job posts in database yet.</div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($jobsList as $job)
                                    <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="flex: 1; padding-right: 15px;">
                                            <p style="font-weight: bold; color: white; font-size: 14px;">{{ $job->title }}</p>
                                            <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 3px 0;">
                                                <span style="color: #93c5fd;">{{ $job->category }}</span> • Brgy. {{ $job->barangay }} • Posted by: <strong>{{ $job->posted_by }}</strong>
                                            </p>
                                            @if($job->applicant_username)
                                                <div style="background: rgba(2, 132, 199, 0.25); border: 1px solid #0284c7; color: #7dd3fc; padding: 3px 10px; border-radius: 4px; display: inline-block; font-size: 11px; font-weight: bold; margin-top: 4px;">
                                                    <i class="fa-solid fa-user-gear"></i> Active Applicant: {{ $job->applicant_username }} (Under Review)
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            @php
                                                $statusColor = match(strtolower($job->status)) {
                                                    'applied' => '#f59e0b',
                                                    'ongoing' => '#0284c7',
                                                    'completed' => '#10b981',
                                                    default => '#6b7280',
                                                };
                                            @endphp
                                            <span style="background: {{ $statusColor }}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                                {{ $job->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- SIDE COLUMN -->
                <div class="side-column" style="flex: 1;">
                    <!-- OFFICIAL COMPLAINTS -->
                    <div class="panel" id="complaints" style="max-height: none; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="color: #f87171; font-size: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> COMPLAINTS</h3>
                            <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold;">
                                {{ $complaintsCount }} Unresolved
                            </span>
                        </div>
                        <hr style="width: 100%; border-color: rgba(255,255,255,0.2); margin: 10px 0;">

                        @if($complaintsList->isEmpty())
                            <div class="no-data" style="padding: 20px 0; font-size: 13px;">No complaints filed. All transactions harmonious!</div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach($complaintsList as $comp)
                                    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 6px; padding: 12px;">
                                        <p style="font-weight: bold; font-size: 13px; color: #fca5a5;">{{ $comp->complaint_type }}</p>
                                        <p style="font-size: 11px; color: white; margin: 4px 0;">{{ $comp->description }}</p>
                                        <p style="font-size: 11px; color: rgba(255,255,255,0.6);">
                                            From: <strong>{{ $comp->complainant_username }}</strong> vs <strong>{{ $comp->respondent_username }}</strong>
                                        </p>
                                        <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: {{ $comp->status === 'Resolved' ? '#10b981' : '#f59e0b' }}; color: white; font-weight: bold;">
                                                {{ $comp->status }}
                                            </span>
                                            @if($comp->status !== 'Resolved')
                                                <form action="{{ route('peso.resolve_complaint', $comp->complaint_id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                                        Resolve
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- VERIFIED WORKERS SUMMARY -->
                    <div class="panel" style="max-height: none;">
                        <h3 style="color: #4ade80; font-size: 15px;"><i class="fa-solid fa-circle-check"></i> ACCREDITED WORKERS</h3>
                        <hr style="width: 100%; border-color: rgba(255,255,255,0.2); margin: 10px 0;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($verifiedWorkers->take(5) as $w)
                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.06); padding: 8px 12px; border-radius: 6px;">
                                    <div>
                                        <p style="font-size: 13px; font-weight: bold; color: white;">{{ $w->full_name }}</p>
                                        <p style="font-size: 11px; color: rgba(255,255,255,0.7);">Brgy. {{ $w->barangay }} • ⭐ {{ number_format($w->rating, 1) }}</p>
                                    </div>
                                    <span style="color: #10b981; font-size: 14px;"><i class="fa-solid fa-badge-check"></i></span>
                                </div>
                            @empty
                                <div class="no-data" style="padding: 10px 0; font-size: 12px;">No accredited workers yet.</div>
                            @endforelse
                        </div>
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