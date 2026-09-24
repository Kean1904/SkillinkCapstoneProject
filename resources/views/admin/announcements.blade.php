<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Municipal Announcements & Job Feeds</title>
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
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: #2563eb; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }

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
                <p>Magalang, Pampanga &bull; Municipal Administrator</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.Admin') }}" class="back-link"><i class="fa-solid fa-house"></i> Admin Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_admin', ['active' => 'announcements'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i> MUNICIPAL ANNOUNCEMENTS & LIVE JOB POSTINGS</span>
                <a href="{{ route('dashboard.Admin') }}" class="back-link">&larr; Back to Admin Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- BROADCAST VIA REAL EMAIL FORM -->
            <div class="card" style="border: 1px solid rgba(147, 197, 253, 0.4); background: rgba(15, 23, 42, 0.75);">
                <h3><i class="fa-solid fa-paper-plane" style="color: #60a5fa;"></i> Executive Municipal Broadcast / Advisory</h3>
                <p style="font-size: 13px; opacity: 0.85;">Magpadala ng opisyal na municipal notice o abiso sa registered emails ng mga manggagawa at residente ng Bayan ng Magalang.</p>
                <hr>

                <form action="{{ route('admin.announcements.broadcast') }}" method="POST" onsubmit="return confirm('I-broadcast ang opisyal na abisong ito?');">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                                <i class="fa-solid fa-users"></i> Target Audience
                            </label>
                            <select name="target_audience" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                                <option value="all">Lahat ng Rehistradong Mamamayan (Workers & Residents)</option>
                                <option value="skilled_worker">Mga Skilled Workers Lamang</option>
                                <option value="residential">Mga Household Residential Clients Lamang</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                                <i class="fa-solid fa-tag"></i> Category
                            </label>
                            <select name="category" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                                <option value="Executive Advisory">Opisyal na Abiso ng Municipal Mayor & PESO</option>
                                <option value="Job Fair & Recruitment">DOLE / PESO Job Fair & Emergency Hiring</option>
                                <option value="TESDA NC II Assessment">Libreng TESDA NC II Skills Training & Assessment</option>
                                <option value="System Maintenance">System Maintenance & Upgrades Advisory</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                            <i class="fa-solid fa-heading"></i> Pamagat ng Abiso (Title)
                        </label>
                        <input type="text" name="title" required placeholder="hal. Municipal PESO Employment Drive & Free TESDA NC II Certification" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                            <i class="fa-solid fa-align-left"></i> Buong Mensahe
                        </label>
                        <textarea name="message" rows="4" required placeholder="Isulat dito ang buong detalye, petsa, lugar, at mga kailangang dalhin..." style="width: 100%; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">
                            <i class="fa-solid fa-paper-plane"></i> Ipadala ang Municipal Broadcast
                        </button>
                    </div>
                </form>
            </div>

            <!-- LIVE UPDATED JOBS POSTED BY PESO STAFF & CITIZENS -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3><i class="fa-solid fa-briefcase" style="color: #4ade80;"></i> Updated Jobs & Service Openings in Magalang</h3>
                        <p style="font-size: 13px; opacity: 0.85;">Mga kasalukuyang trabahong pinopost ng PESO Staff at mga mamamayan para sa kabatiran ni Admin.</p>
                    </div>
                    <div>
                        <span style="background: rgba(16, 185, 129, 0.2); color: #86efac; border: 1px solid #10b981; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            Total: {{ isset($jobs) ? count($jobs) : 0 }} Postings
                        </span>
                    </div>
                </div>
                <hr>

                @if(isset($jobs) && count($jobs) > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($jobs as $job)
                            <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.18); border-radius: 8px; padding: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div style="flex: 1; min-width: 250px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                        <strong style="font-size: 15px; color: white;">{{ $job->title }}</strong>
                                        <span style="background: rgba(37,99,235,0.35); color: #93c5fd; padding: 2px 8px; border-radius: 8px; font-size: 11px; font-weight: bold;">
                                            {{ $job->category }}
                                        </span>
                                    </div>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.8); margin-bottom: 4px;">
                                        {{ $job->description }}
                                    </p>
                                    <div style="font-size: 11px; color: #94a3b8; display: flex; gap: 14px; flex-wrap: wrap;">
                                        <span><i class="fa-solid fa-location-dot" style="color: #f87171;"></i> Brgy. {{ $job->barangay }}</span>
                                        <span><i class="fa-solid fa-user"></i> Posted by: <strong>{{ $job->posted_by }}</strong></span>
                                        <span><i class="fa-regular fa-clock"></i> {{ $job->date_posted }}</span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    @if(!empty($job->applicant_username))
                                        <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #86efac; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 6px;">
                                            <i class="fa-solid fa-user-check"></i> Applicant: {{ $job->applicant_username }}
                                        </div>
                                    @else
                                        <div style="font-size: 11px; opacity: 0.6; margin-bottom: 6px;">No applicant yet</div>
                                    @endif
                                    <span style="background: {{ strtolower($job->status) === 'completed' ? '#10b981' : (strtolower($job->status) === 'ongoing' ? '#0284c7' : '#eab308') }}; color: white; padding: 3px 10px; border-radius: 10px; font-size: 10.5px; font-weight: bold; text-transform: uppercase;">
                                        {{ $job->status ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; padding: 25px; opacity: 0.7;">Walang trabahong nakatala.</p>
                @endif
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
