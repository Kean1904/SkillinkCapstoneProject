<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Worker Settings</title>
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
        .page-inner { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: #0033a0; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 6px; }
        .input-group input { width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white; }

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

        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.25); transition: .3s; border-radius: 24px; border: 1px solid rgba(255,255,255,0.4); }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
        input:checked + .slider { background-color: #10b981; border-color: #10b981; }
        input:checked + .slider:before { transform: translateX(20px); }

        .settings-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.12); cursor: pointer; }
        .settings-row:last-child { border-bottom: none; }
        .settings-row:hover .row-title { color: #93c5fd; }
        .row-title { font-size: 14px; font-weight: bold; transition: color 0.2s; }
        .row-sub { font-size: 12px; opacity: 0.75; margin-top: 2px; }

        .modal-wrap { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); z-index: 3000; align-items: center; justify-content: center; padding: 20px; }
        .modal-box { background: #1e293b; border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 24px; width: 100%; max-width: 520px; color: white; max-height: 85vh; overflow-y: auto; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga &bull; Skilled Worker Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.SkilledWorker') }}" class="back-link"><i class="fa-solid fa-house"></i> Main Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_skilled_worker', ['active' => 'settings'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner" style="max-width: 760px;">

            <div class="page-title">
                <div>
                    <span><i class="fa-solid fa-gear"></i> APP SETTINGS & PREFERENCES</span>
                    <p style="font-size: 12.5px; opacity: 0.75; font-weight: normal; margin-top: 4px;">Manage account security, job notifications, and compliance</p>
                </div>
                <a href="{{ route('dashboard.SkilledWorker') }}" class="back-link">&larr; Back to Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- 1. SECURITY & ACCOUNT -->
            <div class="card" style="background: rgba(30, 58, 138, 0.35); border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; padding: 20px; margin-bottom: 18px;">
                <h3 style="font-size: 13px; font-weight: bold; color: #93c5fd; letter-spacing: 0.8px; margin-bottom: 6px;">
                    <i class="fa-solid fa-shield-halved"></i> SECURITY & ACCOUNT
                </h3>
                <div class="settings-row" onclick="openModal('passwordModal')">
                    <div>
                        <p class="row-title">Change Password</p>
                        <p class="row-sub">Update your worker account login password</p>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="opacity: 0.6; font-size: 13px;"></i>
                </div>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">Accreditation Status</p>
                        <p class="row-sub">Official PESO Magalang worker screening</p>
                    </div>
                    @if(isset($worker) && $worker->is_verified)
                        <span style="background: #10b981; color: white; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: bold;">Accredited Worker</span>
                    @else
                        <span style="background: #f59e0b; color: white; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: bold;">Pending Accreditation</span>
                    @endif
                </div>
            </div>

            <!-- 2. NOTIFICATIONS -->
            <div class="card" style="background: rgba(30, 58, 138, 0.35); border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; padding: 20px; margin-bottom: 18px;">
                <h3 style="font-size: 13px; font-weight: bold; color: #93c5fd; letter-spacing: 0.8px; margin-bottom: 6px;">
                    <i class="fa-solid fa-bell"></i> NOTIFICATIONS & ALERTS
                </h3>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">Job Match Alerts</p>
                        <p class="row-sub">Receive alerts when residential clients post jobs matching your trade</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">Municipal Announcements</p>
                        <p class="row-sub">Updates on PESO job fairs, DOLE TUPAD, and skills training</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">SMS Notifications</p>
                        <p class="row-sub">Receive direct client booking requests via SMS</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- 3. LEGAL & COMPLIANCE -->
            <div class="card" style="background: rgba(30, 58, 138, 0.35); border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; padding: 20px; margin-bottom: 18px;">
                <h3 style="font-size: 13px; font-weight: bold; color: #93c5fd; letter-spacing: 0.8px; margin-bottom: 6px;">
                    <i class="fa-solid fa-gavel"></i> LEGAL & COMPLIANCE
                </h3>
                <div class="settings-row" onclick="openModal('privacyModal')">
                    <div>
                        <p class="row-title">Data Privacy Policy (RA 10173)</p>
                        <p class="row-sub">How PESO Magalang protects worker credentials & certificates</p>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="opacity: 0.6; font-size: 13px;"></i>
                </div>
                <div class="settings-row" onclick="openModal('termsModal')">
                    <div>
                        <p class="row-title">Terms of Service & Code of Ethics</p>
                        <p class="row-sub">Professional conduct guidelines for skilled workers</p>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="opacity: 0.6; font-size: 13px;"></i>
                </div>
            </div>

            <!-- 4. ABOUT & SUPPORT -->
            <div class="card" style="background: rgba(30, 58, 138, 0.35); border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; padding: 20px;">
                <h3 style="font-size: 13px; font-weight: bold; color: #93c5fd; letter-spacing: 0.8px; margin-bottom: 6px;">
                    <i class="fa-solid fa-circle-info"></i> ABOUT & SUPPORT
                </h3>
                <div class="settings-row" onclick="openModal('aboutModal')">
                    <div>
                        <p class="row-title">About SKILLINK</p>
                        <p class="row-sub">PESO Municipality of Magalang Job Platform</p>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="opacity: 0.6; font-size: 13px;"></i>
                </div>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">Municipal PESO Helpline</p>
                        <p class="row-sub">Support for workers regarding accreditation</p>
                    </div>
                    <span style="font-size: 13px; font-weight: bold; color: #93c5fd;">(045) 866-0000</span>
                </div>
                <div class="settings-row" style="cursor: default;">
                    <div>
                        <p class="row-title">App Version</p>
                        <p class="row-sub">Capstone Research Build</p>
                    </div>
                    <span style="font-size: 12px; opacity: 0.75;">v1.0.0 (Capstone Build)</span>
                </div>
            </div>

        </div>
    </div>

    <!-- CHANGE PASSWORD MODAL -->
    <div id="passwordModal" class="modal-wrap">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 17px; font-weight: bold;"><i class="fa-solid fa-lock" style="color: #60a5fa;"></i> Change Account Password</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px;" onclick="closeModal('passwordModal')"></i>
            </div>
            <form method="POST" action="{{ route('skilled_worker.password.update') }}">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">CURRENT PASSWORD</label>
                    <input type="password" name="current_password" placeholder="Enter current password" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);" required>
                </div>
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">NEW PASSWORD (MIN. 6 CHARACTERS)</label>
                    <input type="password" name="new_password" minlength="6" placeholder="Enter new secure password" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white; border-radius: 6px;" onclick="closeModal('passwordModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 6px;"><i class="fa-solid fa-floppy-disk"></i> Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DATA PRIVACY ACT MODAL -->
    <div id="privacyModal" class="modal-wrap">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 16px; font-weight: bold;"><i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> DATA PRIVACY ACT OF 2012 (RA 10173)</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px;" onclick="closeModal('privacyModal')"></i>
            </div>
            <div style="font-size: 13px; line-height: 1.6; opacity: 0.9; display: flex; flex-direction: column; gap: 12px;">
                <p>The Public Employment Service Office (PESO) of the Municipality of Magalang is fully committed to protecting your personal data in strict compliance with the Data Privacy Act of 2012 (Republic Act No. 10173).</p>
                <p><strong>&bull; Collection:</strong> All personal data including contact numbers, barangay addresses, and employment records are collected solely for legitimate job matching, accreditation, and municipal assistance.</p>
                <p><strong>&bull; Protection:</strong> User credentials and passwords are encrypted. Personal documents will never be sold, shared, or disclosed to unauthorized third parties without your explicit consent.</p>
                <p><strong>&bull; Rights:</strong> As a citizen of Magalang, you have the right to access, review, and request modification of your personal records at any time.</p>
            </div>
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="btn btn-primary" style="border-radius: 6px;" onclick="closeModal('privacyModal')">Understood</button>
            </div>
        </div>
    </div>

    <!-- TERMS OF SERVICE MODAL -->
    <div id="termsModal" class="modal-wrap">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 16px; font-weight: bold;"><i class="fa-solid fa-gavel" style="color: #60a5fa;"></i> TERMS OF SERVICE</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px;" onclick="closeModal('termsModal')"></i>
            </div>
            <div style="font-size: 13px; line-height: 1.6; opacity: 0.9; display: flex; flex-direction: column; gap: 12px;">
                <p>By using SKILLINK (PESO Magalang), you agree to:</p>
                <p><strong>1.</strong> Provide truthful and verifiable information regarding your identity, skills, and barangay residency.</p>
                <p><strong>2.</strong> Treat employers, residential clients, and skilled workers with utmost professional courtesy and respect.</p>
                <p><strong>3.</strong> Post only lawful, legitimate, and safe job opportunities within the Municipality of Magalang.</p>
                <p><strong>4.</strong> Adhere to municipal safety, health, and fair labor compensation standards.</p>
            </div>
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="btn btn-primary" style="border-radius: 6px;" onclick="closeModal('termsModal')">I Agree</button>
            </div>
        </div>
    </div>

    <!-- ABOUT SKILLINK MODAL -->
    <div id="aboutModal" class="modal-wrap">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 16px; font-weight: bold;"><i class="fa-solid fa-circle-info" style="color: #60a5fa;"></i> ABOUT SKILLINK</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px;" onclick="closeModal('aboutModal')"></i>
            </div>
            <div style="font-size: 13px; line-height: 1.6; opacity: 0.9; display: flex; flex-direction: column; gap: 12px;">
                <p>SKILLINK is a municipal employment matching and worker accreditation application developed for the Public Employment Service Office (PESO) of the Municipality of Magalang, Pampanga.</p>
                <p>Developed as a Capstone Research Project to bridge skilled local workers (plumbers, carpenters, electricians, etc.) and residential employers across all 27 barangays of Magalang.</p>
                <div style="background: rgba(255,255,255,0.08); padding: 12px; border-radius: 8px; font-size: 12px;">
                    <p><i class="fa-solid fa-location-dot" style="color: #60a5fa;"></i> Municipal Hall Complex, Magalang, Pampanga</p>
                    <p style="margin-top: 4px;"><i class="fa-solid fa-phone" style="color: #60a5fa;"></i> (045) 866-0000 &bull; peso@magalang.gov.ph</p>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="btn btn-primary" style="border-radius: 6px;" onclick="closeModal('aboutModal')">Close</button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>
</body>
</html>
