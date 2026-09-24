<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Municipal Job Tracking & Worker Activity Monitor</title>
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
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(20, 55, 130, 0.65); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1250px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .stat-card { background: rgba(15, 23, 42, 0.65); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px; padding: 16px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; }

        .tab-btn { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: white; padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: bold; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .tab-btn:hover, .tab-btn.active { background: #2563eb; border-color: #60a5fa; box-shadow: 0 2px 8px rgba(37,99,235,0.4); }
        .search-box { padding: 9px 15px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.4); background: rgba(255, 255, 255, 0.15); color: white; outline: none; font-size: 13px; min-width: 260px; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid rgba(255, 255, 255, 0.15); vertical-align: middle; }
        th { background: rgba(0, 51, 160, 0.55); color: #93c5fd; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; white-space: nowrap; }
        tr:hover { background: rgba(255, 255, 255, 0.05); }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; white-space: nowrap; }
        .badge-pending { background: rgba(234, 179, 8, 0.25); color: #fef08a; border: 1px solid #eab308; }
        .badge-accepted { background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid #3b82f6; }
        .badge-completed { background: rgba(16, 185, 129, 0.25); color: #86efac; border: 1px solid #10b981; }
        .badge-cancelled { background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid #ef4444; }

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
                <p>Magalang, Pampanga &bull; PESO Staff Management</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.PesoStaff') }}" class="back-link"><i class="fa-solid fa-house"></i> PESO Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_peso_staff', ['active' => 'job_tracking'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-map-location-dot"></i> MUNICIPAL JOB TRACKING & WORKER MONITOR</span>
                <a href="{{ route('dashboard.PesoStaff') }}" class="back-link">&larr; Back to PESO Dashboard</a>
            </div>

            <!-- METRIC OVERVIEW CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.25); color: #60a5fa;">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div>
                        <div style="font-size: 22px; font-weight: bold;">{{ isset($bookings) ? count($bookings) : 0 }}</div>
                        <div style="font-size: 12px; opacity: 0.75;">Skilled Worker Bookings</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.25); color: #4ade80;">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <div style="font-size: 22px; font-weight: bold;">{{ isset($jobs) ? count($jobs) : 0 }}</div>
                        <div style="font-size: 12px; opacity: 0.75;">Job Postings & Needs</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(234, 179, 8, 0.25); color: #facc15;">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 22px; font-weight: bold;">{{ isset($jobs) ? $jobs->whereNotNull('applicant_username')->count() : 0 }}</div>
                        <div style="font-size: 12px; opacity: 0.75;">Active Job Applicants</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(168, 85, 247, 0.25); color: #c084fc;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <div style="font-size: 22px; font-weight: bold;">{{ isset($workerActivities) ? count($workerActivities) : 0 }}</div>
                        <div style="font-size: 12px; opacity: 0.75;">Worker Activity Logs</div>
                    </div>
                </div>
            </div>

            <!-- TABS & SEARCH CONTROLS -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button class="tab-btn active" id="btnTabBookings" onclick="switchTrackingTab('bookings')">
                        <i class="fa-solid fa-handshake"></i> Direct Bookings ({{ isset($bookings) ? count($bookings) : 0 }})
                    </button>
                    <button class="tab-btn" id="btnTabJobs" onclick="switchTrackingTab('jobs')">
                        <i class="fa-solid fa-clipboard-list"></i> Job Posts & Applications ({{ isset($jobs) ? count($jobs) : 0 }})
                    </button>
                    <button class="tab-btn" id="btnTabLogs" onclick="switchTrackingTab('logs')">
                        <i class="fa-solid fa-list-check"></i> Worker Activity Stream ({{ isset($workerActivities) ? count($workerActivities) : 0 }})
                    </button>
                </div>
                <div>
                    <input type="text" id="trackingSearch" class="search-box" placeholder="🔍 Search worker, client, trade, status..." onkeyup="filterTrackingTable()">
                </div>
            </div>

            <!-- 1. DIRECT BOOKINGS TABLE -->
            <div class="card" id="sectionBookings">
                <h3><i class="fa-solid fa-handshake" style="color: #60a5fa;"></i> Skilled Worker Engagements & Direct Bookings</h3>
                <p style="font-size: 13px; opacity: 0.85;">Rehistro ng mga residenteng nag-book sa skilled workers, takdang petsa, at fixed budget sa bawat barangay.</p>
                <hr>

                <div style="overflow-x: auto;">
                    <table id="tableBookings">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Resident Client</th>
                                <th>Skilled Worker</th>
                                <th>Trade / Service Category</th>
                                <th>Barangay & Address</th>
                                <th>Scheduled Date</th>
                                <th>Fixed Rate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($bookings) && count($bookings) > 0)
                                @foreach($bookings as $b)
                                    @php $st = strtoupper($b->status ?? 'PENDING'); @endphp
                                    <tr class="tracking-row" data-search="{{ strtolower(($b->booking_reference ?? '') . ' ' . ($b->client_name ?? '') . ' ' . ($b->worker_name ?? '') . ' ' . ($b->service_category ?? '') . ' ' . ($b->barangay ?? '') . ' ' . $st) }}">
                                        <td><strong style="color: #93c5fd;">{{ $b->booking_reference ?? 'BK-'.$b->booking_id }}</strong></td>
                                        <td>
                                            <strong>{{ $b->client_name ?? $b->client_username }}</strong>
                                            <div style="font-size: 11px; opacity: 0.7;">@ {{ $b->client_username }}</div>
                                        </td>
                                        <td>
                                            <strong style="color: #86efac;">{{ $b->worker_name ?? $b->worker_username }}</strong>
                                            <div style="font-size: 11px; opacity: 0.7;">@ {{ $b->worker_username }}</div>
                                        </td>
                                        <td><span style="background: rgba(37,99,235,0.3); padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ $b->service_category }}</span></td>
                                        <td>
                                            <i class="fa-solid fa-location-dot" style="color: #f87171; font-size: 11px;"></i> {{ $b->barangay }}
                                            <div style="font-size: 11px; opacity: 0.65; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b->service_address }}</div>
                                        </td>
                                        <td>
                                            <div style="white-space: nowrap;"><i class="fa-regular fa-calendar-check" style="color: #93c5fd;"></i> {{ $b->scheduled_date }}</div>
                                        </td>
                                        <td><strong style="color: #fde047;">{{ $b->estimated_budget ?? '₱500.00' }}</strong></td>
                                        <td>
                                            <span class="badge badge-{{ strtolower($st) }}">{{ $st }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 25px; opacity: 0.7;">Walang aktibong direct booking sa kasalukuyan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. JOB POSTS & APPLICATIONS TABLE -->
            <div class="card" id="sectionJobs" style="display: none;">
                <h3><i class="fa-solid fa-briefcase" style="color: #4ade80;"></i> Community Job Requests & Worker Applications</h3>
                <p style="font-size: 13px; opacity: 0.85;">Mga ipinaskil na trabaho ng residente at sinu-sinong manggagawa ang nag-apply.</p>
                <hr>

                <div style="overflow-x: auto;">
                    <table id="tableJobs">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Trade Category</th>
                                <th>Barangay</th>
                                <th>Posted By</th>
                                <th>Active Applicant</th>
                                <th>Date Posted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($jobs) && count($jobs) > 0)
                                @foreach($jobs as $job)
                                    <tr class="tracking-row" data-search="{{ strtolower($job->title . ' ' . $job->category . ' ' . $job->barangay . ' ' . $job->posted_by . ' ' . ($job->applicant_username ?? '') . ' ' . ($job->status ?? '')) }}">
                                        <td>
                                            <strong>{{ $job->title }}</strong>
                                            <div style="font-size: 11px; opacity: 0.75; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $job->description }}</div>
                                        </td>
                                        <td><span style="background: rgba(37,99,235,0.3); padding: 3px 10px; border-radius: 12px; font-size: 11px;">{{ $job->category }}</span></td>
                                        <td>{{ $job->barangay }}</td>
                                        <td>{{ $job->posted_by }}</td>
                                        <td>
                                            @if(!empty($job->applicant_username))
                                                <span style="color: #86efac; font-weight: bold;"><i class="fa-solid fa-user-check"></i> {{ $job->applicant_username }}</span>
                                            @else
                                                <span style="opacity: 0.5;">Walang nag-apply pa</span>
                                            @endif
                                        </td>
                                        <td>{{ $job->date_posted }}</td>
                                        <td>
                                            <span class="badge badge-{{ strtolower($job->status ?? 'pending') }}">{{ $job->status ?? 'Pending' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 25px; opacity: 0.7;">Walang trabahong nakatala.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. WORKER ACTIVITY LOGS STREAM -->
            <div class="card" id="sectionLogs" style="display: none;">
                <h3><i class="fa-solid fa-list-check" style="color: #c084fc;"></i> Real-Time Skilled Worker Activity Monitor</h3>
                <p style="font-size: 13px; opacity: 0.85;">Audit trail ng bawat galaw ng mga skilled worker sa Magalang (nag-apply, tumanggap ng booking, nagpasa ng credential, o nag-post ng serbisyo).</p>
                <hr>

                <div style="overflow-x: auto;">
                    <table id="tableLogs">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Worker / Actor</th>
                                <th>Role</th>
                                <th>Event Action</th>
                                <th>Activity Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($workerActivities) && count($workerActivities) > 0)
                                @foreach($workerActivities as $log)
                                    <tr class="tracking-row" data-search="{{ strtolower($log->actor_name . ' ' . $log->actor_role . ' ' . $log->action . ' ' . ($log->details ?? '')) }}">
                                        <td style="white-space: nowrap; font-size: 12px; color: #94a3b8;">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td><strong style="color: #86efac;">{{ $log->actor_name }}</strong></td>
                                        <td>
                                            <span style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid #3b82f6; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap;">
                                                {{ $log->actor_role }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: bold; color: #fde047; font-size: 12px; white-space: nowrap;">{{ str_replace('_', ' ', $log->action) }}</span>
                                        </td>
                                        <td style="font-size: 12px; opacity: 0.9;">
                                            {{ $log->details ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 25px; opacity: 0.7;">Walang naitalang aktibidad ng manggagawa.</td>
                                </tr>
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

        function switchTrackingTab(tab) {
            document.getElementById('btnTabBookings').classList.toggle('active', tab === 'bookings');
            document.getElementById('btnTabJobs').classList.toggle('active', tab === 'jobs');
            document.getElementById('btnTabLogs').classList.toggle('active', tab === 'logs');

            document.getElementById('sectionBookings').style.display = (tab === 'bookings') ? 'block' : 'none';
            document.getElementById('sectionJobs').style.display = (tab === 'jobs') ? 'block' : 'none';
            document.getElementById('sectionLogs').style.display = (tab === 'logs') ? 'block' : 'none';
        }

        function filterTrackingTable() {
            const query = (document.getElementById('trackingSearch').value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.tracking-row');
            rows.forEach(r => {
                const searchData = r.getAttribute('data-search') || '';
                r.style.display = (query === '' || searchData.includes(query)) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
