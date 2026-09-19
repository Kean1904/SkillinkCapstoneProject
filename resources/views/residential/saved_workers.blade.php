<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Saved Workers</title>
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
        .page-inner { position: relative; z-index: 2; max-width: 1100px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .worker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 15px; }
        .worker-card { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.25); border-radius: 10px; padding: 18px; transition: transform 0.2s; }
        .worker-card:hover { transform: translateY(-3px); }

        .btn { padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
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
    @include('partials.sidebar_residential', ['active' => 'saved_workers'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-bookmark"></i> SAVED WORKERS & BOOKMARKS</span>
                <a href="{{ route('dashboard.Residential') }}" class="back-link">&larr; Back to Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <h3><i class="fa-solid fa-star" style="color: #eab308;"></i> Bookmarked Skilled Workers in Magalang</h3>
                <p style="font-size: 13px; opacity: 0.85;">Ang iyong listahan ng mga pinagkakatiwalaang manggagawa para sa mabilisang direct booking.</p>
                <hr>

                <div class="worker-grid">
                    @if(isset($workers) && count($workers) > 0)
                        @foreach($workers as $worker)
                            <div class="worker-card">
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    <img src="{{ asset('image/MP_Profile.png') }}" alt="Worker" style="width: 50px; height: 50px; border-radius: 50%; background: white; padding: 3px;">
                                    <div style="flex: 1;">
                                        <h4 style="font-size: 16px;">{{ $worker->full_name }}</h4>
                                        <p style="font-size: 12px; color: #93c5fd;">@ {{ $worker->name }}</p>
                                        <span style="font-size: 11px; color: #fde047;">★ {{ number_format($worker->rating ?? 5.0, 1) }} Rating</span>
                                    </div>
                                </div>

                                <div style="margin: 12px 0; font-size: 12px; opacity: 0.9;">
                                    <p><i class="fa-solid fa-wrench"></i> <strong>Skills:</strong> {{ $worker->skills ?? 'General Handyman' }}</p>
                                    <p style="margin-top: 4px;"><i class="fa-solid fa-location-dot"></i> <strong>Barangay:</strong> {{ $worker->barangay }}</p>
                                    <p style="margin-top: 4px; color: #86efac;"><i class="fa-solid fa-certificate"></i> {{ $worker->certificate_proof ?? 'TESDA NC II' }}</p>
                                </div>

                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <button class="btn btn-primary" style="flex: 1; justify-content: center;" onclick="openBookModal('{{ $worker->name }}', '{{ $worker->full_name }}', '{{ $worker->skills }}')">
                                        <i class="fa-solid fa-calendar-check"></i> Book Now
                                    </button>
                                    <form method="POST" action="{{ route('residential.worker.toggle_save', $worker->user_id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn" style="background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid #ef4444;" title="Remove from Saved">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="grid-column: 1 / -1; text-align: center; padding: 45px 20px; opacity: 0.85;">
                            <i class="fa-regular fa-bookmark" style="font-size: 38px; color: #93c5fd; margin-bottom: 12px; display: inline-block;"></i>
                            <p style="font-size: 16px; font-weight: bold; margin-bottom: 6px;">Walang naka-save na manggagawa sa ngayon.</p>
                            <p style="font-size: 13px; opacity: 0.75; margin-bottom: 16px;">Maaari kang mag-bookmark ng mga rehistradong skilled workers mula sa Main Dashboard.</p>
                            <a href="{{ route('dashboard.Residential') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Maghanap ng Manggagawa</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- DIRECT BOOKING MODAL -->
    <div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center;">
        <div style="background: #1e3a8a; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; padding: 25px; width: 90%; max-width: 500px; color: white;">
            <h3 style="margin-bottom: 6px;"><i class="fa-solid fa-calendar-plus" style="color: #60a5fa;"></i> Direct Service Booking</h3>
            <p style="font-size: 12px; opacity: 0.85; margin-bottom: 15px;">Mag-request ng serbisyo kay <strong id="bookWorkerName" style="color: #93c5fd;">Worker</strong></p>

            <form method="POST" action="{{ route('residential.booking.create') }}">
                @csrf
                <input type="hidden" name="workerUsername" id="bookWorkerUsername">
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Service Category</label>
                    <input type="text" name="serviceCategory" id="bookServiceCategory" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Detailed Task Description</label>
                    <textarea name="taskDescription" rows="2" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" placeholder="hal. Kailangan ayusin ang tumutulong lababo sa kusina..." required></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Service Address</label>
                        <input type="text" name="serviceAddress" placeholder="Purok / Street" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Barangay</label>
                        <input type="text" name="barangay" value="San Nicolas 1st" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Estimated Budget</label>
                        <input type="text" name="estimatedBudget" placeholder="hal. ₱600.00" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Preferred Schedule</label>
                        <input type="text" name="scheduledDate" placeholder="hal. Bukas 9:00 AM" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="closeBookModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Booking Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function openBookModal(username, fullName, skills) {
            document.getElementById('bookWorkerUsername').value = username;
            document.getElementById('bookWorkerName').innerText = fullName + ' (@' + username + ')';
            document.getElementById('bookServiceCategory').value = skills ? skills.split(',')[0].trim() : 'Home Repair';
            document.getElementById('bookingModal').style.display = 'flex';
        }
        function closeBookModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
    </script>
</body>
</html>
