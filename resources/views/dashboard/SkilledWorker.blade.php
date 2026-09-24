<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Skilled Worker Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
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
            max-width: 1200px;
            margin: 0 auto;     
        }

        .main-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .side-column {
            flex: 1;
            display: flex;
            max-width: 350px;
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
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        /* CREATE SERVICE OFFER BUTTON */
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

        /* AVAILABLE JOBS PANEL */
        .panel {
            background: rgba(20, 60, 130, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px 20px 40px 20px;
            color: white;
            min-height: 200px;
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
            padding: 40px 0;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .dashboard-grid {
                flex-direction: column;
            }

            .stats-row {
                flex-direction: column;
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

    <!-- UNIFIED SKILLED WORKER SIDEBAR (PARTIAL) -->
    @include('partials.sidebar_skilled_worker', ['active' => 'dashboard']) 

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 class="page-title" style="margin-bottom: 0; color: white;">SKILLED WORKER</h2>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; padding: 5px 14px; border-radius: 20px; font-size: 11.5px; color: #6ee7b7; font-weight: 600;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                    <span id="liveSyncStatus">Live Cloud Sync</span>
                </div>
            </div>

            <div class="dashboard-grid">

                <!-- MAIN COLUMN -->
                <div class="main-column">

                    <!-- STAT CARDS -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <p class="label">AVAILABLE JOBS</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                                <span class="stat-number" id="statAvailableJobs">{{ $availableJobs ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <p class="label">PENDING JOBS</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-user-group"></i></div>
                                <span class="stat-number" id="statPendingJobs">{{ $pendingJobs ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- PESO ACCREDITATION STATUS & CREDENTIAL APPLICATION -->
                    <div class="panel" style="background: rgba(30, 58, 138, 0.55); border: 2px solid {{ ($worker->is_verified ?? false) ? '#10b981' : '#f59e0b' }}; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h3 style="color: white; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-certificate" style="color: {{ ($worker->is_verified ?? false) ? '#10b981' : '#fde047' }};"></i>
                                    PESO ACCREDITATION & TESDA CREDENTIALS
                                </h3>
                                <p style="font-size: 12px; color: rgba(255,255,255,0.85); margin-top: 2px;">
                                    @if($worker->is_verified ?? false)
                                        Opisyal kang kinikilala at accredited ng Public Employment Service Office (PESO) ng Munisipyo ng Magalang.
                                    @else
                                        Mag-submit ng patunay ng TESDA Certificate at Valid ID para suriin at ma-accredit ng PESO Staff.
                                    @endif
                                </p>
                            </div>
                            <div>
                                @if($worker->is_verified ?? false)
                                    <span style="background: #10b981; color: white; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 0 10px rgba(16,185,129,0.5);">
                                        <i class="fa-solid fa-circle-check"></i> OFFICIALLY ACCREDITED
                                    </span>
                                @else
                                    <span style="background: #f59e0b; color: white; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-clock"></i> PENDING PESO REVIEW
                                    </span>
                                @endif
                            </div>
                        </div>

                        <hr style="margin: 14px 0; border-color: rgba(255,255,255,0.2);">

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-bottom: 14px;">
                            <div style="background: rgba(255,255,255,0.08); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                                <div style="font-size: 11px; color: #93c5fd; font-weight: bold; text-transform: uppercase;">Current Certificate / Qualification</div>
                                <div style="font-size: 14px; font-weight: bold; color: #fde047; margin-top: 4px;">
                                    <i class="fa-solid fa-award"></i> {{ $worker->certificate_proof ?? 'TESDA NC II Certification' }}
                                </div>
                                @if(!empty($worker->certificate_file))
                                    <div style="margin-top: 6px; font-size: 11.5px; color: #86efac;">
                                        <i class="fa-solid fa-file-circle-check"></i> Digital Certificate Attached
                                        <a href="{{ asset($worker->certificate_file) }}" target="_blank" style="color: #93c5fd; margin-left: 6px; text-decoration: underline;">View</a>
                                    </div>
                                @endif
                            </div>

                            <div style="background: rgba(255,255,255,0.08); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15);">
                                <div style="font-size: 11px; color: #93c5fd; font-weight: bold; text-transform: uppercase;">Valid ID Proof</div>
                                <div style="font-size: 14px; font-weight: bold; color: white; margin-top: 4px;">
                                    <i class="fa-solid fa-id-card"></i> Brgy. {{ $worker->barangay ?? 'Magalang' }} Resident
                                </div>
                                @if(!empty($worker->valid_id_proof))
                                    <div style="margin-top: 6px; font-size: 11.5px; color: #86efac;">
                                        <i class="fa-solid fa-file-circle-check"></i> Valid ID Attached
                                        <a href="{{ asset($worker->valid_id_proof) }}" target="_blank" style="color: #93c5fd; margin-left: 6px; text-decoration: underline;">View</a>
                                    </div>
                                @else
                                    <div style="margin-top: 6px; font-size: 11px; color: rgba(255,255,255,0.6);">
                                        <i class="fa-solid fa-circle-info"></i> Upload valid government ID below
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- SUBMIT APPLICATION FORM -->
                        <details style="background: rgba(0,0,0,0.25); border-radius: 8px; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.2);" {{ !($worker->is_verified ?? false) ? 'open' : '' }}>
                            <summary style="cursor: pointer; font-weight: bold; color: #93c5fd; font-size: 13.5px; user-select: none;">
                                <i class="fa-solid fa-upload"></i> {{ ($worker->is_verified ?? false) ? 'Update Credentials / Re-submit Proof' : 'Upload Credentials & Submit Application for Accreditation' }}
                            </summary>

                            <form action="{{ route('skilled_worker.submit_application') }}" method="POST" enctype="multipart/form-data" style="margin-top: 14px;">
                                @csrf
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px; margin-bottom: 12px;">
                                    <div>
                                        <label style="font-size: 11.5px; color: rgba(255,255,255,0.9); font-weight: bold; display: block; margin-bottom: 4px;">
                                            TESDA Certificate Title / Qualification *
                                        </label>
                                        <input type="text" name="certificate_proof" value="{{ old('certificate_proof', $worker->certificate_proof ?? 'TESDA NC II - ' . ($worker->skills ?? 'General Handyman')) }}" required
                                               placeholder="e.g. TESDA NC II - Plumbing"
                                               style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-size: 13px;">
                                    </div>

                                    <div>
                                        <label style="font-size: 11.5px; color: rgba(255,255,255,0.9); font-weight: bold; display: block; margin-bottom: 4px;">
                                            Upload TESDA Certificate File (Photo or PDF)
                                        </label>
                                        <input type="file" name="certificate_file" accept="image/*,.pdf"
                                               style="width: 100%; padding: 6px 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-size: 12px;">
                                    </div>

                                    <div>
                                        <label style="font-size: 11.5px; color: rgba(255,255,255,0.9); font-weight: bold; display: block; margin-bottom: 4px;">
                                            Upload Valid Government ID (Photo or PDF)
                                        </label>
                                        <input type="file" name="valid_id_file" accept="image/*,.pdf"
                                               style="width: 100%; padding: 6px 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-size: 12px;">
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                                    <button type="submit" style="background: #0033a0; color: white; border: 2px solid #60a5fa; padding: 10px 24px; border-radius: 8px; font-weight: bold; font-size: 13.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.2s;">
                                        <i class="fa-solid fa-paper-plane"></i> Submit Application
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>

                    <!-- CREATE A SERVICE OFFER -->
                    <div class="create-offer-btn" onclick="window.location.href='{{ route('skilled_worker.my_services') }}'" style="cursor: pointer;" title="Manage Your Skills and Service Catalog">
                        <div class="plus-icon"><i class="fa-solid fa-plus"></i></div>
                        <span>CREATE A SERVICE OFFER</span>
                    </div>

                    @if(session('success'))
                        <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 10px 16px; border-radius: 8px; margin-bottom: 12px; font-size: 13px;">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div style="background: rgba(239, 68, 68, 0.25); border: 1px solid #f87171; color: #fecaca; padding: 10px 16px; border-radius: 8px; margin-bottom: 12px; font-size: 13px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- AVAILABLE JOBS LIST -->
                    <div class="panel">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3>AVAILABLE JOBS</h3>
                            <a href="{{ route('skilled_worker.tracking_service') }}" style="color: #93c5fd; font-size: 12px; text-decoration: none;"><i class="fa-solid fa-list-check"></i> Tracking Service</a>
                        </div>
                        <hr>
                        <div id="jobsListWrapper">
                        @if(isset($jobsList) && count($jobsList) > 0)
                            @foreach($jobsList as $job)
                                <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 14px; margin-bottom: 12px; border-left: 4px solid #3b82f6;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <strong style="font-size: 14px;">{{ $job->title }}</strong>
                                        <span style="font-size: 11px; background: rgba(37,99,235,0.3); padding: 2px 8px; border-radius: 12px; border: 1px solid #3b82f6;">{{ $job->category }}</span>
                                    </div>
                                    <p style="font-size: 12px; opacity: 0.85; margin: 6px 0;">{{ $job->description }}</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
                                        <div style="font-size: 11px; opacity: 0.75; display: flex; gap: 14px;">
                                            <span><i class="fa-solid fa-location-dot"></i> {{ $job->barangay }}</span>
                                            <span><i class="fa-regular fa-calendar"></i> {{ $job->date_posted }}</span>
                                            <span><i class="fa-solid fa-user"></i> {{ $job->posted_by }}</span>
                                        </div>
                                        <div>
                                            @if($job->applicant_username === session('user_name'))
                                                <span style="background: rgba(34,197,94,0.3); color: #86efac; border: 1px solid #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;">
                                                    <i class="fa-solid fa-check"></i> Applied
                                                </span>
                                            @elseif(!empty($job->applicant_username))
                                                <span style="background: rgba(234,179,8,0.25); color: #fef08a; border: 1px solid #eab308; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                                                    <i class="fa-solid fa-hourglass-half"></i> Under Review
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('skilled_worker.job.apply', $job->request_id ?? $job->getKey()) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                                        <i class="fa-solid fa-paper-plane"></i> Apply for Job
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-data">No Data</div>
                        @endif
                        </div>
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

    // Live Cloudbase Stats & Jobs Synchronizer (4-second interval)
    let lastAvailableCount = {{ $availableJobs ?? 0 }};
    let lastPendingCount = {{ $pendingJobs ?? 0 }};

    function pollSkilledWorkerLiveStats() {
        fetch("{{ route('dashboard.live_stats') }}?username={{ urlencode(session('user_name') ?? '') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) return;

            const elAvailable = document.getElementById('statAvailableJobs');
            const elPending = document.getElementById('statPendingJobs');

            // 1. Update Available Jobs counter with subtle highlight if changed
            if (elAvailable && data.availableJobs !== undefined) {
                if (parseInt(elAvailable.textContent.trim(), 10) !== data.availableJobs) {
                    elAvailable.textContent = data.availableJobs;
                    if (elAvailable.parentElement && elAvailable.parentElement.parentElement) {
                        const card = elAvailable.parentElement.parentElement;
                        card.style.transition = 'all 0.3s ease';
                        card.style.boxShadow = '0 0 20px #60a5fa';
                        setTimeout(() => { card.style.boxShadow = ''; }, 1200);
                    }
                }
            }

            // 2. Update Pending Jobs counter
            if (elPending && data.pendingJobs !== undefined) {
                if (parseInt(elPending.textContent.trim(), 10) !== data.pendingJobs) {
                    elPending.textContent = data.pendingJobs;
                    if (elPending.parentElement && elPending.parentElement.parentElement) {
                        const card = elPending.parentElement.parentElement;
                        card.style.transition = 'all 0.3s ease';
                        card.style.boxShadow = '0 0 20px #eab308';
                        setTimeout(() => { card.style.boxShadow = ''; }, 1200);
                    }
                }
            }

            // 3. Update Available Jobs Feed dynamically if jobs changed
            if (data.jobsList && (data.availableJobs !== lastAvailableCount || data.pendingJobs !== lastPendingCount)) {
                lastAvailableCount = data.availableJobs;
                lastPendingCount = data.pendingJobs;
                renderJobsList(data.jobsList);
            }
        })
        .catch(err => {
            console.debug('Live sync poll error (will retry):', err);
        });
    }

    function renderJobsList(jobs) {
        const container = document.getElementById('jobsListWrapper');
        if (!container) return;

        if (!jobs || jobs.length === 0) {
            container.innerHTML = '<div class="no-data">No Data</div>';
            return;
        }

        let html = '';
        const csrfToken = '{{ csrf_token() }}';

        jobs.forEach(job => {
            let actionHtml = '';
            if (job.is_applied_by_me) {
                actionHtml = `<span style="background: rgba(34,197,94,0.3); color: #86efac; border: 1px solid #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;">
                    <i class="fa-solid fa-check"></i> Applied
                </span>`;
            } else if (job.is_taken) {
                actionHtml = `<span style="background: rgba(234,179,8,0.25); color: #fef08a; border: 1px solid #eab308; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                    <i class="fa-solid fa-hourglass-half"></i> Under Review
                </span>`;
            } else {
                actionHtml = `<form method="POST" action="${job.apply_url}" style="display: inline;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-paper-plane"></i> Apply for Job
                    </button>
                </form>`;
            }

            html += `<div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 14px; margin-bottom: 12px; border-left: 4px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <strong style="font-size: 14px;">${escapeHtml(job.title)}</strong>
                    <span style="font-size: 11px; background: rgba(37,99,235,0.3); padding: 2px 8px; border-radius: 12px; border: 1px solid #3b82f6;">${escapeHtml(job.category)}</span>
                </div>
                <p style="font-size: 12px; opacity: 0.85; margin: 6px 0;">${escapeHtml(job.description)}</p>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
                    <div style="font-size: 11px; opacity: 0.75; display: flex; gap: 14px;">
                        <span><i class="fa-solid fa-location-dot"></i> ${escapeHtml(job.barangay)}</span>
                        <span><i class="fa-regular fa-calendar"></i> ${escapeHtml(job.date_posted)}</span>
                        <span><i class="fa-solid fa-user"></i> ${escapeHtml(job.posted_by)}</span>
                    </div>
                    <div>${actionHtml}</div>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Run polling every 4 seconds
    setInterval(pollSkilledWorkerLiveStats, 4000);
    </script>

    @include('partials.privacy_consent_modal')

</body>
</html>