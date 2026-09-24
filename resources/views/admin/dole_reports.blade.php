<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - DOLE Employment & Analytics Report</title>
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
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 5px; height: 100%;
        }
        .kpi-card.blue::before { background: #3b82f6; }
        .kpi-card.green::before { background: #10b981; }
        .kpi-card.amber::before { background: #f59e0b; }
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
        .report-table th, .report-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.15); }
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
                margin-bottom: 25px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
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
        <div style="font-size: 13px; font-weight: bold; background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 6px;">
            <i class="fa-solid fa-file-contract" style="color: #93c5fd;"></i> DOLE Report System
        </div>
    </header>

    @include('partials.sidebar_admin', ['active' => 'dole_reports'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <!-- Official Print Header (Visible only when printed) -->
            <div class="official-header">
                <p style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Republic of the Philippines</p>
                <p style="font-size: 13px; font-weight: bold; text-transform: uppercase;">Department of Labor and Employment (DOLE) &bull; PESO Magalang</p>
                <h2 style="font-size: 18px; margin: 5px 0; font-weight: 900;">MONTHLY EMPLOYMENT FACILITATION & WORKFORCE COMPLIANCE REPORT</h2>
                <p style="font-size: 11px; color: #555;">Generated on {{ now()->format('F d, Y - h:i A') }} | Platform: SKILLINK Magalang</p>
            </div>

            <div class="page-title no-print">
                <div>
                    <h2><i class="fa-solid fa-chart-line" style="color: #60a5fa;"></i> DOLE Employment & Analytics Report</h2>
                    <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Official municipal labor market analytics and workforce facilitation statistics for DOLE regulatory compliance.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('dashboard.Admin') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                    <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> Print Official Report</button>
                </div>
            </div>

            <!-- Executive KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card blue">
                    <div class="kpi-title"><i class="fa-solid fa-users-gear"></i> Total Skilled Workers</div>
                    <div class="kpi-value">{{ number_format($totalWorkers ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #86efac;">{{ $accreditedWorkers ?? 0 }} Accredited</strong> &bull;
                        <span style="color: #fde047;">{{ $pendingWorkers ?? 0 }} Pending Screening</span>
                    </div>
                </div>

                <div class="kpi-card green">
                    <div class="kpi-title"><i class="fa-solid fa-house-user"></i> Residential Clients</div>
                    <div class="kpi-value">{{ number_format($totalResidential ?? 0) }}</div>
                    <div class="kpi-sub">Households sourcing verified labor in Magalang</div>
                </div>

                <div class="kpi-card amber">
                    <div class="kpi-title"><i class="fa-solid fa-briefcase"></i> Total Direct Bookings</div>
                    <div class="kpi-value">{{ number_format($totalBookings ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #86efac;">{{ $completedBookings ?? 0 }} Completed</strong> &bull;
                        <span>{{ $employmentRate ?? 0 }}% Fulfillment Rate</span>
                    </div>
                </div>

                <div class="kpi-card purple">
                    <div class="kpi-title"><i class="fa-solid fa-shield-halved"></i> Grievance / Complaints</div>
                    <div class="kpi-value">{{ number_format($totalComplaints ?? 0) }}</div>
                    <div class="kpi-sub">
                        <strong style="color: #86efac;">{{ $resolvedComplaints ?? 0 }} Resolved (Case Close)</strong> &bull;
                        <span>{{ ($totalComplaints ?? 0) > 0 ? round((($resolvedComplaints ?? 0) / $totalComplaints) * 100, 1) : 100 }}% Settlement</span>
                    </div>
                </div>
            </div>

            <!-- Section: DOLE Regulatory Compliance & Labor Screening -->
            <div class="card">
                <h3><i class="fa-solid fa-award" style="color: #facc15;"></i> DOLE & TESDA Workforce Verification Audit</h3>
                <p style="font-size: 13px; color: #cbd5e1; margin-top: 4px;">Accreditation compliance status of local skilled labor verified through TESDA National Certificates (NC II/III) and PESO municipal background clearance.</p>
                <hr>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div style="background: rgba(255,255,255,0.05); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12);">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Worker Accreditation Ratio</div>
                        @php
                            $accRate = ($totalWorkers ?? 0) > 0 ? round((($accreditedWorkers ?? 0) / $totalWorkers) * 100, 1) : 0;
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 14px; font-weight: bold;">Accredited Labor Rate</span>
                            <span style="font-size: 16px; font-weight: 900; color: #86efac;">{{ $accRate }}%</span>
                        </div>
                        <div style="background: rgba(255,255,255,0.15); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                            <div style="background: #10b981; width: {{ $accRate }}%; height: 100%;"></div>
                        </div>
                        <p style="font-size: 11.5px; color: #94a3b8;">
                            Out of <strong>{{ $totalWorkers ?? 0 }}</strong> registered skilled workers, <strong>{{ $accreditedWorkers ?? 0 }}</strong> have completed full document validation (TESDA NC, Government ID, PESO Interview).
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12);">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Job Creation & Posting Volume</div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 14px; font-weight: bold;">Residential Job Vacancies</span>
                            <span style="font-size: 16px; font-weight: 900; color: #60a5fa;">{{ $totalJobPosts ?? 0 }} Posts</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 14px; font-weight: bold;">Direct Contract Bookings</span>
                            <span style="font-size: 16px; font-weight: 900; color: #f59e0b;">{{ $totalBookings ?? 0 }} Contracts</span>
                        </div>
                        <p style="font-size: 11.5px; color: #94a3b8; margin-top: 8px;">
                            High labor demand observed in residential maintenance, electrical repairs, and civil infrastructure trade lines in Magalang.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Two-Column Breakdowns -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px;">

                <!-- Top Trade Breakdown -->
                <div class="card">
                    <h3><i class="fa-solid fa-screwdriver-wrench" style="color: #60a5fa;"></i> Top Labor Trades & Demand Categories</h3>
                    <p style="font-size: 12.5px; color: #cbd5e1; margin-top: 3px;">Distribution of residential job requests categorized by technical trade specialization.</p>
                    <hr>

                    @if(isset($topTrades) && count($topTrades) > 0)
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Trade / Technical Category</th>
                                    <th style="text-align: right;">Job Posts</th>
                                    <th style="text-align: right;">Demand Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topTrades as $trade)
                                    @php
                                        $share = ($totalJobPosts ?? 0) > 0 ? round(($trade->count / $totalJobPosts) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $trade->category }}</strong>
                                        </td>
                                        <td style="text-align: right; font-weight: bold;">
                                            {{ $trade->count }}
                                        </td>
                                        <td style="text-align: right;">
                                            <span style="background: rgba(37,99,235,0.25); color: #93c5fd; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">
                                                {{ $share }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="text-align: center; padding: 20px; opacity: 0.7;">No trade categorization records available yet.</p>
                    @endif
                </div>

                <!-- Barangay Demographic Distribution -->
                <div class="card">
                    <h3><i class="fa-solid fa-map-location-dot" style="color: #10b981;"></i> Barangay Labor Demographics (Magalang)</h3>
                    <p style="font-size: 12.5px; color: #cbd5e1; margin-top: 3px;">Registered labor supply and workforce density across top Magalang barangays.</p>
                    <hr>

                    @if(isset($barangayStats) && count($barangayStats) > 0)
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th style="text-align: right;">Registered Users</th>
                                    <th style="text-align: right;">Municipal Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allUsersTotal = ($totalWorkers ?? 0) + ($totalResidential ?? 0);
                                @endphp
                                @foreach($barangayStats as $bgy)
                                    @php
                                        $bShare = $allUsersTotal > 0 ? round(($bgy->total / $allUsersTotal) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="fa-solid fa-location-dot" style="color: #f87171; margin-right: 6px;"></i>
                                            <strong>Brgy. {{ $bgy->barangay }}</strong>
                                        </td>
                                        <td style="text-align: right; font-weight: bold;">
                                            {{ $bgy->total }}
                                        </td>
                                        <td style="text-align: right;">
                                            <span style="background: rgba(16,185,129,0.2); color: #86efac; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">
                                                {{ $bShare }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="text-align: center; padding: 20px; opacity: 0.7;">No barangay demographic distribution records available yet.</p>
                    @endif
                </div>

            </div>

            <!-- Sign-off Section (Visible in Print) -->
            <div style="margin-top: 30px; display: none;" class="print-signoff">
                <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">PESO Staff Officer</p>
                        <p style="font-size: 10px; color: #555;">Prepared & Screened By</p>
                    </div>
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">Municipal PESO Manager</p>
                        <p style="font-size: 10px; color: #555;">Noted & Endorsed By</p>
                    </div>
                    <div style="text-align: center; width: 220px;">
                        <div style="border-bottom: 1px solid #000; height: 35px;"></div>
                        <p style="font-size: 12px; font-weight: bold; margin-top: 6px;">DOLE Field Officer</p>
                        <p style="font-size: 10px; color: #555;">Received for Compliance</p>
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
