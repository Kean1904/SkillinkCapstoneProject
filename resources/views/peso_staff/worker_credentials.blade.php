<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Worker Credentials & Verification</title>
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
        .header-left h1 { font-size: 18px; color: white; }
        .header-left p { font-size: 11px; color: rgba(255,255,255,0.8); }

        .page-content { padding: 80px 25px 40px 25px; position: relative; min-height: 100vh; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(20, 55, 130, 0.65); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1100px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .back-link:hover { text-decoration: underline; color: white; }

        .card { background: rgba(20, 60, 130, 0.65); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 14px; padding: 24px; color: white; margin-bottom: 22px; }
        .card h3 { font-size: 17px; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; color: #93c5fd; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 14px 0 18px 0; }

        .credentials-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 820px) {
            .credentials-grid {
                grid-template-columns: 1fr;
            }
        }

        .doc-preview-box {
            background: rgba(0, 0, 0, 0.25);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            min-height: 240px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .doc-preview-img {
            max-width: 100%;
            max-height: 320px;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.4);
            object-fit: contain;
            background: #fff;
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-verified { background: #10b981; color: white; }
        .badge-pending { background: #f59e0b; color: white; }

        .btn-action {
            padding: 12px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-confirm {
            background: #10b981;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        .btn-confirm:hover { background: #059669; }

        .btn-unaccredit {
            background: #ef4444;
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-unaccredit:hover { background: #dc2626; }

        .btn-reupload {
            background: #eab308;
            color: #0f172a;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.35);
        }
        .btn-reupload:hover { background: #ca8a04; color: white; }

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
            width: 54px !important;
            height: 54px !important;
            max-width: 54px !important;
            max-height: 54px !important;
            border-radius: 50% !important;
            background: white;
            padding: 3px;
            margin-bottom: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            object-fit: cover !important;
            display: block;
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
            color: lightcoral;
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
                <p>Magalang, Pampanga &bull; PESO Staff Verification Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('peso_staff.accreditation') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Accreditation Queue</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_peso_staff', ['active' => 'accreditation'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <div>
                    <span><i class="fa-solid fa-id-card-clip"></i> WORKER CREDENTIALS & TESDA VERIFICATION</span>
                    <p style="font-size: 13px; color: #cbd5e1; font-weight: normal; margin-top: 4px;">
                        Municipal accreditation screening for <strong>{{ $worker->full_name }}</strong>
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('peso_staff.accreditation') }}" class="back-link" style="background: rgba(255,255,255,0.15); padding: 8px 14px; border-radius: 6px;">
                        <i class="fa-solid fa-list-check"></i> All Applicants Queue
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- WORKER SUMMARY CARD -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <img src="{{ $worker->profile_image_uri ? asset($worker->profile_image_uri) : asset('image/MP_Profile.png') }}"
                             alt="Avatar"
                             style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; background: white; border: 3px solid rgba(255,255,255,0.6);"
                             onerror="this.outerHTML='<div style=\'width:72px;height:72px;border-radius:50%;background:#0033a0;display:flex;align-items:center;justify-content:center;color:white;font-size:28px;border:2px solid white;\'><i class=\'fa-solid fa-user\'></i></div>'">
                        <div>
                            <h2 style="font-size: 22px; font-weight: bold; color: white;">
                                {{ $worker->full_name }}
                            </h2>
                            <p style="font-size: 13px; color: #93c5fd; margin: 3px 0;">
                                <i class="fa-solid fa-user-tag"></i> @ {{ $worker->name }} &bull; <i class="fa-solid fa-phone"></i> {{ $worker->contact_number ?? 'No contact' }}
                            </p>
                            <p style="font-size: 12px; color: rgba(255,255,255,0.8);">
                                <i class="fa-solid fa-location-dot" style="color: #f87171;"></i> Brgy. {{ $worker->barangay }} &bull; {{ $worker->address ?? 'Magalang, Pampanga' }}
                            </p>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <div style="margin-bottom: 8px;">
                            @if($worker->is_verified)
                                <span class="badge-status badge-verified">
                                    <i class="fa-solid fa-circle-check"></i> OFFICIALLY ACCREDITED
                                </span>
                            @else
                                <span class="badge-status badge-pending">
                                    <i class="fa-solid fa-hourglass-half"></i> PENDING PESO VERIFICATION
                                </span>
                            @endif
                        </div>
                        <div style="font-size: 12px; color: #fde047;">
                            Fixed Service Rate: <strong>{{ $worker->service_rate_display }}</strong>
                        </div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 3px;">
                            Registered Skills: <strong>{{ $worker->skills ?? 'General Skilled Labor' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMITTED CREDENTIALS DISPLAY (TESDA PROOF & VALID ID) -->
            <div class="credentials-grid">

                <!-- 1. TESDA CERTIFICATE PROOF -->
                <div class="card">
                    <h3><i class="fa-solid fa-award"></i> 1. TESDA CERTIFICATE / PROOF</h3>
                    <p style="font-size: 13px; opacity: 0.85;">Document presented to certify National Certificate (NC) competency.</p>
                    <hr>

                    <div style="margin-bottom: 12px; background: rgba(255,255,255,0.08); padding: 10px 14px; border-radius: 8px;">
                        <div style="font-size: 11px; color: #93c5fd; text-transform: uppercase; font-weight: bold;">Submitted Certificate Title</div>
                        <div style="font-size: 15px; font-weight: bold; color: #fde047; margin-top: 2px;">
                            {{ $worker->certificate_proof ?? 'TESDA NC II Qualification' }}
                        </div>
                    </div>

                    @php
                        $hasCertFile = !empty($worker->certificate_file) && file_exists(public_path($worker->certificate_file));
                        $certExt = $hasCertFile ? strtolower(pathinfo($worker->certificate_file, PATHINFO_EXTENSION)) : '';
                    @endphp

                    <div class="doc-preview-box">
                        @if($hasCertFile)
                            @if(in_array($certExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                <img src="{{ asset($worker->certificate_file) }}" alt="TESDA Certificate" class="doc-preview-img">
                                <div style="margin-top: 8px;">
                                    <a href="{{ asset($worker->certificate_file) }}" target="_blank" style="color: #93c5fd; font-size: 12px; text-decoration: underline;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Full Resolution
                                    </a>
                                </div>
                            @elseif($certExt === 'pdf')
                                <i class="fa-solid fa-file-pdf" style="font-size: 64px; color: #ef4444;"></i>
                                <p style="font-size: 14px; font-weight: bold; margin-top: 6px;">PDF Certificate Document Attached</p>
                                <a href="{{ asset($worker->certificate_file) }}" target="_blank" class="btn-action" style="background: #2563eb; color: white; padding: 8px 16px; font-size: 12px; margin-top: 6px;">
                                    <i class="fa-solid fa-file-arrow-down"></i> View / Download PDF
                                </a>
                            @endif
                        @else
                            <i class="fa-solid fa-certificate" style="font-size: 60px; color: #fde047; opacity: 0.9;"></i>
                            <p style="font-size: 14px; font-weight: bold; color: white; margin-top: 4px;">
                                {{ $worker->certificate_proof ?? 'TESDA NC II Certification' }}
                            </p>
                            <p style="font-size: 12px; color: rgba(255,255,255,0.7); max-width: 320px;">
                                Worker has specified this certification upon account creation. Digital file copy can be attached by worker via profile update.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- 2. VALID GOVERNMENT ID -->
                <div class="card">
                    <h3><i class="fa-solid fa-id-card"></i> 2. VALID GOVERNMENT ID</h3>
                    <p style="font-size: 13px; opacity: 0.85;">Proof of identity and residency in Magalang, Pampanga.</p>
                    <hr>

                    <div style="margin-bottom: 12px; background: rgba(255,255,255,0.08); padding: 10px 14px; border-radius: 8px;">
                        <div style="font-size: 11px; color: #93c5fd; text-transform: uppercase; font-weight: bold;">Residency & ID Verification</div>
                        <div style="font-size: 14px; font-weight: bold; color: white; margin-top: 2px;">
                            Brgy. {{ $worker->barangay }}, Magalang, Pampanga
                        </div>
                    </div>

                    @php
                        $hasIdFile = !empty($worker->valid_id_proof) && file_exists(public_path($worker->valid_id_proof));
                        $idExt = $hasIdFile ? strtolower(pathinfo($worker->valid_id_proof, PATHINFO_EXTENSION)) : '';
                    @endphp

                    <div class="doc-preview-box">
                        @if($hasIdFile)
                            @if(in_array($idExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                <img src="{{ asset($worker->valid_id_proof) }}" alt="Valid ID" class="doc-preview-img">
                                <div style="margin-top: 8px;">
                                    <a href="{{ asset($worker->valid_id_proof) }}" target="_blank" style="color: #93c5fd; font-size: 12px; text-decoration: underline;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Full Resolution
                                    </a>
                                </div>
                            @elseif($idExt === 'pdf')
                                <i class="fa-solid fa-file-pdf" style="font-size: 64px; color: #ef4444;"></i>
                                <p style="font-size: 14px; font-weight: bold; margin-top: 6px;">PDF Government ID Document Attached</p>
                                <a href="{{ asset($worker->valid_id_proof) }}" target="_blank" class="btn-action" style="background: #2563eb; color: white; padding: 8px 16px; font-size: 12px; margin-top: 6px;">
                                    <i class="fa-solid fa-file-arrow-down"></i> View / Download PDF
                                </a>
                            @endif
                        @else
                            <i class="fa-solid fa-address-card" style="font-size: 60px; color: #60a5fa; opacity: 0.9;"></i>
                            <p style="font-size: 14px; font-weight: bold; color: white; margin-top: 4px;">
                                Registered Barangay Constituent ID
                            </p>
                            <p style="font-size: 12px; color: rgba(255,255,255,0.7); max-width: 320px;">
                                Residency verification on record: {{ $worker->address ?? ('Brgy. ' . $worker->barangay) }}.
                            </p>
                        @endif
                    </div>
                </div>

            </div>

            <!-- PESO STAFF DECISION ACTIONS -->
            <div class="card" style="background: rgba(30, 58, 138, 0.7); border: 2px solid #3b82f6;">
                <h3><i class="fa-solid fa-gavel"></i> PESO STAFF OFFICIAL CONFIRMATION & ACTION</h3>
                <p style="font-size: 13.5px; opacity: 0.9;">
                    Ang PESO Staff ang may kapangyarihang mag-kumpirma kung totoo at lehitimo ang TESDA Certificate at dokumento ng manggagawa bago ibigay ang opisyal na PESO Accreditation.
                </p>

                @if(!empty($worker->rejection_reason))
                    <div style="margin-top: 12px; background: rgba(234, 179, 8, 0.2); border: 1px solid #eab308; padding: 12px 16px; border-radius: 8px; color: #fde047; font-size: 13px;">
                        <strong><i class="fa-solid fa-triangle-exclamation"></i> Kasalukuyang Tala / Huling Dahilan:</strong>
                        <span style="color: white; margin-left: 6px;">{{ $worker->rejection_reason }}</span>
                    </div>
                @endif

                <hr>

                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    @if($worker->is_verified)
                        <span style="color: #10b981; font-weight: bold; font-size: 15px; display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.15); padding: 10px 20px; border-radius: 8px; border: 1px solid #10b981;">
                            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i> Verified & Officially Accredited
                        </span>
                        <button type="button" class="btn-action btn-unaccredit" onclick="openReasonModal('deny')" title="Revoke accreditation">
                            <i class="fa-solid fa-circle-xmark"></i> REVOKE ACCREDITATION
                        </button>
                    @else
                        <!-- CONFIRM & ACCREDIT -->
                        <form method="POST" action="{{ route('peso.accredit', $worker->user_id) }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-action btn-confirm" title="Confirm validity and approve accreditation for this skilled worker">
                                <i class="fa-solid fa-circle-check"></i> APPROVED (ACCREDIT)
                            </button>
                        </form>

                        <!-- DENIED (RED) -->
                        <button type="button" class="btn-action btn-unaccredit" onclick="openReasonModal('deny')" title="Deny accreditation with reason">
                            <i class="fa-solid fa-circle-xmark"></i> DENIED
                        </button>

                        <!-- RE-UPLOAD (YELLOW) -->
                        <button type="button" class="btn-action btn-reupload" onclick="openReasonModal('reupload')" title="Request worker to re-upload clear ID or certificate">
                            <i class="fa-solid fa-rotate"></i> RE-UPLOAD
                        </button>
                    @endif

                    <!-- BACK -->
                    <a href="{{ route('peso_staff.accreditation') }}" class="btn-action" style="background: rgba(255,255,255,0.2); color: white;">
                        <i class="fa-solid fa-arrow-left"></i> Return to Queue
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- REASON MODAL (FOR DENIED OR RE-UPLOAD) -->
    <div id="actionReasonModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 3000; align-items: center; justify-content: center; padding: 16px;">
        <div style="background: #0f172a; border: 2px solid #3b82f6; border-radius: 14px; width: 100%; max-width: 520px; color: white; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6);" onclick="event.stopPropagation();">
            <div style="background: #1e293b; padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.15); display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalReasonTitle" style="font-size: 15px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clipboard-question" style="color: #fde047;"></i> Dahilan ng Aksyon
                </h3>
                <button type="button" onclick="closeReasonModal()" style="background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="actionReasonForm" method="POST" action="">
                @csrf
                <div style="padding: 20px;">
                    <p id="modalReasonSubtitle" style="font-size: 12.5px; color: #cbd5e1; margin-bottom: 14px;">
                        Pumili o isulat ang opisyal na dahilan para maabisuhan si <strong>{{ $worker->full_name }}</strong>:
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

        function openReasonModal(mode) {
            const modal = document.getElementById('actionReasonModal');
            const form = document.getElementById('actionReasonForm');
            const title = document.getElementById('modalReasonTitle');
            const btn = document.getElementById('btnSubmitReason');
            const textarea = document.getElementById('rejectionReasonText');
            const preset = document.getElementById('presetReasonSelect');

            preset.value = "";
            textarea.value = "";

            if (mode === 'reupload') {
                title.innerHTML = '<i class="fa-solid fa-rotate" style="color: #fde047;"></i> Humiling ng Re-Upload ng Dokumento';
                form.action = "{{ route('peso.request_reupload', $worker->user_id) }}";
                btn.innerText = "Ipadala ang Re-Upload Request";
                btn.style.background = "#eab308";
                btn.style.color = "#0f172a";
                textarea.value = "Kailangan ng mas malinaw o kumpletong kopya ng dokumento / ID / TESDA Certification.";
            } else {
                title.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color: #f87171;"></i> I-Deny ang Accreditation Application';
                form.action = "{{ route('peso.unaccredit', $worker->user_id) }}";
                btn.innerText = "Kumpirmahin ang Pag-Deny";
                btn.style.background = "#ef4444";
                btn.style.color = "#ffffff";
                textarea.value = "Hindi pumasa sa pagsusuri ng PESO Staff ang isinumiteng dokumento.";
            }

            modal.style.display = 'flex';
        }

        function closeReasonModal() {
            document.getElementById('actionReasonModal').style.display = 'none';
        }

        function applyPresetReason(val) {
            if (val) {
                document.getElementById('rejectionReasonText').value = val;
            }
        }
    </script>
</body>
</html>
