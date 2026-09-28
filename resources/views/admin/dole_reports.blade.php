<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - TESDA Analytics Reports</title>
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

        .btn { padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-primary { background: #2563eb; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-print { background: #10b981; color: white; border: 1px solid #34d399; }
        .btn-print:hover { background: #059669; }

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

        /* Standardized Compact Sidebar */
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
        .sidebar.active { left: 0; }
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
            display: none;
        }
        .sidebar-overlay.active { display: block; }
        .sidebar-profile { padding: 16px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.15); }
        .sidebar-avatar { width: 54px; height: 54px; border-radius: 50%; border: 2px solid white; object-fit: cover; margin-bottom: 8px; }
        .sidebar-avatar-fallback {
            width: 54px; height: 54px; border-radius: 50%;
            background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.8);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px auto; color: white; font-size: 24px;
        }
        .sidebar-profile .name { font-size: 14px; font-weight: bold; color: white; }
        .sidebar-profile .role { font-size: 11px; color: #93c5fd; }
        .role-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; color: white; margin-top: 3px; }
        .sidebar-menu { list-style: none; padding: 10px 0; flex: 1; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: white; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.18); border-left: 4px solid #60a5fa; }
        .sidebar-menu li a i { width: 18px; text-align: center; font-size: 14px; }
        .sidebar-footer { padding: 12px 18px; border-top: 1px solid rgba(255,255,255,0.15); }
        .logout-btn { display: flex; align-items: center; gap: 8px; color: #fca5a5; text-decoration: none; font-size: 13px; font-weight: bold; }

        /* Report Tables */
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .report-table th, .report-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.15); vertical-align: middle; }
        .report-table th { background: rgba(255,255,255,0.08); color: #93c5fd; font-weight: bold; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .report-table tr:hover td { background: rgba(255,255,255,0.04); }

        /* Official Print Styles */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .header, .sidebar, .sidebar-overlay, .btn-print, .back-link, .no-print {
                display: none !important;
            }
            .page-content {
                padding: 0 !important;
            }
            .overlay { display: none !important; }
            .card {
                background: white !important;
                color: black !important;
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                margin-bottom: 15px !important;
                padding: 15px !important;
            }
            .card h3, .page-title h2, .kpi-value {
                color: black !important;
            }
            .kpi-card {
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: black !important;
            }
            .kpi-title, .kpi-sub {
                color: #475569 !important;
            }
            .report-table th {
                background: #e2e8f0 !important;
                color: black !important;
                border-bottom: 2px solid #94a3b8 !important;
            }
            .report-table td {
                color: black !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }
            .official-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 12px;
            }
            .print-signoff {
                display: block !important;
            }
        }
        .official-header {
            display: none;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK - MUNICIPALITY OF MAGALANG</h1>
                <p>Public Employment Service Office (PESO) Administrator Portal</p>
            </div>
        </div>
        <div style="font-size: 13px; font-weight: bold; background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 6px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-certificate" style="color: #fde047;"></i> TESDA Report System
        </div>
    </header>

    @include('partials.sidebar_admin', ['active' => 'tesda_reports'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <!-- Official Print Header (Visible only when printed) -->
            <div class="official-header">
                <p style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Republic of the Philippines &bull; Province of Pampanga</p>
                <p style="font-size: 13px; font-weight: bold; text-transform: uppercase;">Technical Education and Skills Development Authority (TESDA) &bull; PESO Magalang</p>
                <h2 style="font-size: 18px; margin: 6px 0; font-weight: 900;">MUNICIPAL WORKFORCE TESDA ACCREDITATION & SKILLS TRAINING READINESS REPORT</h2>
                <p style="font-size: 11px; color: #555;">Generated on {{ now()->format('F d, Y - h:i A') }} | Platform: SKILLINK Magalang &bull; Office of the Municipal Mayor</p>
            </div>

            <div class="page-title no-print">
                <div>
                    <h2><i class="fa-solid fa-chart-pie" style="color: #60a5fa;"></i> TESDA Analytics Reports</h2>
                    <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Technical Education and Skills Development Authority (TESDA) &bull; Local Skilled Workforce Competency & Skills Training Assessment</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('dashboard.Admin') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                    <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> Print Official Report</button>
                </div>
            </div>

            <!-- Executive KPI Cards -->
            <div class="kpi-grid">
                <!-- 1. Uncertified Skilled Workers (No TESDA NC) -->
                <div class="kpi-card red" id="cardUncertified">
                    <div class="kpi-title" style="color: #fca5a5;"><i class="fa-solid fa-user-xmark"></i> Uncertified Skilled Workers</div>
                    <div class="kpi-value" id="statUncertifiedCount" style="color: #f87171;">{{ number_format($uncertifiedCount ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #fca5a5;">Walang TESDA NC I, NC II, NC III</strong> &bull;
                        <span>Target for Skills Training</span>
                    </div>
                </div>

                <!-- 2. Certified TESDA Workers (Replacing Household Clients) -->
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

                <!-- 4. Services / Trades Needing Training -->
                <div class="kpi-card purple" id="cardTrades">
                    <div class="kpi-title" style="color: #d8b4fe;"><i class="fa-solid fa-graduation-cap"></i> Trades Needing Training</div>
                    <div class="kpi-value" id="statPrioritySkillsCount" style="color: #c084fc;">{{ number_format($prioritySkillsCount ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #d8b4fe;">Priority Skills Modules</strong> &bull;
                        <span>To Request from TESDA</span>
                    </div>
                </div>
            </div>

            <!-- Section: TESDA Competency Gap & Training Assessment -->
            <div class="card">
                <h3><i class="fa-solid fa-certificate" style="color: #facc15;"></i> TESDA Accreditation & Competency Gap Assessment</h3>
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

            <!-- Lower Section 1: Detailed List of Skilled Workers Without TESDA Certificate & Their Service Offers -->
            <div class="card">
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

            <!-- Lower Section 2: Two-Column Breakdowns (Barangay Distribution + Trade Training Demands) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px;">

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

            <!-- Sign-off Section (Visible in Print) -->
            <div style="margin-top: 30px; display: none;" class="print-signoff">
                <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">PESO Skills & Training Coordinator</p>
                        <p style="font-size: 10px; color: #555;">Prepared & Evaluated By</p>
                    </div>
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">Municipal PESO Manager</p>
                        <p style="font-size: 10px; color: #555;">Noted & Endorsed By</p>
                    </div>
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">TESDA Provincial Director / Specialist</p>
                        <p style="font-size: 10px; color: #555;">Received for Skills Assessment & Training</p>
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

                if (elUncert) elUncert.textContent = data.uncertifiedCount;
                if (elCert) elCert.textContent = data.certifiedCount;
                if (elTotal) elTotal.textContent = data.totalWorkers;
                if (elPriority) elPriority.textContent = data.prioritySkillsCount;
                if (elRate) elRate.textContent = data.certificationRate + '%';
                if (elRateText) elRateText.textContent = data.certificationRate + '% Certified';
                if (elProgressBar) elProgressBar.style.width = data.certificationRate + '%';
                if (elBadgeUncert) elBadgeUncert.textContent = data.uncertifiedCount;

                if (elRatioText) {
                    elRatioText.innerHTML = `Sa kabuuang <strong>${data.totalWorkers}</strong> rehistradong skilled workers sa Magalang, <strong>${data.certifiedCount}</strong> ang may beripikadong TESDA NC, habang <strong>${data.uncertifiedCount}</strong> ang target na isailalim sa skills upskilling seminar at assessment.`;
                }

                // If count changed, dynamically update table
                if (data.uncertifiedCount !== lastUncertifiedCount || data.certifiedCount !== lastCertifiedCount) {
                    lastUncertifiedCount = data.uncertifiedCount;
                    lastCertifiedCount = data.certifiedCount;
                    renderUncertifiedTable(data.uncertifiedWorkers);
                    renderBarangayTable(data.uncertifiedByBarangay);
                    renderTradesTable(data.tradesNeedingTraining);
                }
            })
            .catch(err => console.debug('Live TESDA sync error:', err));
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
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
            if (!tbody || !barangays) return;

            let html = '';
            barangays.forEach(b => {
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
            });
            tbody.innerHTML = html;
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
