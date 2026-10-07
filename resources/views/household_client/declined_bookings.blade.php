<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Declined Bookings</title>
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
        .back-link:hover { text-decoration: underline; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-declined { background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid #ef4444; }

        .booking-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 15px;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }
        .booking-item:hover {
            border-color: rgba(239, 68, 68, 0.7);
            transform: translateY(-2px);
        }

        .btn {
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-find-worker {
            background: #2563eb;
            color: white;
            border: 1px solid #60a5fa;
        }
        .btn-find-worker:hover {
            background: #1d4ed8;
        }

        /* STANDARDIZED COMPACT SIDEBAR */
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
        .sidebar.open { left: 0; }
        .sidebar-profile {
            text-align: center;
            padding: 15px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .sidebar-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            margin-bottom: 6px;
        }
        .sidebar-avatar-fallback {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 2px solid white;
            background: rgba(255,255,255,0.2);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 6px;
        }
        .sidebar-profile .name {
            font-size: 14.5px;
            font-weight: bold;
            color: white;
            margin-bottom: 3px;
        }
        .sidebar-profile .role {
            font-size: 10.5px;
            color: #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-badge {
            background: #10b981;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0 10px;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-menu li {
            margin-bottom: 2px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            padding: 9px 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .sidebar-menu a.active {
            background-color: #ffffff;
            color: #0033a0;
            font-weight: bold;
        }
        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 14px;
            color: inherit;
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
        }
        .sidebar-footer .logout-btn:hover { opacity: 0.8; }
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
        .sidebar-overlay.active { display: block; }
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
                <p>Magalang, Pampanga &bull; Household Client Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.HouseholdClient') }}" class="back-link"><i class="fa-solid fa-house"></i> Main Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_household_client', ['active' => 'declined_bookings'])

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-calendar-xmark" style="color: #f87171;"></i> DECLINED BOOKINGS RECORD</span>
                <a href="{{ route('dashboard.HouseholdClient') }}" class="back-link">&larr; Back to Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h3><i class="fa-solid fa-ban" style="color: #ef4444;"></i> Talaan ng mga Declined Bookings ({{ count($declinedBookings) }})</h3>
                    <span style="font-size: 12px; background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 4px 12px; border-radius: 20px;">
                        <i class="fa-solid fa-shield-halved"></i> Closed / Inactive Bookings
                    </span>
                </div>
                <p style="font-size: 13.5px; opacity: 0.9; margin-top: 6px;">
                    Lahat ng mga service booking request na tinanggihan (declined) ng napiling skilled worker ay ligtas na nakatala rito upang maging malinaw ang iyong hiring history at makapaghanap ka muli ng ibang manggagawa sa Magalang.
                </p>
                <hr>

                @if(isset($declinedBookings) && count($declinedBookings) > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($declinedBookings as $booking)
                            <div class="booking-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <span style="font-size: 12px; font-weight: bold; background: #1e3a8a; color: #93c5fd; padding: 3px 8px; border-radius: 4px; border: 1px solid #3b82f6;">
                                                #{{ $booking->booking_reference }}
                                            </span>
                                            <span class="badge badge-declined">
                                                <i class="fa-solid fa-xmark"></i> {{ $booking->status }}
                                            </span>
                                        </div>
                                        <h4 style="font-size: 17px; color: #ffffff; margin-top: 8px;">
                                            {{ $booking->service_category }}
                                        </h4>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                                        <span style="font-size: 11.5px; color: #cbd5e1;">
                                            <i class="fa-regular fa-clock"></i> {{ $booking->updated_at ? $booking->updated_at->format('M d, Y h:i A') : ($booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A') }}
                                        </span>
                                        <a href="{{ route('dashboard.HouseholdClient') }}" class="btn btn-find-worker">
                                            <i class="fa-solid fa-magnifying-glass"></i> Maghanap ng Ibang Worker
                                        </a>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; font-size: 13px; opacity: 0.95; background: rgba(0,0,0,0.25); padding: 12px 14px; border-radius: 8px;">
                                    <div>
                                        <i class="fa-solid fa-user-gear" style="color: #60a5fa; width: 16px;"></i>
                                        <strong>Skilled Worker:</strong> {{ $booking->worker_name ?? $booking->worker_username }}
                                        <span style="font-size: 11px; opacity: 0.8;">(@ {{ $booking->worker_username }})</span>
                                    </div>
                                    <div>
                                        <i class="fa-solid fa-location-dot" style="color: #f87171; width: 16px;"></i>
                                        <strong>Barangay:</strong> {{ $booking->barangay }}
                                    </div>
                                    <div>
                                        <i class="fa-regular fa-calendar" style="color: #facc15; width: 16px;"></i>
                                        <strong>Scheduled Date:</strong> {{ $booking->scheduled_date ?? 'Not set' }}
                                    </div>
                                    <div>
                                        <i class="fa-solid fa-coins" style="color: #4ade80; width: 16px;"></i>
                                        <strong>Budget:</strong> <span style="color: #4ade80; font-weight: bold;">{{ $booking->estimated_budget }}</span>
                                    </div>
                                    @if(!empty($booking->task_description))
                                        <div style="grid-column: 1 / -1; margin-top: 4px;">
                                            <i class="fa-solid fa-align-left" style="color: #93c5fd; width: 16px;"></i>
                                            <strong>Details:</strong> {{ $booking->task_description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 45px 20px; opacity: 0.85;">
                        <i class="fa-solid fa-clipboard-check" style="font-size: 40px; color: #4ade80; margin-bottom: 12px; display: block;"></i>
                        <h4 style="font-size: 16px; margin-bottom: 6px;">Walang Declined Bookings</h4>
                        <p style="font-size: 13px; max-width: 480px; margin: 0 auto; opacity: 0.8;">
                            Wala kang tinanggihang booking request mula sa mga manggagawa. Lahat ng iyong hiring transactions ay makikita sa Hiring History dashboard.
                        </p>
                        <a href="{{ route('household_client.hiring_history') }}" style="display: inline-flex; align-items: center; gap: 8px; margin-top: 16px; background: #0033a0; color: white; border: 1px solid #60a5fa; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
                            <i class="fa-solid fa-clock-rotate-left"></i> Pumunta sa Hiring History
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
        }
    </script>
</body>
</html>
