<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Job Categories</title>
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
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 25, 70, 0.75); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1250px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .grid-categories { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 15px; margin-top: 15px; }
        .category-box { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 10px; padding: 18px; text-align: center; }
        .category-icon { font-size: 28px; color: #60a5fa; margin-bottom: 8px; }

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
    @include('partials.sidebar_admin', ['active' => 'categories'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-tags"></i> RECOGNIZED TRADE & JOB CATEGORIES</span>
                <a href="{{ route('dashboard.Admin') }}" class="back-link">&larr; Back to Admin Dashboard</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-list-check"></i> Accredited Employment Classifications in Magalang</h3>
                <p style="font-size: 13px; opacity: 0.85;">Mga opisyal na kasanayang kinikilala ng PESO Magalang para sa accreditation at job matching.</p>
                <hr>

                <div class="grid-categories">
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-faucet-drip"></i></div>
                        <h4 style="font-size: 16px;">Plumbing Repair</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Pipe fitting, drainage, leakage, seals</p>
                    </div>
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-bolt"></i></div>
                        <h4 style="font-size: 16px;">Electrical Installation</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Wiring, breaker troubleshooting, outlets</p>
                    </div>
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-hammer"></i></div>
                        <h4 style="font-size: 16px;">Carpentry & Roofing</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Furniture, ceiling, doors, roofing repair</p>
                    </div>
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-fire"></i></div>
                        <h4 style="font-size: 16px;">Welding & Fabrication</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Steel gates, window grills, structural metal</p>
                    </div>
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-trowel-bricks"></i></div>
                        <h4 style="font-size: 16px;">Masonry & Construction</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Concrete, tiling, plastering, wall repair</p>
                    </div>
                    <div class="category-box">
                        <div class="category-icon"><i class="fa-solid fa-tv"></i></div>
                        <h4 style="font-size: 16px;">Appliance Repair</h4>
                        <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Refrigeration, aircon, washing machine</p>
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
