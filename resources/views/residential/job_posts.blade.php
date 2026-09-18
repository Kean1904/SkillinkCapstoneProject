<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - My Posted Job Needs</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background-image: url('{{ asset('image/MP_Background.JPG') }}');
            background-size: cover; background-position: center; background-repeat: no-repeat;
            background-attachment: fixed; min-height: 100vh;
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
        .overlay { position: absolute; top: 0; left: 0; width: 100%; min-height: 100vh; background: rgba(20, 55, 130, 0.65); z-index: 0; }
        .page-inner { position: relative; z-index: 2; max-width: 1100px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: #0033a0; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-success { background: #16a34a; color: white; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-pending { background: rgba(234, 179, 8, 0.3); color: #fef08a; border: 1px solid #eab308; }
        .badge-applied { background: rgba(59, 130, 246, 0.3); color: #bfdbfe; border: 1px solid #3b82f6; }
        .badge-completed { background: rgba(34, 197, 94, 0.3); color: #bbf7d0; border: 1px solid #22c55e; }

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
                <p>Magalang, Pampanga &bull; Residential Client Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.Residential') }}" class="back-link"><i class="fa-solid fa-house"></i> Main Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_residential', ['active' => 'job_posts'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-briefcase"></i> MY POSTED JOB NEEDS</span>
                <button class="btn btn-success" onclick="openPostJobModal()"><i class="fa-solid fa-plus"></i> Post New Job Need</button>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- JOB POSTS LIST -->
            <div class="card">
                <h3><i class="fa-solid fa-list-check"></i> Active & Completed Job Posts</h3>
                <p style="font-size: 13px; opacity: 0.85;">Dito makikita ang mga trabahong ipinoste mo at kung sino-sinong manggagawa ang nag-apply.</p>
                <hr>

                @if(isset($jobs) && count($jobs) > 0)
                    @foreach($jobs as $job)
                        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 18px; margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap;">
                                <div>
                                    <h4 style="font-size: 17px;">{{ $job->title }}</h4>
                                    <p style="font-size: 13px; opacity: 0.9; margin: 4px 0 8px 0;">{{ $job->description }}</p>
                                    <div style="font-size: 11px; opacity: 0.75; display: flex; gap: 15px;">
                                        <span><i class="fa-solid fa-tag"></i> {{ $job->category }}</span>
                                        <span><i class="fa-solid fa-location-dot"></i> Brgy. {{ $job->barangay }}</span>
                                        <span><i class="fa-regular fa-calendar"></i> {{ $job->date_posted }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-{{ strtolower($job->status ?? 'pending') }}">{{ $job->status ?? 'Pending' }}</span>
                                </div>
                            </div>

                            <!-- Applicant Banner -->
                            @if(!empty($job->applicant_username))
                                <div style="margin-top: 14px; background: rgba(34, 197, 94, 0.2); border: 1px solid #4ade80; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                    <div style="font-size: 13px;">
                                        <i class="fa-solid fa-user-check" style="color: #4ade80;"></i>
                                        <strong>May nag-apply na Manggagawa:</strong>
                                        <span style="color: #93c5fd; font-weight: bold;">@ {{ $job->applicant_username }}</span>
                                    </div>
                                    <span style="font-size: 11px; background: #16a34a; color: white; padding: 3px 8px; border-radius: 10px;">Application Under Review</span>
                                </div>
                            @else
                                <div style="margin-top: 12px; font-size: 12px; opacity: 0.7;">
                                    <i class="fa-solid fa-hourglass-half"></i> Naghihintay ng aplikanteng manggagawa mula sa Magalang...
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 30px; opacity: 0.7;">
                        <p>Wala ka pang ipinoposteng job need.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- POST JOB MODAL -->
    <div id="postJobModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center;">
        <div style="background: #1e3a8a; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; padding: 25px; width: 90%; max-width: 500px; color: white;">
            <h3 style="margin-bottom: 6px;"><i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i> Post a Job Need in Magalang</h3>
            <p style="font-size: 12px; opacity: 0.85; margin-bottom: 15px;">Makikita ito ng mga accredited skilled workers sa kanilang dashboard.</p>

            <form method="POST" action="{{ route('residential.job.create') }}">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Job Title / Trabaho</label>
                    <input type="text" name="title" placeholder="hal. Kailangan ng Tubero para sa Tumutulong Pipe" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Category / Larangan</label>
                    <select name="category" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                        <option value="Plumbing Repair" style="color: black;">Plumbing Repair</option>
                        <option value="Electrical Installation" style="color: black;">Electrical Installation</option>
                        <option value="Carpentry & Roofing" style="color: black;">Carpentry & Roofing</option>
                        <option value="Welding & Fabrication" style="color: black;">Welding & Fabrication</option>
                        <option value="Appliance Repair" style="color: black;">Appliance Repair</option>
                    </select>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Task Description</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" placeholder="Ipaliwanag ang kailangang ayusin..." required></textarea>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Barangay in Magalang</label>
                    <input type="text" name="barangay" value="San Nicolas 1st" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="closePostJobModal()">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Publish Job Need</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function openPostJobModal() {
            document.getElementById('postJobModal').style.display = 'flex';
        }
        function closePostJobModal() {
            document.getElementById('postJobModal').style.display = 'none';
        }
    </script>
</body>
</html>
