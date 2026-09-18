<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Hiring History</title>
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

        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-pending { background: rgba(234, 179, 8, 0.3); color: #fef08a; border: 1px solid #eab308; }
        .badge-accepted { background: rgba(59, 130, 246, 0.3); color: #bfdbfe; border: 1px solid #3b82f6; }
        .badge-inprogress { background: rgba(168, 85, 247, 0.3); color: #e9d5ff; border: 1px solid #a855f7; }
        .badge-completed { background: rgba(34, 197, 94, 0.3); color: #bbf7d0; border: 1px solid #22c55e; }

        .btn { padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-warning { background: #eab308; color: black; }
        .btn-danger { background: #ef4444; color: white; }

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
    @include('partials.sidebar_residential', ['active' => 'hiring_history'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-clock-rotate-left"></i> HIRING HISTORY & SERVICE BOOKINGS</span>
                <a href="{{ route('dashboard.Residential') }}" class="back-link">&larr; Back to Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <h3><i class="fa-solid fa-receipt"></i> All Service Transactions</h3>
                <p style="font-size: 13px; opacity: 0.85;">Talaan ng lahat ng iyong hiniling na serbisyo sa mga lisensyado at accredited na manggagawa ng Magalang.</p>
                <hr>

                @if(isset($bookings) && count($bookings) > 0)
                    @foreach($bookings as $booking)
                        @php
                            $status = strtoupper($booking->status ?? 'PENDING');
                            $cleanStatus = strtolower(str_replace(['_', ' '], '', $status));
                        @endphp
                        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 18px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <span style="font-size: 14px; font-weight: bold; color: #93c5fd;">{{ $booking->booking_reference }}</span>
                                    <h4 style="font-size: 17px; margin-top: 4px;">{{ $booking->service_category }}</h4>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $cleanStatus }}">{{ $status }}</span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; font-size: 13px; opacity: 0.9; margin: 14px 0; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px;">
                                <div><strong>Hired Worker:</strong> {{ $booking->worker_name ?? $booking->worker_username }}</div>
                                <div><strong>Scheduled Date:</strong> {{ $booking->scheduled_date }}</div>
                                <div><strong>Budget:</strong> <span style="color: #4ade80; font-weight: bold;">{{ $booking->estimated_budget }}</span></div>
                                <div><strong>Location:</strong> Brgy. {{ $booking->barangay }}</div>
                                <div style="grid-column: 1 / -1;"><strong>Task Details:</strong> {{ $booking->task_description }}</div>
                            </div>

                            <!-- Feedback or Incident Report buttons -->
                            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                                @if($status === 'COMPLETED')
                                    <button class="btn btn-warning" onclick="openReviewModal('{{ $booking->worker_username }}', '{{ $booking->booking_reference }}')">
                                        <i class="fa-solid fa-star"></i> Leave Rating & Review
                                    </button>
                                @endif
                                <button class="btn btn-danger" onclick="openComplaintModal('{{ $booking->worker_username }}', '{{ $booking->booking_reference }}')">
                                    <i class="fa-solid fa-triangle-exclamation"></i> File Complaint to PESO
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 30px; opacity: 0.7;">
                        <i class="fa-solid fa-folder-open" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <p>Wala ka pang transaksyon o booking.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- REVIEW MODAL -->
    <div id="reviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center;">
        <div style="background: #1e3a8a; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; padding: 25px; width: 90%; max-width: 450px; color: white;">
            <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-star" style="color: #eab308;"></i> Rate Worker Service</h3>
            <form method="POST" action="{{ route('residential.review.submit') }}">
                @csrf
                <input type="hidden" name="bookingId" id="revBookingId">
                <input type="hidden" name="workerUsername" id="revWorkerUsername">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; margin-bottom: 6px;">Rating Score (1 to 5 Stars)</label>
                    <select name="ratingStars" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                        <option value="5" style="color: black;">★★★★★ (5 Stars - Excellent)</option>
                        <option value="4" style="color: black;">★★★★☆ (4 Stars - Very Good)</option>
                        <option value="3" style="color: black;">★★★☆☆ (3 Stars - Satisfactory)</option>
                        <option value="2" style="color: black;">★★☆☆☆ (2 Stars - Needs Improvement)</option>
                        <option value="1" style="color: black;">★☆☆☆☆ (1 Star - Poor)</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; margin-bottom: 6px;">Feedback Comments</label>
                    <textarea name="reviewText" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" placeholder="Ibahagi ang iyong komento sa kalidad ng serbisyo..."></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="closeModals()">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- COMPLAINT MODAL -->
    <div id="complaintModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 3000; align-items: center; justify-content: center;">
        <div style="background: #1e3a8a; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; padding: 25px; width: 90%; max-width: 480px; color: white;">
            <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> File Grievance to PESO Magalang</h3>
            <form method="POST" action="{{ route('residential.complaint.submit') }}">
                @csrf
                <input type="hidden" name="bookingId" id="compBookingId">
                <input type="hidden" name="respondentUsername" id="compWorkerUsername">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; margin-bottom: 6px;">Complaint Category</label>
                    <select name="complaintType" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                        <option value="Unfinished Work" style="color: black;">Unfinished / Abandoned Work</option>
                        <option value="Poor Service Quality" style="color: black;">Poor Service Quality / Backjob</option>
                        <option value="Overpricing" style="color: black;">Overpricing / Hidden Charges</option>
                        <option value="Tardiness / No-show" style="color: black;">Tardiness / Unreasonable Delay</option>
                        <option value="Unprofessional Conduct" style="color: black;">Unprofessional Conduct</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; margin-bottom: 6px;">Detailed Incident Narrative</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required placeholder="Ipaliwanag nang detalyado ang nangyari para sa imbestigasyon ng PESO..."></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="closeModals()">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-shield-halved"></i> File Official Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function openReviewModal(worker, ref) {
            document.getElementById('revWorkerUsername').value = worker;
            document.getElementById('revBookingId').value = ref;
            document.getElementById('reviewModal').style.display = 'flex';
        }
        function openComplaintModal(worker, ref) {
            document.getElementById('compWorkerUsername').value = worker;
            document.getElementById('compBookingId').value = ref;
            document.getElementById('complaintModal').style.display = 'flex';
        }
        function closeModals() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('complaintModal').style.display = 'none';
        }
    </script>
</body>
</html>
