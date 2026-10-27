<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - TESDA Analytics & Official Endorsement Reports</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background-color: #07152B;
            min-height: 100vh;
            color: white;
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
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(7, 21, 43, 0.5); z-index: 0; pointer-events: none; }
        .page-inner { position: relative; z-index: 2; max-width: 1250px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(15, 34, 64, 0.75); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0; }

        .btn { padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s; }
        .btn-primary { background: #2563eb; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-print { background: #10b981; color: white; border: 1px solid #34d399; }
        .btn-print:hover { background: #059669; }
        .btn-preview { background: #8b5cf6; color: white; border: 1px solid #c084fc; }
        .btn-preview:hover { background: #7c3aed; }

        /* Metric Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 25px;
        }
        .kpi-card {
            background: rgba(255, 255, 255, 0.07);
            border: 2px solid rgba(255, 255, 255, 0.18);
            border-radius: 10px;
            padding: 18px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 5px; height: 100%;
        }
        .kpi-card.blue::before { background: #3b82f6; }
        .kpi-card.green::before { background: #10b981; }
        .kpi-card.amber::before { background: #f59e0b; }
        .kpi-card.red::before { background: #ef4444; }
        .kpi-card.purple::before { background: #a855f7; }

        .kpi-title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; }
        .kpi-value { font-size: 28px; font-weight: 900; color: white; margin-bottom: 4px; }
        .kpi-sub { font-size: 11.5px; color: #cbd5e1; }

        /* Report Tables */
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .report-table th, .report-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.15); vertical-align: middle; }
        .report-table th { background: rgba(255,255,255,0.08); color: #93c5fd; font-weight: bold; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .report-table tr:hover td { background: rgba(255,255,255,0.04); }

        /* Badge Styles */
        .badge-urgent { background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid #ef4444; padding: 3px 8px; border-radius: 6px; font-weight: bold; font-size: 11px; }
        .badge-high { background: rgba(245,158,11,0.25); color: #fde047; border: 1px solid #f59e0b; padding: 3px 8px; border-radius: 6px; font-weight: bold; font-size: 11px; }
        .badge-standard { background: rgba(59,130,246,0.25); color: #93c5fd; border: 1px solid #3b82f6; padding: 3px 8px; border-radius: 6px; font-weight: bold; font-size: 11px; }

        /* ========================================================
           OFFICIAL PRINTABLE TESDA ENDORSEMENT FORM (OPTION B)
           Layout strictly based on TESDA-SOP-CACO-07-F21
           ======================================================== */
        .official-endorsement-document {
            display: none; /* Hidden on screen by default unless toggled for preview */
            background: #ffffff;
            color: #000000;
            font-family: "Arial", "Helvetica", sans-serif;
            border: 2px solid #002060;
            border-radius: 8px;
            padding: 30px 35px;
            margin-bottom: 35px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .doc-control-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #002060;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 11px;
            font-weight: bold;
            color: #333333;
        }

        .doc-control-box {
            text-align: right;
            border: 1px solid #002060;
            padding: 4px 10px;
            background: #f8fafc;
            border-radius: 4px;
        }

        .official-letterhead {
            display: grid;
            grid-template-columns: 85px 1fr 85px;
            align-items: center;
            text-align: center;
            margin-bottom: 16px;
            gap: 15px;
        }
        .official-letterhead img.seal {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .official-letterhead .header-text h3 {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333333;
            margin: 0;
        }
        .official-letterhead .header-text h2 {
            font-size: 15px;
            font-weight: 900;
            color: #002060;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .official-letterhead .header-text h1 {
            font-size: 16px;
            font-weight: 900;
            color: #b91c1c;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .official-letterhead .header-text p {
            font-size: 11px;
            color: #444444;
            margin: 1px 0;
        }

        .form-banner-title {
            background: #002060;
            color: #ffffff;
            text-align: center;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        .memo-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 12px;
        }
        .memo-meta-table td {
            padding: 5px 8px;
            vertical-align: top;
            border: 1px solid #cbd5e1;
        }
        .memo-meta-table .label {
            width: 160px;
            background: #f1f5f9;
            font-weight: bold;
            color: #002060;
            text-transform: uppercase;
            font-size: 11px;
        }

        .doc-section-title {
            font-size: 12px;
            font-weight: bold;
            color: #002060;
            background: #e2e8f0;
            padding: 5px 10px;
            border-left: 4px solid #002060;
            margin: 16px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .print-grid-kpi {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }
        .print-kpi-item {
            border: 1px solid #002060;
            padding: 8px 10px;
            text-align: center;
            background: #f8fafc;
            border-radius: 4px;
        }
        .print-kpi-item .p-num {
            font-size: 20px;
            font-weight: 900;
            color: #002060;
        }
        .print-kpi-item .p-lbl {
            font-size: 10.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        .doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 14px;
        }
        .doc-table th, .doc-table td {
            border: 1px solid #334155;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .doc-table th {
            background: #002060;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .doc-table .highlight-num {
            font-weight: 900;
            color: #b91c1c;
            text-align: center;
            font-size: 13px;
        }

        .doc-statement {
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            text-align: justify;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            border-radius: 4px;
            margin: 16px 0;
        }

        .signatories-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signatory-box {
            text-align: center;
            font-size: 11px;
        }
        .signatory-line {
            border-bottom: 1.5px solid #000000;
            margin: 40px auto 6px auto;
            width: 85%;
        }
        .signatory-name {
            font-weight: 900;
            font-size: 12px;
            color: #000000;
            text-transform: uppercase;
        }
        .signatory-title {
            color: #334155;
            font-size: 10.5px;
        }

        /* ========================================================
           PRINT STYLES: window.print() or Save as PDF
           ======================================================== */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
            }
            .header, .sidebar, .sidebar-overlay, .btn, .back-link, .no-print, .kpi-grid, .card, .overlay {
                display: none !important;
            }
            .page-content {
                padding: 0 !important;
                margin: 0 !important;
            }
            .official-endorsement-document {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }
            .doc-table th {
                background: #002060 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .form-banner-title {
                background: #002060 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .doc-section-title {
                background: #e2e8f0 !important;
                color: #002060 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .memo-meta-table .label {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-kpi-item {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <header class="header no-print">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK - MUNICIPALITY OF MAGALANG</h1>
                <p>Public Employment Service Office (PESO) Official Portal</p>
            </div>
        </div>
        <div style="font-size: 13px; font-weight: bold; background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 6px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-certificate" style="color: #fde047;"></i> TESDA Accreditation & Endorsement
        </div>
    </header>

    @include('partials.sidebar_peso_staff', ['active' => 'reports'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <!-- Title & Action Buttons -->
            <div class="page-title no-print">
                <div>
                    <h2><i class="fa-solid fa-chart-pie" style="color: #60a5fa;"></i> TESDA Training Needs & Endorsement</h2>
                    <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Dynamic Local Skilled Workforce Competency Assessment & TESDA NC Endorsement System</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('dashboard.PesoStaff') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                    <button onclick="toggleDocPreview()" class="btn btn-preview" id="btnTogglePreview">
                        <i class="fa-solid fa-file-contract"></i> Preview Official Endorsement Form
                    </button>
                    <button onclick="window.print()" class="btn btn-print">
                        <i class="fa-solid fa-print"></i> Print Official TESDA Report (PDF)
                    </button>
                </div>
            </div>

            <!-- =========================================================================
                 OFFICIAL PRINTABLE TESDA ENDORSEMENT FORM (OPTION B)
                 Visible in print (@media print) and when Preview is clicked
                 ========================================================================= -->
            <div class="official-endorsement-document" id="officialDocContainer">
                
                <!-- Document Control Box (TESDA Template Header Standard) -->
                <div class="doc-control-header">
                    <div>
                        <span>STANDARD OPERATING PROCEDURE &bull; CACO OPERATIONS</span>
                    </div>
                    <div class="doc-control-box">
                        <div><strong>FORM CODE:</strong> TESDA-SOP-CACO-07-F21</div>
                        <div><strong>PESO REF:</strong> PESO-MGL-TNA-{{ date('Y') }}-{{ str_pad(date('m'), 2, '0', STR_PAD_LEFT) }}-01</div>
                        <div><strong>REV. NO.:</strong> 01 &bull; <strong>SERIES:</strong> {{ date('Y') }}</div>
                    </div>
                </div>

                <!-- Letterhead -->
                <div class="official-letterhead">
                    <div>
                        <img src="{{ asset('image/MP_Logo.png') }}" alt="Magalang Municipal Seal" class="seal">
                    </div>
                    <div class="header-text">
                        <h3>Republic of the Philippines &bull; Province of Pampanga</h3>
                        <h2>Municipality of Magalang</h2>
                        <h1>Public Employment Service Office (PESO)</h1>
                        <p>In formal institutional partnership with</p>
                        <p><strong>TECHNICAL EDUCATION AND SKILLS DEVELOPMENT AUTHORITY (TESDA)</strong></p>
                        <p style="font-size: 10px; color: #555;">Region III &bull; Pampanga Provincial Office &bull; San Fernando, Pampanga</p>
                    </div>
                    <div>
                        <!-- High Quality TESDA Logo Emblem -->
                        <svg class="seal" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="46" fill="#002060" stroke="#001845" stroke-width="2"/>
                            <circle cx="50" cy="50" r="38" fill="#ffffff"/>
                            <circle cx="50" cy="50" r="34" fill="#0033a0"/>
                            <!-- Cogwheel Teeth -->
                            <path d="M47 14h6v6h-6zM47 80h6v6h-6zM14 47h6v6h-6zM80 47h6v6h-6zM24 24h5v5h-5zM71 71h5v5h-5zM24 71h5v5h-5zM71 24h5v5h-5z" fill="#facc15"/>
                            <!-- Inner Torch & Rays -->
                            <circle cx="50" cy="50" r="18" fill="#ffffff"/>
                            <path d="M50 36l3 9h-6l3-9z" fill="#b91c1c"/>
                            <path d="M48 45h4v16h-4z" fill="#002060"/>
                            <circle cx="50" cy="50" r="4" fill="#facc15"/>
                            <text x="50" y="74" text-anchor="middle" font-size="6.5" font-weight="900" fill="#ffffff" font-family="Arial">TESDA</text>
                        </svg>
                    </div>
                </div>

                <!-- Form Banner Title -->
                <div class="form-banner-title">
                    COMMUNITY SKILLS TRAINING NEEDS ASSESSMENT & ACCREDITATION ENDORSEMENT FORM
                </div>

                <!-- Memorandum Routing Metadata -->
                <table class="memo-meta-table">
                    <tr>
                        <td class="label">MEMORANDUM FOR:</td>
                        <td><strong>THE PROVINCIAL DIRECTOR</strong><br>Technical Education and Skills Development Authority (TESDA) &bull; Pampanga Provincial Office</td>
                        <td class="label">DATE ISSUED:</td>
                        <td><strong>{{ now()->format('F d, Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">THRU:</td>
                        <td><strong>Chief, Technical-Vocational Education & Training (TVET) Operations</strong></td>
                        <td class="label">DOCUMENT TRACKING:</td>
                        <td><span style="font-family: monospace; font-weight: bold; color: #002060;">PESO-MGL-TESDA-{{ date('Y') }}-{{ str_pad(date('m'), 2, '0', STR_PAD_LEFT) }}-001</span></td>
                    </tr>
                    <tr>
                        <td class="label">FROM:</td>
                        <td colspan="3"><strong>PUBLIC EMPLOYMENT SERVICE OFFICE (PESO)</strong> &bull; Municipality of Magalang, Pampanga</td>
                    </tr>
                    <tr>
                        <td class="label">SUBJECT:</td>
                        <td colspan="3"><strong>FORMAL ENDORSEMENT OF LOCAL SKILLED TRADESPERSONS & REQUEST FOR COMMUNITY-BASED TRAINING PROGRAM (CBTP) / MOBILE TRAINING PROGRAM (MTP) & MANDATORY TESDA NC II COMPETENCY ASSESSMENT ALLOCATION</strong></td>
                    </tr>
                    <tr>
                        <td class="label">LEGAL AUTHORITY:</td>
                        <td colspan="3">Republic Act No. 8759 (PESO Act of 1999 as amended by RA 10691) & Republic Act No. 7796 (TESDA Act of 1994)</td>
                    </tr>
                    <tr>
                        <td class="label">DATA SOURCING:</td>
                        <td colspan="3"><strong>SKILLINK Magalang Municipal Labor Matching & Workforce Competency Registry</strong> (Live Database)</td>
                    </tr>
                </table>

                <!-- SECTION I: Executive Workforce Metrics -->
                <div class="doc-section-title">
                    SECTION I: MUNICIPAL WORKFORCE PROFILING & ACCREDITATION READINESS SUMMARY
                </div>

                <div class="print-grid-kpi">
                    <div class="print-kpi-item">
                        <div class="p-num" id="pDocTotal">{{ number_format($totalWorkers ?? 0) }}</div>
                        <div class="p-lbl">Total Registered Skilled Workforce</div>
                    </div>
                    <div class="print-kpi-item" style="border-color: #10b981;">
                        <div class="p-num" id="pDocCert" style="color: #059669;">{{ number_format($certifiedCount ?? 0) }}</div>
                        <div class="p-lbl">Verified TESDA NC Holders (Accredited)</div>
                    </div>
                    <div class="print-kpi-item" style="border-color: #b91c1c;">
                        <div class="p-num" id="pDocUncert" style="color: #b91c1c;">{{ number_format($uncertifiedCount ?? 0) }}</div>
                        <div class="p-lbl">Workers Needing Certification / Training</div>
                    </div>
                    <div class="print-kpi-item" style="border-color: #7c3aed;">
                        <div class="p-num" id="pDocRate" style="color: #7c3aed;">{{ $certificationRate ?? 0 }}%</div>
                        <div class="p-lbl">Current Municipal Certification Rate</div>
                    </div>
                </div>

                <!-- SECTION II: Target TESDA Qualifications Table (Exact counts requested by user) -->
                <div class="doc-section-title">
                    SECTION II: PRIORITY TESDA NC QUALIFICATIONS & NUMBER OF WORKERS NEEDING CERTIFICATION
                </div>

                <table class="doc-table" id="docQualificationsTable">
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">Item</th>
                            <th>Priority TESDA NC Qualification Title</th>
                            <th style="width: 105px; text-align: center;">Course Code</th>
                            <th>Industry Sector</th>
                            <th style="width: 95px; text-align: center;">Number of Workers Needing Certification</th>
                            <th style="width: 60px; text-align: center;">Share (%)</th>
                            <th>Recommended Delivery Mode</th>
                            <th style="width: 80px; text-align: center;">Priority</th>
                        </tr>
                    </thead>
                    <tbody id="docQualificationsTableBody">
                        @if(isset($qualificationsNeeded) && count($qualificationsNeeded) > 0)
                            @foreach($qualificationsNeeded as $q)
                                <tr>
                                    <td style="text-align: center; font-weight: bold;">{{ $q['item_no'] }}</td>
                                    <td>
                                        <strong>{{ $q['qualification_title'] }}</strong>
                                        <div style="font-size: 10px; color: #555;">Represented Skills: {{ $q['raw_trades'] }}</div>
                                    </td>
                                    <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $q['course_code'] }}</td>
                                    <td>{{ $q['sector'] }}</td>
                                    <td class="highlight-num">{{ $q['worker_count'] }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $q['share_percentage'] }}%</td>
                                    <td style="font-size: 10.5px;">{{ $q['delivery_mode'] }}</td>
                                    <td style="text-align: center; font-weight: bold; font-size: 10px;">
                                        @if($q['priority_level'] === 'URGENT')
                                            <span style="color: #b91c1c;">URGENT</span>
                                        @elseif($q['priority_level'] === 'HIGH')
                                            <span style="color: #d97706;">HIGH</span>
                                        @else
                                            <span style="color: #2563eb;">STANDARD</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="background: #f1f5f9; font-weight: bold;">
                                <td colspan="4" style="text-align: right; padding-right: 12px; text-transform: uppercase;">
                                    Total Uncertified Candidates Endorsed for Training / Assessment:
                                </td>
                                <td class="highlight-num" id="docTotalQualificationsWorkers">{{ $uncertifiedCount ?? 0 }}</td>
                                <td style="text-align: center;">100%</td>
                                <td colspan="2" style="font-size: 10.5px; color: #002060;">Comprehensive Endorsement Roster</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 15px;">Walang naitalang manggagawa na nangangailangan ng NC certification sa kasalukuyan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- SECTION III: Barangay Distribution Table -->
                <div class="doc-section-title">
                    SECTION III: GEOGRAPHIC CONCENTRATION ACROSS MAGALANG BARANGAYS
                </div>

                <table class="doc-table" id="docBarangayTable">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th>Barangay Location</th>
                            <th style="width: 120px; text-align: center;">Uncertified Workers</th>
                            <th>Dominant Trade Demands</th>
                            <th style="width: 110px; text-align: center;">Deployment Priority</th>
                            <th>Proposed Training Venue</th>
                        </tr>
                    </thead>
                    <tbody id="docBarangayTableBody">
                        @if(isset($uncertifiedByBarangay) && count($uncertifiedByBarangay) > 0)
                            @foreach($uncertifiedByBarangay as $idx => $bgy)
                                <tr>
                                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                                    <td><strong>Brgy. {{ $bgy['barangay'] }}</strong></td>
                                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ $bgy['count'] }}</td>
                                    <td style="font-size: 10.5px;">{{ implode(', ', $bgy['trades']) }}</td>
                                    <td style="text-align: center; font-weight: bold; font-size: 10px;">
                                        @if($bgy['count'] >= 2)
                                            <span style="color: #b91c1c;">HIGH PRIORITY</span>
                                        @else
                                            <span style="color: #0284c7;">STANDARD</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 10.5px;">Brgy. {{ $bgy['barangay'] }} Multi-Purpose Hall / Covered Court</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 15px;">Walang naitalang barangay breakdown.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- SECTION IV: Formal LGU Endorsement Statement -->
                <div class="doc-statement">
                    <strong>OFFICIAL ENDORSEMENT & STATUTORY CERTIFICATION:</strong><br>
                    Pursuant to the provisions of <strong>Republic Act No. 8759</strong> (Public Employment Service Office Act of 1999 as amended by R.A. 10691) and <strong>Republic Act No. 7796</strong> (Technical Education and Skills Development Act of 1994), the Public Employment Service Office (PESO) of the Municipality of Magalang hereby formally transmits and endorses the skilled tradespersons and workforce requirements enumerated above to the <strong>Technical Education and Skills Development Authority (TESDA) Pampanga Provincial Office</strong>. 
                    <br><br>
                    The data above is verified and compiled dynamically through the <strong>SKILLINK Magalang Municipal Labor Registry</strong>. To advance community livelihood development, elevate technical workmanship standards, and protect client households, the Local Government Unit of Magalang respectfully requests the allocation of <strong>Community-Based Training Programs (CBTP)</strong>, <strong>Mobile Training Programs (MTP)</strong>, and <strong>TESDA Competency Assessment Centers (NC II / NC III)</strong> for these identified tradespersons. The Municipal Government of Magalang pledges full administrative, mobilization, and venue support.
                </div>

                <!-- SECTION V: Signatories Block -->
                <div class="signatories-container">
                    <div class="signatory-box">
                        <div style="font-size: 10px; color: #555; text-transform: uppercase;">Prepared & Evaluated By:</div>
                        <div class="signatory-line"></div>
                        <div class="signatory-name">PESO Skills Profiling Officer</div>
                        <div class="signatory-title">Labor Market Information (LMI) & Skills Division<br>PESO Municipality of Magalang</div>
                    </div>
                    <div class="signatory-box">
                        <div style="font-size: 10px; color: #555; text-transform: uppercase;">Certified Correct & Endorsed By:</div>
                        <div class="signatory-line"></div>
                        <div class="signatory-name">Municipal PESO Manager</div>
                        <div class="signatory-title">Head of Office &bull; Public Employment Service Office<br>Municipality of Magalang, Pampanga</div>
                    </div>
                    <div class="signatory-box">
                        <div style="font-size: 10px; color: #555; text-transform: uppercase;">Approved & Formally Endorsed By:</div>
                        <div class="signatory-line"></div>
                        <div class="signatory-name">Municipal Mayor</div>
                        <div class="signatory-title">Office of the Municipal Mayor<br>Municipality of Magalang, Province of Pampanga</div>
                    </div>
                </div>

                <div style="margin-top: 25px; padding-top: 10px; border-top: 1px dashed #cbd5e1; display: flex; justify-content: space-between; font-size: 10px; color: #64748b;">
                    <div>Document Control: TESDA-SOP-CACO-07-F21 &bull; System: SKILLINK Magalang</div>
                    <div>Page 1 of 1 &bull; Verified Accurate via Live Municipal Labor Database</div>
                </div>

            </div>

            <!-- =========================================================================
                 INTERACTIVE DASHBOARD VIEW (ON SCREEN)
                 ========================================================================= -->
            
            <!-- Executive KPI Cards -->
            <div class="kpi-grid no-print">
                <!-- 1. Uncertified Skilled Workers (No TESDA NC) -->
                <div class="kpi-card red" id="cardUncertified">
                    <div class="kpi-title" style="color: #fca5a5;"><i class="fa-solid fa-user-xmark"></i> Uncertified Skilled Workers</div>
                    <div class="kpi-value" id="statUncertifiedCount" style="color: #f87171;">{{ number_format($uncertifiedCount ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #fca5a5;">Walang TESDA NC I, NC II, NC III</strong> &bull;
                        <span>Target for Skills Training</span>
                    </div>
                </div>

                <!-- 2. Certified TESDA Workers -->
                <div class="kpi-card green" id="cardCertified">
                    <div class="kpi-title" style="color: #86efac;"><i class="fa-solid fa-award"></i> Certified TESDA Workers</div>
                    <div class="kpi-value" id="statCertifiedCount" style="color: #4ade80;">{{ number_format($certifiedCount ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #86efac;">Verified NC Holders</strong> &bull;
                        <span>Accredited Skilled Labor</span>
                    </div>
                </div>

                <!-- 3. Total Skilled Workforce -->
                <div class="kpi-card blue" id="cardTotal">
                    <div class="kpi-title" style="color: #93c5fd;"><i class="fa-solid fa-users-gear"></i> Total Skilled Workforce</div>
                    <div class="kpi-value" id="statTotalWorkers" style="color: #60a5fa;">{{ number_format($totalWorkers ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong id="statCertRateText" style="color: #93c5fd;">{{ $certificationRate ?? 0 }}% Certified</strong> &bull;
                        <span>Registered in Magalang</span>
                    </div>
                </div>

                <!-- 4. Qualifications Needing Training -->
                <div class="kpi-card purple" id="cardTrades">
                    <div class="kpi-title" style="color: #d8b4fe;"><i class="fa-solid fa-graduation-cap"></i> Target Qualifications</div>
                    <div class="kpi-value" id="statPrioritySkillsCount" style="color: #c084fc;">{{ number_format(count($qualificationsNeeded ?? [])) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #d8b4fe;">Official NC II Courses</strong> &bull;
                        <span>To Request from TESDA</span>
                    </div>
                </div>
            </div>

            <!-- CARD 1: Priority TESDA NC Qualifications Table (The core Option B feature on screen) -->
            <div class="card no-print">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3><i class="fa-solid fa-certificate" style="color: #fde047;"></i> Priority TESDA NC Qualifications & Training Needs</h3>
                        <p style="font-size: 13px; color: #cbd5e1; margin-top: 3px;">Bilang ng mga skilled workers na nangangailangan ng opisyal na TESDA National Certificate (NC II) na ipapasa ng PESO sa TESDA Provincial Office para buksan ang assessment at training program.</p>
                    </div>
                    <span style="font-size: 12px; background: rgba(37,99,235,0.3); border: 1px solid #3b82f6; color: #93c5fd; padding: 5px 12px; border-radius: 20px;">
                        <i class="fa-solid fa-stamp"></i> Form: TESDA-SOP-CACO-07-F21
                    </span>
                </div>
                <hr>

                <div style="overflow-x: auto;">
                    <table class="report-table" id="screenQualificationsTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Priority TESDA Qualification Title</th>
                                <th style="text-align: center;">Course Code</th>
                                <th>Industry Sector</th>
                                <th style="text-align: center; color: #fca5a5;">Number of Workers Needing Certification</th>
                                <th style="text-align: center;">Demand Share</th>
                                <th>Recommended Training Delivery Mode</th>
                                <th style="text-align: center;">Priority</th>
                            </tr>
                        </thead>
                        <tbody id="screenQualificationsTableBody">
                            @if(isset($qualificationsNeeded) && count($qualificationsNeeded) > 0)
                                @foreach($qualificationsNeeded as $q)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: #93c5fd;">{{ $q['item_no'] }}</td>
                                        <td>
                                            <strong style="color: white; font-size: 13.5px;">{{ $q['qualification_title'] }}</strong>
                                            <div style="font-size: 11px; color: #94a3b8;">Mga Trabaho: {{ $q['raw_trades'] }}</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 2px 7px; border-radius: 4px; font-family: monospace; font-size: 11.5px; color: #fde047;">
                                                {{ $q['course_code'] }}
                                            </span>
                                        </td>
                                        <td><span style="color: #cbd5e1;">{{ $q['sector'] }}</span></td>
                                        <td style="text-align: center;">
                                            <span style="background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid #ef4444; padding: 4px 12px; border-radius: 8px; font-weight: 900; font-size: 14px; display: inline-block;">
                                                {{ $q['worker_count'] }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; font-weight: bold; color: #93c5fd;">
                                            {{ $q['share_percentage'] }}%
                                        </td>
                                        <td>
                                            <span style="font-size: 12px; color: #cbd5e1;">
                                                <i class="fa-solid fa-graduation-cap" style="color: #60a5fa; margin-right: 4px;"></i>
                                                {{ $q['delivery_mode'] }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            @if($q['priority_level'] === 'URGENT')
                                                <span class="badge-urgent"><i class="fa-solid fa-triangle-exclamation"></i> Urgent</span>
                                            @elseif($q['priority_level'] === 'HIGH')
                                                <span class="badge-high"><i class="fa-solid fa-arrow-up"></i> High</span>
                                            @else
                                                <span class="badge-standard"><i class="fa-solid fa-check"></i> Standard</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 25px; opacity: 0.7;">
                                        <i class="fa-solid fa-circle-check" style="color: #4ade80; font-size: 24px;"></i>
                                        <p style="margin-top: 6px;">Lahat ng skilled workers ay may sertipikasyon mula sa TESDA.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CARD 2: Competency Ratio & Training Action Plan -->
            <div class="card no-print">
                <h3><i class="fa-solid fa-chart-line" style="color: #facc15;"></i> TESDA Accreditation & Competency Gap Assessment</h3>
                <p style="font-size: 13px; color: #cbd5e1; margin-top: 4px;">Pagsusuri sa kakayahan ng mga manggagawa upang malaman ng PESO Magalang kung anong technical training modules ang kailangang i-request sa TESDA para sa mga wala pang National Certificate (NC I, NC II, NC III).</p>
                <hr>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div style="background: rgba(255,255,255,0.05); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12);">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">TESDA Certification Progress Ratio</div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 14px; font-weight: bold;">Municipal Accredited Rate</span>
                            <span style="font-size: 16px; font-weight: 900; color: #86efac;" id="statCertRate">{{ $certificationRate ?? 0 }}%</span>
                        </div>
                        <div style="background: rgba(255,255,255,0.15); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                            <div id="statCertProgressBar" style="background: #10b981; width: {{ $certificationRate ?? 0 }}%; height: 100%; transition: width 0.4s ease;"></div>
                        </div>
                        <p style="font-size: 11.5px; color: #cbd5e1; line-height: 1.5;" id="statRatioText">
                            Sa kabuuang <strong>{{ $totalWorkers ?? 0 }}</strong> rehistradong skilled workers sa Magalang, <strong>{{ $certifiedCount ?? 0 }}</strong> ang may beripikadong TESDA NC, habang <strong>{{ $uncertifiedCount ?? 0 }}</strong> ang target na isailalim sa skills upskilling seminar at assessment.
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12);">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">PESO Skills Training Action Plan</div>
                        <div style="font-size: 13px; color: #fde047; font-weight: bold; margin-bottom: 6px;">
                            <i class="fa-solid fa-bell"></i> Target Priority: Upskilling for Low-Rated & Non-Certified Workers
                        </div>
                        <p style="font-size: 11.5px; color: #cbd5e1; line-height: 1.5;">
                            Kung si Skilled Worker ay hindi nakakakuha ng mataas na rating mula sa HouseHold Clients o wala pang hawak na certificate, magsasagawa ang PESO Magalang ng coordinated training kasama ang TESDA Provincial Training Center upang mahasa at maging kwalipikado sa NC I/II/III.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 3: Detailed List of Skilled Workers Without TESDA Certificate -->
            <div class="card no-print">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3><i class="fa-solid fa-user-xmark" style="color: #ef4444;"></i> Skilled Workers Without Certified TESDA Certificate</h3>
                        <p style="font-size: 12.5px; color: #cbd5e1; margin-top: 3px;">Talaan ng mga skilled workers at kanilang mga service offers / technical trades na wala pang TESDA NC certification upang matukoy ng PESO kung anong training ang hihilingin sa TESDA.</p>
                    </div>
                    <span style="font-size: 11.5px; background: rgba(239, 68, 68, 0.25); border: 1px solid #ef4444; color: #fca5a5; padding: 4px 10px; border-radius: 20px;">
                        <i class="fa-solid fa-list-check"></i> <span id="badgeUncertifiedCount">{{ count($uncertifiedWorkers ?? []) }}</span> Workers Needing Certification
                    </span>
                </div>
                <hr>

                <div style="overflow-x: auto;">
                    <table class="report-table" id="uncertifiedWorkersTable">
                        <thead>
                            <tr>
                                <th>Worker Name & Profile</th>
                                <th>Service Offer / Trade Skills</th>
                                <th>Barangay</th>
                                <th style="text-align: center;">HouseHold Rating</th>
                                <th style="text-align: center;">TESDA Certificate Status</th>
                                <th>Recommended Action for PESO</th>
                            </tr>
                        </thead>
                        <tbody id="uncertifiedWorkersTableBody">
                            @if(isset($uncertifiedWorkers) && count($uncertifiedWorkers) > 0)
                                @foreach($uncertifiedWorkers as $worker)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(59, 130, 246, 0.3); border: 1px solid #60a5fa; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; color: white;">
                                                    {{ strtoupper(substr($worker->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ trim(($worker->first_name ?? '') . ' ' . ($worker->last_name ?? '')) ?: $worker->name }}</strong>
                                                    <div style="font-size: 11px; opacity: 0.7;">@ {{ $worker->name }} &bull; {{ $worker->contact_number ?: $worker->email ?: 'No contact' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid rgba(96, 165, 250, 0.4); padding: 4px 9px; border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-block;">
                                                <i class="fa-solid fa-screwdriver-wrench"></i> {{ $worker->skills ?: 'General Handyman' }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fa-solid fa-location-dot" style="color: #f87171; margin-right: 4px;"></i>
                                            Brgy. {{ $worker->barangay ?: 'Magalang' }}
                                        </td>
                                        <td style="text-align: center;">
                                            @php $ratingVal = (float)($worker->rating ?? 5.0); @endphp
                                            <span style="font-weight: bold; color: {{ $ratingVal >= 4.5 ? '#facc15' : ($ratingVal >= 3.5 ? '#60a5fa' : '#f87171') }}; font-size: 13px;">
                                                ★ {{ number_format($ratingVal, 1) }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; display: inline-block;">
                                                <i class="fa-solid fa-xmark"></i> No TESDA NC I/II/III
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: #93c5fd; font-size: 12px; font-weight: 500;">
                                                <i class="fa-solid fa-graduation-cap" style="color: #60a5fa;"></i> Request TESDA {{ strtok($worker->skills ?: 'Handyman', ',') }} NC Assessment
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 25px; opacity: 0.7;">
                                        <i class="fa-solid fa-circle-check" style="color: #4ade80; font-size: 24px;"></i>
                                        <p style="margin-top: 6px;">Lahat ng skilled workers ay may sertipikasyon mula sa TESDA.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CARD 4: Two-Column Breakdowns (Barangay Distribution + Trade Training Demands) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px;" class="no-print">

                <!-- Barangay Record with Most Uncertified Skilled Workers -->
                <div class="card">
                    <h3><i class="fa-solid fa-map-location-dot" style="color: #fbbf24;"></i> Barangay Records: Uncertified Workers (Magalang)</h3>
                    <p style="font-size: 12.5px; color: #cbd5e1; margin-top: 3px;">Talaan kung saang barangay ang may pinakamaraming skilled workers na walang Certified TESDA certificate para sa localized training scheduling.</p>
                    <hr>

                    <div style="overflow-x: auto;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th style="text-align: right;">Uncertified Workers</th>
                                    <th>Service Trades Needing Training</th>
                                    <th style="text-align: right;">Priority Level</th>
                                </tr>
                            </thead>
                            <tbody id="barangayTableBody">
                                @if(isset($uncertifiedByBarangay) && count($uncertifiedByBarangay) > 0)
                                    @foreach($uncertifiedByBarangay as $bgy)
                                        <tr>
                                            <td>
                                                <i class="fa-solid fa-location-dot" style="color: #f87171; margin-right: 6px;"></i>
                                                <strong>Brgy. {{ $bgy['barangay'] }}</strong>
                                            </td>
                                            <td style="text-align: right; font-weight: bold; color: #f87171; font-size: 14px;">
                                                {{ $bgy['count'] }}
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                                    @foreach($bgy['trades'] as $tr)
                                                        <span style="font-size: 10.5px; background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px; color: #cbd5e1;">{{ $tr }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td style="text-align: right;">
                                                @if($bgy['count'] >= 2)
                                                    <span style="background: rgba(239,68,68,0.25); color: #fca5a5; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">High Priority</span>
                                                @else
                                                    <span style="background: rgba(245,158,11,0.2); color: #fde047; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">Priority</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 20px; opacity: 0.7;">Walang naitalang uncertified worker sa mga barangay.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Priority Service Trades to Request from TESDA -->
                <div class="card">
                    <h3><i class="fa-solid fa-screwdriver-wrench" style="color: #60a5fa;"></i> Services to Request for TESDA Training</h3>
                    <p style="font-size: 12.5px; color: #cbd5e1; margin-top: 3px;">Kabuuan ng mga serbisyo at specialized skills na walang certification para sa pagbuo ng municipal training modules.</p>
                    <hr>

                    <div style="overflow-x: auto;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Service Offer / Trade Category</th>
                                    <th style="text-align: right;">Workers Needing Training</th>
                                    <th style="text-align: right;">Training Demand Share</th>
                                </tr>
                            </thead>
                            <tbody id="tradesTableBody">
                                @if(isset($tradesNeedingTraining) && count($tradesNeedingTraining) > 0)
                                    @php $totalUncertTrades = array_sum($tradesNeedingTraining); @endphp
                                    @foreach($tradesNeedingTraining as $tradeName => $count)
                                        @php
                                            $share = $totalUncertTrades > 0 ? round(($count / $totalUncertTrades) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <i class="fa-solid fa-wrench" style="color: #60a5fa; margin-right: 6px;"></i>
                                                <strong>{{ $tradeName }}</strong>
                                            </td>
                                            <td style="text-align: right; font-weight: bold; color: #f87171; font-size: 14px;">
                                                {{ $count }}
                                            </td>
                                            <td style="text-align: right;">
                                                <span style="background: rgba(37,99,235,0.25); color: #93c5fd; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">
                                                    {{ $share }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 20px; opacity: 0.7;">No trade training demand recorded.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const sbo = document.getElementById('sidebarOverlay');
            if (sb) sb.classList.toggle('active');
            if (sbo) sbo.classList.toggle('active');
        }

        // Toggle printable endorsement preview on screen
        function toggleDocPreview() {
            const doc = document.getElementById('officialDocContainer');
            const btn = document.getElementById('btnTogglePreview');
            if (!doc) return;

            if (doc.style.display === 'block') {
                doc.style.display = 'none';
                if (btn) btn.innerHTML = '<i class="fa-solid fa-file-contract"></i> Preview Official Endorsement Form';
            } else {
                doc.style.display = 'block';
                doc.scrollIntoView({ behavior: 'smooth' });
                if (btn) btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Hide Official Preview';
            }
        }

        // Live Real-Time Synchronizer for TESDA Analytics Reports
        let lastUncertifiedCount = {{ $uncertifiedCount ?? 0 }};
        let lastCertifiedCount = {{ $certifiedCount ?? 0 }};

        function pollTesdaLiveStats() {
            fetch("{{ route('admin.tesda_reports.live_data') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) return;

                const elUncert = document.getElementById('statUncertifiedCount');
                const elCert = document.getElementById('statCertifiedCount');
                const elTotal = document.getElementById('statTotalWorkers');
                const elPriority = document.getElementById('statPrioritySkillsCount');
                const elRate = document.getElementById('statCertRate');
                const elRateText = document.getElementById('statCertRateText');
                const elProgressBar = document.getElementById('statCertProgressBar');
                const elRatioText = document.getElementById('statRatioText');
                const elBadgeUncert = document.getElementById('badgeUncertifiedCount');

                // Update Print KPIs
                const pTotal = document.getElementById('pDocTotal');
                const pCert = document.getElementById('pDocCert');
                const pUncert = document.getElementById('pDocUncert');
                const pRate = document.getElementById('pDocRate');
                const pTotalWorkersQual = document.getElementById('docTotalQualificationsWorkers');

                if (elUncert) elUncert.textContent = data.uncertifiedCount;
                if (elCert) elCert.textContent = data.certifiedCount;
                if (elTotal) elTotal.textContent = data.totalWorkers;
                if (elPriority && data.qualificationsNeeded) elPriority.textContent = data.qualificationsNeeded.length;
                if (elRate) elRate.textContent = data.certificationRate + '%';
                if (elRateText) elRateText.textContent = data.certificationRate + '% Certified';
                if (elProgressBar) elProgressBar.style.width = data.certificationRate + '%';
                if (elBadgeUncert) elBadgeUncert.textContent = data.uncertifiedCount;

                if (pTotal) pTotal.textContent = data.totalWorkers;
                if (pCert) pCert.textContent = data.certifiedCount;
                if (pUncert) pUncert.textContent = data.uncertifiedCount;
                if (pRate) pRate.textContent = data.certificationRate + '%';
                if (pTotalWorkersQual) pTotalWorkersQual.textContent = data.uncertifiedCount;

                if (elRatioText) {
                    elRatioText.innerHTML = `Sa kabuuang <strong>${data.totalWorkers}</strong> rehistradong skilled workers sa Magalang, <strong>${data.certifiedCount}</strong> ang may beripikadong TESDA NC, habang <strong>${data.uncertifiedCount}</strong> ang target na isailalim sa skills upskilling seminar at assessment.`;
                }

                if (data.uncertifiedCount !== lastUncertifiedCount || data.certifiedCount !== lastCertifiedCount) {
                    lastUncertifiedCount = data.uncertifiedCount;
                    lastCertifiedCount = data.certifiedCount;
                    renderUncertifiedTable(data.uncertifiedWorkers);
                    renderBarangayTable(data.uncertifiedByBarangay);
                    renderTradesTable(data.tradesNeedingTraining);
                    if (data.qualificationsNeeded) {
                        renderQualificationsTable(data.qualificationsNeeded);
                    }
                }
            })
            .catch(err => console.debug('Live TESDA sync error:', err));
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function renderQualificationsTable(quals) {
            const screenBody = document.getElementById('screenQualificationsTableBody');
            const docBody = document.getElementById('docQualificationsTableBody');

            if (!quals || quals.length === 0) {
                if (screenBody) screenBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 25px; opacity: 0.7;"><i class="fa-solid fa-circle-check" style="color: #4ade80; font-size: 24px;"></i><p style="margin-top: 6px;">Lahat ng skilled workers ay may sertipikasyon mula sa TESDA.</p></td></tr>`;
                if (docBody) docBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 15px;">Walang naitalang manggagawa na nangangailangan ng NC certification sa kasalukuyan.</td></tr>`;
                return;
            }

            let sHtml = '';
            let dHtml = '';
            let totalWorkers = 0;

            quals.forEach(q => {
                totalWorkers += q.worker_count;
                const priorityBadge = q.priority_level === 'URGENT'
                    ? `<span class="badge-urgent"><i class="fa-solid fa-triangle-exclamation"></i> Urgent</span>`
                    : (q.priority_level === 'HIGH' 
                        ? `<span class="badge-high"><i class="fa-solid fa-arrow-up"></i> High</span>` 
                        : `<span class="badge-standard"><i class="fa-solid fa-check"></i> Standard</span>`);

                const docPriority = q.priority_level === 'URGENT'
                    ? `<span style="color: #b91c1c;">URGENT</span>`
                    : (q.priority_level === 'HIGH' ? `<span style="color: #d97706;">HIGH</span>` : `<span style="color: #2563eb;">STANDARD</span>`);

                sHtml += `<tr>
                    <td style="text-align: center; font-weight: bold; color: #93c5fd;">${q.item_no}</td>
                    <td>
                        <strong style="color: white; font-size: 13.5px;">${escapeHtml(q.qualification_title)}</strong>
                        <div style="font-size: 11px; color: #94a3b8;">Mga Trabaho: ${escapeHtml(q.raw_trades)}</div>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 2px 7px; border-radius: 4px; font-family: monospace; font-size: 11.5px; color: #fde047;">
                            ${escapeHtml(q.course_code)}
                        </span>
                    </td>
                    <td><span style="color: #cbd5e1;">${escapeHtml(q.sector)}</span></td>
                    <td style="text-align: center;">
                        <span style="background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid #ef4444; padding: 4px 12px; border-radius: 8px; font-weight: 900; font-size: 14px; display: inline-block;">
                            ${q.worker_count}
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #93c5fd;">${q.share_percentage}%</td>
                    <td>
                        <span style="font-size: 12px; color: #cbd5e1;">
                            <i class="fa-solid fa-graduation-cap" style="color: #60a5fa; margin-right: 4px;"></i>
                            ${escapeHtml(q.delivery_mode)}
                        </span>
                    </td>
                    <td style="text-align: center;">${priorityBadge}</td>
                </tr>`;

                dHtml += `<tr>
                    <td style="text-align: center; font-weight: bold;">${q.item_no}</td>
                    <td>
                        <strong>${escapeHtml(q.qualification_title)}</strong>
                        <div style="font-size: 10px; color: #555;">Represented Skills: ${escapeHtml(q.raw_trades)}</div>
                    </td>
                    <td style="text-align: center; font-family: monospace; font-weight: bold;">${escapeHtml(q.course_code)}</td>
                    <td>${escapeHtml(q.sector)}</td>
                    <td class="highlight-num">${q.worker_count}</td>
                    <td style="text-align: center; font-weight: bold;">${q.share_percentage}%</td>
                    <td style="font-size: 10.5px;">${escapeHtml(q.delivery_mode)}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 10px;">${docPriority}</td>
                </tr>`;
            });

            dHtml += `<tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4" style="text-align: right; padding-right: 12px; text-transform: uppercase;">
                    Total Uncertified Candidates Endorsed for Training / Assessment:
                </td>
                <td class="highlight-num">${totalWorkers}</td>
                <td style="text-align: center;">100%</td>
                <td colspan="2" style="font-size: 10.5px; color: #002060;">Comprehensive Endorsement Roster</td>
            </tr>`;

            if (screenBody) screenBody.innerHTML = sHtml;
            if (docBody) docBody.innerHTML = dHtml;
        }

        function renderUncertifiedTable(workers) {
            const tbody = document.getElementById('uncertifiedWorkersTableBody');
            if (!tbody) return;

            if (!workers || workers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 25px; opacity: 0.7;"><i class="fa-solid fa-circle-check" style="color: #4ade80; font-size: 24px;"></i><p style="margin-top: 6px;">Lahat ng skilled workers ay may sertipikasyon mula sa TESDA.</p></td></tr>`;
                return;
            }

            let html = '';
            workers.forEach(w => {
                const initial = escapeHtml((w.name || 'W').charAt(0).toUpperCase());
                const firstSkill = (w.skills || 'Handyman').split(',')[0].trim();
                const rating = parseFloat(w.rating || 5.0).toFixed(1);
                const ratingColor = rating >= 4.5 ? '#facc15' : (rating >= 3.5 ? '#60a5fa' : '#f87171');

                html += `<tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(59, 130, 246, 0.3); border: 1px solid #60a5fa; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; color: white;">
                                ${initial}
                            </div>
                            <div>
                                <strong>${escapeHtml(w.full_name)}</strong>
                                <div style="font-size: 11px; opacity: 0.7;">@ ${escapeHtml(w.name)} &bull; ${escapeHtml(w.contact)}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid rgba(96, 165, 250, 0.4); padding: 4px 9px; border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-block;">
                            <i class="fa-solid fa-screwdriver-wrench"></i> ${escapeHtml(w.skills)}
                        </span>
                    </td>
                    <td>
                        <i class="fa-solid fa-location-dot" style="color: #f87171; margin-right: 4px;"></i>
                        Brgy. ${escapeHtml(w.barangay)}
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: bold; color: ${ratingColor}; font-size: 13px;">
                            ★ ${rating}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; display: inline-block;">
                            <i class="fa-solid fa-xmark"></i> No TESDA NC I/II/III
                        </span>
                    </td>
                    <td>
                        <span style="color: #93c5fd; font-size: 12px; font-weight: 500;">
                            <i class="fa-solid fa-graduation-cap" style="color: #60a5fa;"></i> Request TESDA ${escapeHtml(firstSkill)} NC Assessment
                        </span>
                    </td>
                </tr>`;
            });

            tbody.innerHTML = html;
        }

        function renderBarangayTable(barangays) {
            const tbody = document.getElementById('barangayTableBody');
            const docBgyBody = document.getElementById('docBarangayTableBody');
            if (!tbody || !barangays) return;

            let html = '';
            let dHtml = '';
            barangays.forEach((b, idx) => {
                const priorityBadge = b.count >= 2 
                    ? `<span style="background: rgba(239,68,68,0.25); color: #fca5a5; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">High Priority</span>`
                    : `<span style="background: rgba(245,158,11,0.2); color: #fde047; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">Priority</span>`;

                const tradesHtml = (b.trades || []).map(t => `<span style="font-size: 10.5px; background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px; color: #cbd5e1;">${escapeHtml(t)}</span>`).join(' ');

                html += `<tr>
                    <td>
                        <i class="fa-solid fa-location-dot" style="color: #f87171; margin-right: 6px;"></i>
                        <strong>Brgy. ${escapeHtml(b.barangay)}</strong>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #f87171; font-size: 14px;">
                        ${b.count}
                    </td>
                    <td>
                        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                            ${tradesHtml}
                        </div>
                    </td>
                    <td style="text-align: right;">${priorityBadge}</td>
                </tr>`;

                dHtml += `<tr>
                    <td style="text-align: center;">${idx + 1}</td>
                    <td><strong>Brgy. ${escapeHtml(b.barangay)}</strong></td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">${b.count}</td>
                    <td style="font-size: 10.5px;">${escapeHtml((b.trades || []).join(', '))}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 10px;">
                        ${b.count >= 2 ? '<span style="color: #b91c1c;">HIGH PRIORITY</span>' : '<span style="color: #0284c7;">STANDARD</span>'}
                    </td>
                    <td style="font-size: 10.5px;">Brgy. ${escapeHtml(b.barangay)} Multi-Purpose Hall / Covered Court</td>
                </tr>`;
            });
            tbody.innerHTML = html;
            if (docBgyBody) docBgyBody.innerHTML = dHtml;
        }

        function renderTradesTable(trades) {
            const tbody = document.getElementById('tradesTableBody');
            if (!tbody || !trades) return;

            let total = 0;
            for (let k in trades) { total += trades[k]; }

            let html = '';
            for (let tradeName in trades) {
                const count = trades[tradeName];
                const share = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                html += `<tr>
                    <td>
                        <i class="fa-solid fa-wrench" style="color: #60a5fa; margin-right: 6px;"></i>
                        <strong>${escapeHtml(tradeName)}</strong>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #f87171; font-size: 14px;">
                        ${count}
                    </td>
                    <td style="text-align: right;">
                        <span style="background: rgba(37,99,235,0.25); color: #93c5fd; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">
                            ${share}%
                        </span>
                    </td>
                </tr>`;
            }
            tbody.innerHTML = html;
        }

        setInterval(pollTesdaLiveStats, 5000);
    </script>
</body>
</html>