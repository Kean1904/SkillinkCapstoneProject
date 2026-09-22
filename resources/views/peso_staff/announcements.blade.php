<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - PESO Announcements</title>
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
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(20, 55, 130, 0.65); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1000px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: #0033a0; color: white; border: 1px solid #60a5fa; }
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
                <p>Magalang, Pampanga &bull; PESO Staff Management</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.PesoStaff') }}" class="back-link"><i class="fa-solid fa-house"></i> PESO Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_peso_staff', ['active' => 'announcements'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-bullhorn"></i> PESO MUNICIPAL ANNOUNCEMENTS</span>
                <a href="{{ route('dashboard.PesoStaff') }}" class="back-link">&larr; Back to PESO Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- BROADCAST VIA REAL EMAIL FORM -->
            <div class="card" style="border: 1px solid rgba(147, 197, 253, 0.4); background: rgba(15, 23, 42, 0.75);">
                <h3><i class="fa-solid fa-paper-plane" style="color: #60a5fa;"></i> Mag-broadcast ng Email Advisory / Maintenance Notice</h3>
                <p style="font-size: 13px; opacity: 0.85;">Magpadala ng opisyal na mensahe at abiso sa tunay na email inbox ng mga rehistradong manggagawa at residente ng Bayan ng Magalang.</p>
                <hr>

                <form action="{{ route('peso_staff.announcements.broadcast') }}" method="POST" onsubmit="return confirm('Sigurado ka bang nais mong i-broadcast ang email advisory na ito sa mga napiling email accounts?');">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                                <i class="fa-solid fa-users"></i> Target na Makakatanggap (Audience)
                            </label>
                            <select name="target_audience" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                                <option value="all">Lahat ng Rehistradong Gumagamit (Workers at Residents)</option>
                                <option value="skilled_worker">Mga Skilled Workers Lamang</option>
                                <option value="residential">Mga Residente / Clients Lamang</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                                <i class="fa-solid fa-tag"></i> Uri ng Abiso (Category)
                            </label>
                            <select name="category" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                                <option value="System Maintenance & Updates">System Maintenance & System Updates</option>
                                <option value="PESO Official Advisory">Opisyal na Abiso ng PESO Magalang</option>
                                <option value="LGU Magalang Notice">LGU Magalang Municipal Advisory</option>
                                <option value="TESDA NC II Assessment">Libreng TESDA Assessment / Training</option>
                                <option value="Job Fair / Emergency Hiring">Job Fair / Emergency Employment (TUPAD)</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                            <i class="fa-solid fa-heading"></i> Pamagat ng Abiso (Announcement Title)
                        </label>
                        <input type="text" name="title" required placeholder="hal. Scheduled System Maintenance & Technical Optimization" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12.5px; font-weight: bold; margin-bottom: 6px; color: #bfdbfe;">
                            <i class="fa-solid fa-align-left"></i> Buong Mensahe / Detalye ng Abiso
                        </label>
                        <textarea name="message" rows="4" required placeholder="Isulat dito ang buong detalye ng abiso, petsa, oras ng maintenance, o instruksyon para sa mga residente at manggagawa..." style="width: 100%; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25); background: #1e293b; color: white; font-size: 13.5px; outline: none; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="background: #2563eb; padding: 11px 24px; font-size: 14px; border-radius: 8px;">
                            <i class="fa-solid fa-paper-plane"></i> Ipadala ang Email Broadcast Ngayon
                        </button>
                    </div>
                </form>
            </div>

            <!-- ACTIVE ANNOUNCEMENTS -->
            <div class="card">
                <h3><i class="fa-solid fa-newspaper"></i> Active Public Announcements</h3>
                <p style="font-size: 13px; opacity: 0.85;">Mga opisyal na abiso ng PESO Magalang para sa mga manggagawa at residente.</p>
                <hr>

                <div style="background: rgba(255,255,255,0.08); border-left: 4px solid #3b82f6; border-radius: 8px; padding: 15px; margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between;">
                        <strong style="font-size: 15px; color: #93c5fd;">TUPAD Emergency Employment Program Registration</strong>
                        <span style="font-size: 11px; opacity: 0.7;">Sept 2026</span>
                    </div>
                    <p style="font-size: 13px; margin-top: 6px; opacity: 0.9;">Bukas na ang registration para sa mga manggagawang kwalipikado sa TUPAD Community Clean-up and Repair Project. Magtungo sa PESO Office dala ang inyong Valid ID.</p>
                </div>

                <div style="background: rgba(255,255,255,0.08); border-left: 4px solid #10b981; border-radius: 8px; padding: 15px;">
                    <div style="display: flex; justify-content: space-between;">
                        <strong style="font-size: 15px; color: #86efac;">Free TESDA NC II Assessment & Screening</strong>
                        <span style="font-size: 11px; opacity: 0.7;">Oct 2026</span>
                    </div>
                    <p style="font-size: 13px; margin-top: 6px; opacity: 0.9;">Libreng assessment para sa mga skilled plumbers, electricians, at welders upang magkaroon ng opisyal na TESDA NC II Certification sa tulong ng LGU Magalang.</p>
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
