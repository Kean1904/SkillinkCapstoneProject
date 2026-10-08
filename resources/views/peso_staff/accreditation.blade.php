<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Worker Accreditation Queue</title>
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
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
        th { background: rgba(0, 51, 160, 0.5); color: #93c5fd; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        tr:hover { background: rgba(255, 255, 255, 0.05); }

        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .badge-verified { background: #16a34a; color: white; }
        .badge-pending { background: #eab308; color: black; }

        .btn { padding: 7px 14px; border-radius: 5px; font-size: 12px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
        .btn-success { background: #16a34a; color: white; }
        .btn-success:hover { background: #15803d; }

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
    @include('partials.sidebar_peso_staff', ['active' => 'accreditation'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-certificate"></i> SKILLED WORKER ACCREDITATION & SCREENING</span>
                <a href="{{ route('dashboard.PesoStaff') }}" class="back-link">&larr; Back to PESO Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <h3><i class="fa-solid fa-user-check"></i> Worker Screening & Credential Verification Queue</h3>
                <p style="font-size: 13px; opacity: 0.85;">Pagsusuri sa mga TESDA NC II certificates at skills ng mga manggagawa sa Magalang bago i-endorso sa publiko.</p>
                <hr>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Worker Name</th>
                                <th>Specialization / Skills</th>
                                <th>Barangay</th>
                                <th>TESDA Proof / Document</th>
                                <th>Status</th>
                                <th style="min-width: 300px; text-align: center; white-space: nowrap;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($workers) && count($workers) > 0)
                                @foreach($workers as $w)
                                    <tr>
                                        <td>
                                            <strong>{{ $w->full_name }}</strong>
                                            <div style="font-size: 11px; opacity: 0.7;">@ {{ $w->name }} &bull; {{ $w->contact_number }}</div>
                                        </td>
                                        <td>{{ $w->skills ?? 'General Handyman' }}</td>
                                        <td>{{ $w->barangay }}</td>
                                        <td>
                                            <a href="{{ route('peso_staff.worker_credentials', $w->user_id) }}" style="color: #93c5fd; text-decoration: underline; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;" title="Click to view submitted TESDA credentials & documents">
                                                <i class="fa-solid fa-file-lines"></i> {{ $w->certificate_proof ?? 'TESDA NC II Document' }}
                                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if($w->is_verified)
                                                <span class="badge badge-verified"><i class="fa-solid fa-check"></i> Accredited</span>
                                            @elseif(($w->verification_status ?? '') === 're_upload')
                                                <span class="badge" style="background: rgba(234, 179, 8, 0.25); color: #fde047; border: 1px solid #eab308;" title="{{ $w->rejection_reason }}"><i class="fa-solid fa-rotate"></i> Re-Upload Needed</span>
                                            @elseif(($w->verification_status ?? '') === 'rejected')
                                                <span class="badge" style="background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid #ef4444;" title="{{ $w->rejection_reason }}"><i class="fa-solid fa-xmark"></i> Denied</span>
                                            @else
                                                <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> Pending Review</span>
                                            @endif
                                        </td>
                                        <td style="white-space: nowrap; text-align: center;">
                                            @if($w->is_verified)
                                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: nowrap;">
                                                    <span style="color: #10b981; font-weight: bold; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; background: rgba(16, 185, 129, 0.15); padding: 6px 12px; border-radius: 6px; border: 1px solid #10b981;">
                                                        <i class="fa-solid fa-circle-check"></i> Verified Worker
                                                    </span>
                                                    <button type="button" class="btn btn-danger" onclick="openReasonModal({{ $w->user_id }}, '{{ addslashes($w->full_name) }}', 'deny')" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px;" title="Revoke accreditation">
                                                        <i class="fa-solid fa-xmark"></i> Revoke
                                                    </button>
                                                </div>
                                            @else
                                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: nowrap;">
                                                    <!-- APPROVED (GREEN) -->
                                                    <form method="POST" action="{{ route('peso.accredit', $w->user_id) }}" style="display:inline; margin: 0;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" style="background: #10b981; color: white; border: none; padding: 7px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-size: 12px; transition: all 0.2s;" title="Approve and accredit this skilled worker">
                                                            <i class="fa-solid fa-check"></i> Approved
                                                        </button>
                                                    </form>

                                                    <!-- DENIED (RED) -->
                                                    <button type="button" class="btn btn-danger" onclick="openReasonModal({{ $w->user_id }}, '{{ addslashes($w->full_name) }}', 'deny')" style="background: #ef4444; color: white; border: none; padding: 7px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-size: 12px; transition: all 0.2s;" title="Deny accreditation with reason">
                                                        <i class="fa-solid fa-xmark"></i> Denied
                                                    </button>

                                                    <!-- RE-UPLOAD (YELLOW) -->
                                                    <button type="button" class="btn btn-warning" onclick="openReasonModal({{ $w->user_id }}, '{{ addslashes($w->full_name) }}', 'reupload')" style="background: #eab308; color: #0f172a; border: none; padding: 7px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-size: 12px; transition: all 0.2s;" title="Request worker to re-upload documents">
                                                        <i class="fa-solid fa-rotate"></i> Re-Upload
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 25px; opacity: 0.7;">Walang manggagawa sa queue.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- REASON MODAL (FOR DENIED OR RE-UPLOAD) -->
    <div id="queueReasonModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 3000; align-items: center; justify-content: center; padding: 16px;">
        <div style="background: #0f172a; border: 2px solid #3b82f6; border-radius: 14px; width: 100%; max-width: 520px; color: white; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6);" onclick="event.stopPropagation();">
            <div style="background: #1e293b; padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.15); display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalReasonTitle" style="font-size: 15px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clipboard-question" style="color: #fde047;"></i> Dahilan ng Aksyon
                </h3>
                <button type="button" onclick="closeReasonModal()" style="background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="queueReasonForm" method="POST" action="">
                @csrf
                <div style="padding: 20px;">
                    <p id="modalReasonSubtitle" style="font-size: 12.5px; color: #cbd5e1; margin-bottom: 14px;">
                        Pumili o isulat ang opisyal na dahilan para maabisuhan ang manggagawa:
                    </p>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #93c5fd; margin-bottom: 6px;">
                            Karaniwang Dahilan (Quick Presets):
                        </label>
                        <select id="presetReasonSelect" onchange="applyPresetReason(this.value)" style="width: 100%; padding: 10px; border-radius: 6px; background: #ffffff; color: #1e293b; font-weight: 500; font-size: 13px; outline: none; border: 1px solid rgba(255,255,255,0.3);">
                            <option value="">-- Pumili ng Dahilan o Mag-type sa ibaba --</option>
                            <option value="Malabo ang in-upload na Valid ID / Hindi mabasa ang impormasyon.">Malabo ang in-upload na Valid ID / Hindi mabasa ang impormasyon.</option>
                            <option value="Kulang ang TESDA NC II Certificate / Hindi tugma ang ipinakitang sertipiko.">Kulang ang TESDA NC II Certificate / Hindi tugma ang sertipiko.</option>
                            <option value="Kulang ang dokumento (Paki-upload kapwa ang Valid ID at TESDA Certificate).">Kulang ang dokumento (Paki-upload kapwa ang ID at TESDA Certificate).</option>
                            <option value="Paso o expired na ang lisensya / sertipiko.">Paso o expired na ang lisensya / sertipiko.</option>
                            <option value="Hindi tugma ang rehistradong pangalan sa nakalagay sa ID / Sertipiko.">Hindi tugma ang rehistradong pangalan sa dokumento.</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #fde047; margin-bottom: 6px;">
                            Paliwanag / Tala para sa Manggagawa (Required):
                        </label>
                        <textarea id="rejectionReasonText" name="rejection_reason" rows="3" required placeholder="Isulat dito ang espesipikong dahilan kung bakit kailangan mag-re-upload o i-deny..." style="width: 100%; padding: 10px; border-radius: 6px; background: #1e293b; color: white; font-size: 13px; border: 1px solid rgba(255,255,255,0.3); outline: none; resize: vertical;"></textarea>
                    </div>
                </div>

                <div style="background: #1e293b; padding: 12px 20px; border-top: 1px solid rgba(255,255,255,0.15); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeReasonModal()" style="background: rgba(255,255,255,0.15); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer;">
                        Kanselahin
                    </button>
                    <button type="submit" id="btnSubmitReason" style="padding: 8px 20px; font-size: 13px; font-weight: bold; border-radius: 6px; border: none; cursor: pointer; color: white;">
                        Kumpirmahin
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

        function openReasonModal(userId, workerName, mode) {
            const modal = document.getElementById('queueReasonModal');
            const form = document.getElementById('queueReasonForm');
            const title = document.getElementById('modalReasonTitle');
            const subtitle = document.getElementById('modalReasonSubtitle');
            const btn = document.getElementById('btnSubmitReason');
            const textarea = document.getElementById('rejectionReasonText');
            const preset = document.getElementById('presetReasonSelect');

            preset.value = "";
            textarea.value = "";
            subtitle.innerHTML = 'Opisyal na dahilan para maabisuhan si <strong>' + workerName + '</strong>:';

            if (mode === 'reupload') {
                title.innerHTML = '<i class="fa-solid fa-rotate" style="color: #fde047;"></i> Humiling ng Re-Upload ng Dokumento';
                form.action = "{{ url('/dashboard/PesoStaff/request-reupload') }}/" + userId;
                btn.innerText = "Ipadala ang Re-Upload Request";
                btn.style.background = "#eab308";
                btn.style.color = "#0f172a";
                textarea.value = "Kailangan ng mas malinaw o kumpletong kopya ng dokumento / ID / TESDA Certification.";
            } else {
                title.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color: #f87171;"></i> I-Deny ang Accreditation';
                form.action = "{{ url('/dashboard/PesoStaff/unaccredit') }}/" + userId;
                btn.innerText = "Kumpirmahin ang Pag-Deny";
                btn.style.background = "#ef4444";
                btn.style.color = "#ffffff";
                textarea.value = "Hindi pumasa sa pagsusuri ng PESO Staff ang isinumiteng dokumento.";
            }

            modal.style.display = 'flex';
        }

        function closeReasonModal() {
            document.getElementById('queueReasonModal').style.display = 'none';
        }

        function applyPresetReason(val) {
            if (val) {
                document.getElementById('rejectionReasonText').value = val;
            }
        }
    </script>
</body>
</html>
