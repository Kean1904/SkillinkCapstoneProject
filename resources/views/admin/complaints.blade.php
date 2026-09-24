<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Admin Grievance & Complaints Executive Oversight</title>
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

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid rgba(255, 255, 255, 0.15); vertical-align: middle; }
        th { background: rgba(0, 51, 160, 0.6); color: #93c5fd; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; white-space: nowrap; }
        tr:hover { background: rgba(255, 255, 255, 0.05); }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; white-space: nowrap; }
        .badge-pending { background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid #ef4444; }
        .badge-resolved { background: rgba(16, 185, 129, 0.25); color: #86efac; border: 1.5px solid #10b981; }

        .btn { padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .btn-resolve { background: #10b981; color: white; }
        .btn-resolve:hover { background: #059669; box-shadow: 0 2px 8px rgba(16,185,129,0.5); }
        .btn-review { background: #2563eb; color: white; border: 1px solid #60a5fa; }
        .btn-review:hover { background: #1d4ed8; }

        .clickable-complaint {
            cursor: pointer;
            transition: all 0.2s;
            padding: 4px 6px;
            border-radius: 6px;
        }
        .clickable-complaint:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #93c5fd;
        }

        /* MODAL STYLES */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: #0f172a;
            border: 2px solid #3b82f6;
            border-radius: 14px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            color: white;
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header {
            background: #1e293b;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body {
            padding: 20px;
            max-height: 75vh;
            overflow-y: auto;
        }
        .modal-footer {
            background: #1e293b;
            padding: 14px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
    @include('partials.sidebar_admin', ['active' => 'complaints'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-triangle-exclamation" style="color: #f87171;"></i> CITIZEN COMPLAINTS & GRIEVANCE OVERSIGHT</span>
                <a href="{{ route('dashboard.Admin') }}" class="back-link">&larr; Back to Admin Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3><i class="fa-solid fa-scale-balanced" style="color: #60a5fa;"></i> Municipal Grievance Registry & Resolution Hub</h3>
                        <p style="font-size: 13px; opacity: 0.85;">Kumpletong talaan ng mga reklamo ng residente o manggagawa sa Magalang na may executive access upang siyasatin at i-resolve.</p>
                    </div>
                    <div>
                        <input type="text" id="adminComplaintSearch" placeholder="🔍 Search case, citizen, worker..." onkeyup="filterAdminComplaints()" style="padding: 8px 16px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; font-size: 13px; outline: none; min-width: 250px;">
                    </div>
                </div>
                <hr>

                <div style="overflow-x: auto;">
                    <table id="adminComplaintTable">
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>Complainant</th>
                                <th>Respondent</th>
                                <th>Complaint Type</th>
                                <th>Narrative Reklamo (Click to View)</th>
                                <th>Status</th>
                                <th style="white-space: nowrap; min-width: 140px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($complaints) && count($complaints) > 0)
                                @foreach($complaints as $c)
                                    @php $isResolved = strtolower($c->status ?? '') === 'resolved'; @endphp
                                    <tr class="admin-complaint-row" data-search="{{ strtolower(($c->complaint_id ?? '') . ' ' . ($c->complainant_username ?? '') . ' ' . ($c->respondent_username ?? '') . ' ' . ($c->complaint_type ?? '') . ' ' . ($c->description ?? '') . ' ' . ($isResolved ? 'case close resolved' : 'pending')) }}">
                                        <td><strong>#CMP-{{ $c->complaint_id }}</strong></td>
                                        <td>
                                            <strong>{{ $c->complainant_username }}</strong>
                                            <div style="font-size: 11px; opacity: 0.7;">Citizen Complainant</div>
                                        </td>
                                        <td>
                                            <span style="color: #93c5fd; font-weight: bold;">@ {{ $c->respondent_username }}</span>
                                            <div style="font-size: 11px; opacity: 0.7;">Respondent Worker/Client</div>
                                        </td>
                                        <td>
                                            <span style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid #ef4444; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                                                {{ $c->complaint_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <!-- CLICKABLE COMPLAINT NARRATIVE -->
                                            <div class="clickable-complaint" onclick="openAdminComplaintModal({{ json_encode($c) }})" title="Click to view full complaint & enter Administrator resolution">
                                                <div style="max-width: 320px; font-size: 12.5px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <i class="fa-solid fa-comment-dots" style="color: #fde047; margin-right: 4px;"></i> {{ $c->description }}
                                                </div>
                                                <span style="font-size: 11px; color: #93c5fd; text-decoration: underline; font-weight: bold; margin-top: 2px; display: inline-block;">
                                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i> Tingnan ang buong reklamo & solusyon
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isResolved)
                                                <span class="badge badge-resolved"><i class="fa-solid fa-check"></i> Resolved</span>
                                            @else
                                                <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> Under Investigation</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($isResolved)
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.25); color: #86efac; border: 1.5px solid #10b981; padding: 5px 14px; border-radius: 20px; font-weight: bold; font-size: 11.5px;">
                                                    <i class="fa-solid fa-folder-closed"></i> Case Close
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-review" onclick="openAdminComplaintModal({{ json_encode($c) }})" title="Review complaint & resolve case">
                                                    <i class="fa-solid fa-scale-balanced"></i> Review & Resolve
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px; opacity: 0.7;">Walang nakabinbing reklamo sa database.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- POP-UP SCREEN (MODAL) PARA SA COMPLAINT AT RESOLUTION -->
    <div class="modal-overlay" id="adminComplaintModalOverlay" onclick="closeAdminComplaintModal(event)">
        <div class="modal-card" onclick="event.stopPropagation();">
            <div class="modal-header">
                <div>
                    <h3 style="font-size: 16px; color: white; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-scale-balanced" style="color: #60a5fa;"></i>
                        <span id="modalCaseId">#CMP-Case</span>
                    </h3>
                    <div style="font-size: 11.5px; color: #94a3b8;" id="modalDateFiled">Date Filed: Recent</div>
                </div>
                <button type="button" onclick="closeAdminComplaintModal()" style="background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; padding: 4px 8px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="adminResolveComplaintForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <!-- DETAILS SUMMARY -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px;">
                        <div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Nagreklamo (Complainant)</div>
                            <strong id="modalComplainant" style="color: #86efac; font-size: 13.5px;">—</strong>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Inireklamo (Respondent)</div>
                            <strong id="modalRespondent" style="color: #93c5fd; font-size: 13.5px;">—</strong>
                        </div>
                        <div style="grid-column: span 2;">
                            <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Uri ng Reklamo (Complaint Category)</div>
                            <span id="modalType" style="color: #fca5a5; font-weight: bold; font-size: 13px;">—</span>
                        </div>
                    </div>

                    <!-- NARRATIVE REKLAMO BOX -->
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #fde047; margin-bottom: 6px;">
                            <i class="fa-solid fa-bullhorn"></i> Reklamo ng Residente / Manggagawa (Grievance Narrative):
                        </label>
                        <div id="modalDescription" style="background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 14px; font-size: 13px; line-height: 1.5; color: #f1f5f9; white-space: pre-wrap; max-height: 150px; overflow-y: auto;">
                            —
                        </div>
                    </div>

                    <!-- MEDIATION & SOLUTION BOX -->
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12.5px; font-weight: bold; color: #60a5fa; margin-bottom: 6px;">
                            <i class="fa-solid fa-comment-medical"></i> Solusyon at Mediation Action (Staff / Admin Resolution Notes):
                        </label>
                        <textarea id="modalNotes" name="notes" rows="4" required placeholder="Isulat dito ang opisyal na solusyon, kasunduan sa pamamagitan ng mediation, o aksyon ng munisipyo..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(96,165,250,0.5); background: #1e293b; color: white; font-size: 13px; outline: none; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <div id="modalResolvedBadge" style="display: none; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #86efac; padding: 10px; border-radius: 8px; font-size: 12px; font-weight: bold; text-align: center;">
                        <i class="fa-solid fa-check-circle"></i> Ang kasong ito ay naresolba na. Status: CASE CLOSE.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeAdminComplaintModal()" style="background: rgba(255,255,255,0.15); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer;">
                        Isara
                    </button>
                    <button type="submit" id="btnAdminModalSubmitResolve" class="btn btn-resolve" style="padding: 8px 20px; font-size: 13px;">
                        <i class="fa-solid fa-check"></i> Resolve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        function openAdminComplaintModal(complaint) {
            const isResolved = (complaint.status || '').toLowerCase() === 'resolved';

            document.getElementById('modalCaseId').innerText = '#CMP-' + complaint.complaint_id;
            document.getElementById('modalDateFiled').innerText = 'Petsa: ' + (complaint.created_at ? complaint.created_at.substring(0, 10) : 'Recent');
            document.getElementById('modalComplainant').innerText = complaint.complainant_username || 'Citizen';
            document.getElementById('modalRespondent').innerText = '@ ' + (complaint.respondent_username || 'N/A');
            document.getElementById('modalType').innerText = complaint.complaint_type || 'General Incident';
            document.getElementById('modalDescription').innerText = complaint.description || 'Walang detalye na ibinigay.';

            const notesField = document.getElementById('modalNotes');
            notesField.value = complaint.resolution_notes || '';

            const submitBtn = document.getElementById('btnAdminModalSubmitResolve');
            const resolvedNotice = document.getElementById('modalResolvedBadge');

            if (isResolved) {
                notesField.readOnly = true;
                notesField.style.background = '#0f172a';
                notesField.style.borderColor = 'rgba(255,255,255,0.2)';
                submitBtn.style.display = 'none';
                resolvedNotice.style.display = 'block';
            } else {
                notesField.readOnly = false;
                notesField.style.background = '#1e293b';
                notesField.style.borderColor = 'rgba(96,165,250,0.5)';
                submitBtn.style.display = 'inline-flex';
                resolvedNotice.style.display = 'none';
            }

            const form = document.getElementById('adminResolveComplaintForm');
            form.action = "{{ url('/admin/complaint') }}/" + complaint.complaint_id + "/resolve";

            document.getElementById('adminComplaintModalOverlay').classList.add('active');
        }

        function closeAdminComplaintModal(event) {
            if (!event || event.target.id === 'adminComplaintModalOverlay' || event.target.closest('button')) {
                document.getElementById('adminComplaintModalOverlay').classList.remove('active');
            }
        }

        function filterAdminComplaints() {
            const query = (document.getElementById('adminComplaintSearch').value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.admin-complaint-row');
            rows.forEach(r => {
                const searchData = r.getAttribute('data-search') || '';
                r.style.display = (query === '' || searchData.includes(query)) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
