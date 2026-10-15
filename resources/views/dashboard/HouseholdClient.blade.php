<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - HouseHold Client Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-light.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #0033a0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 25px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-icon {
            font-size: 20px;
            cursor: pointer;
        }

        .header-left img.logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
        }

        .header-left h1 {
            font-size: 18px;
        }

        .header-left p {
            font-size: 11px;
            font-weight: normal;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 6px 15px;
            gap: 10px;
        }

        .search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            font-size: 13px;
            width: 150px;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box i {
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        /* PAGE WRAPPER */
        .page-content {
            padding: 80px 25px 40px 25px;
            position: relative;
            min-height: 100vh;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(20, 55, 130, 0.55);
            z-index: 0;
            pointer-events: none;
        }

        .page-inner {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: white;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        /* MAIN GRID LAYOUT */
        .dashboard-grid {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .main-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .side-column {
            flex: 1;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* STAT CARDS */
        .stats-row {
            display: flex;
            gap: 20px;
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 15px;
        }

        .stat-card {
            flex: 1;
            max-width: 500px;
            background: rgba(20, 60, 130, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 15px 20px;
            color: white;
        }

        .stat-card .label {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-card .value-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        /* CREATE A JOBS BUTTON */
        .create-offer-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(20, 60, 130, 0.7);
            border: 2px solid #7c4dff;
            border-radius: 10px;
            padding: 18px 20px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .create-offer-btn:hover {
            background: rgba(30, 75, 150, 0.8);
        }

        .plus-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* PANELS (Available Skilled Worker, Notification, Service Request) */
        .panel {
            background: rgba(20, 60, 130, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px 20px 30px 20px;
            color: white;
            min-height: 180px;
            max-height: none;
            overflow: visible;
        }

        .panel h3 {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .panel hr {
            border: none;
            border-top: 2px solid rgba(255, 255, 255, 0.5);
            width: 60px;
            margin-bottom: 20px;
        }

        .no-data {
            text-align: center;
            color: rgba(255, 255, 255, 0.75);
            font-size: 18px;
            font-weight: 500;
            padding: 30px 0;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .dashboard-grid {
                flex-direction: column;
            }

            .side-column {
                max-width: 100%;
            }

            .stats-row {
                flex-direction: column;
            }

            .stat-card {
                max-width: 100%;
            }

            .search-box input {
                width: 100px;
            }
        }

        
        /* MOBILE-PARITY STANDARDIZED SIDEBAR DRAWER (EXACT MATCH TO MOBILE APP) */
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

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon" onclick="toggleSidebar()"></i>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo" class="logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga</p>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="headerSearchInput" value="{{ $search ?? '' }}" placeholder="Search jobs (e.g. Plumbing)..." onkeyup="handleHeaderSearch(event)">
            <i class="fa-solid fa-magnifying-glass" onclick="triggerSearch()"></i>
        </div>
    </div>

    <!-- UNIFIED HOUSEHOLD CLIENT SIDEBAR (PARTIAL) -->
    @include('partials.sidebar_household_client', ['active' => 'dashboard']) 

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 class="page-title" style="margin-bottom: 0;">HouseHold Client Dashboard</h2>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; padding: 5px 14px; border-radius: 20px; font-size: 11.5px; color: #6ee7b7; font-weight: 600;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                    <span>Live Cloud Sync</span>
                </div>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background: rgba(239, 68, 68, 0.25); border: 1px solid #f87171; color: #fecaca; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div style="background: rgba(239, 68, 68, 0.25); border: 1px solid #f87171; color: #fecaca; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> May problema sa pag-submit:</strong>
                    <ul style="margin: 5px 0 0 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="dashboard-grid">

                <!-- MAIN COLUMN -->
                <div class="main-column">

                    @if(isset($announcements) && count($announcements) > 0)
                        <div class="panel" style="background: rgba(15, 23, 42, 0.75); border: 2px solid #3b82f6; margin-bottom: 20px; min-height: auto; padding: 18px 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                                <h3 style="color: white; font-size: 15px; display: flex; align-items: center; gap: 8px; margin: 0;">
                                    <i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i>
                                    OPISYAL NA ABISO NG PESO MAGALANG
                                </h3>
                                <span style="font-size: 11px; color: #93c5fd; background: rgba(59, 130, 246, 0.25); padding: 3px 10px; border-radius: 6px; font-weight: bold; border: 1px solid #3b82f6;">
                                    <i class="fa-solid fa-circle-info"></i> Municipal Feed
                                </span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($announcements as $ann)
                                    <div style="background: rgba(255, 255, 255, 0.06); border-left: 4px solid #3b82f6; border-radius: 6px; padding: 12px 14px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 6px;">
                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                <strong style="color: white; font-size: 14px;">{{ $ann->title }}</strong>
                                                <span style="background: rgba(59, 130, 246, 0.3); color: #bfdbfe; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                                    {{ $ann->category }}
                                                </span>
                                            </div>
                                            <span style="font-size: 11px; color: #94a3b8;">
                                                <i class="fa-regular fa-clock"></i> {{ $ann->created_at ? $ann->created_at->format('M d, Y h:i A') : 'Recent' }}
                                            </span>
                                        </div>
                                        <p style="font-size: 12.5px; color: #f1f5f9; margin-top: 6px; line-height: 1.45; white-space: pre-line;">{{ $ann->message }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- STAT CARDS -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <p class="label">AVAILABLE SKILLED WORKER</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-user-group"></i></div>
                                <span class="stat-number" id="statAvailableWorkers">{{ $availableWorkers ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('household_client.job_posts') }}'" title="View Your Posted Jobs">
                            <p class="label">POST JOB</p>
                            <div class="value-row">
                                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                                <span class="stat-number" id="statPostedJobs">{{ $postedJobs ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CREATE A JOBS (INTERACTIVE) -->
                    <div class="create-offer-btn" onclick="window.location.href='{{ route('household_client.job_posts') }}?action=create'" style="cursor: pointer;" title="Click to Post a Job Need in Magalang">
                        <div class="plus-icon"><i class="fa-solid fa-plus"></i></div>
                        <span>CREATE A JOBS</span>
                    </div>

                    <!-- SERVICE OFFERS OF SKILLED WORKERS (MAIN SECTION) -->
                    <div class="panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <h3 style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 16px;">
                                    <i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i>
                                    SERVICE OFFERS OF SKILLED WORKERS
                                </h3>
                                <p style="font-size: 11.5px; opacity: 0.8; margin-top: 3px;">
                                    Tingnan at i-book ang mga ipinaskil na alok na serbisyo at presyo ng mga lehitimong manggagawa sa Magalang.
                                </p>
                            </div>
                            <span style="font-size: 11px; color: #86efac; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; padding: 3px 10px; border-radius: 6px; font-weight: bold;">
                                <i class="fa-solid fa-handshake"></i> Direct Worker Rates
                            </span>
                        </div>

                        <!-- Dedicated In-Panel Search & Quick Filters -->
                        <div style="margin-top: 14px; margin-bottom: 12px;">
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <div style="position: relative; flex: 1;">
                                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 11px; color: #93c5fd; font-size: 13px;"></i>
                                    <input type="text" id="panelSearchInput" value="{{ $search ?? '' }}" 
                                           placeholder="Type service needed (e.g., IT Technician, Plumbing, Electrical, Carpentry)..." 
                                           onkeyup="syncAndFilterOffersAndWorkers(this.value)"
                                           style="width: 100%; padding: 8px 12px 8px 34px; border-radius: 8px; border: 1.5px solid rgba(147, 197, 253, 0.4); background: rgba(0, 0, 0, 0.25); color: white; font-size: 13px; outline: none;">
                                </div>
                                <button type="button" onclick="clearSearch()" title="Clear Search" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 8px; padding: 8px 14px; font-size: 12px; cursor: pointer; white-space: nowrap;">
                                    Reset
                                </button>
                            </div>

                            <!-- Quick Filter Pills -->
                            <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 11px; color: rgba(255,255,255,0.7); margin-right: 4px;">Popular Trades:</span>
                                @foreach(['All', 'Plumbing', 'Electrical', 'Carpentry', 'Welding', 'Masonry', 'IT Technician', 'Appliance', 'Other Home Services'] as $pill)
                                    <button type="button" class="trade-pill" onclick="applyQuickFilter('{{ $pill === 'All' ? '' : $pill }}')" 
                                            style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.25); color: #e2e8f0; font-size: 11px; padding: 3px 10px; border-radius: 14px; cursor: pointer; transition: all 0.2s;">
                                        {{ $pill }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div id="searchResultsInfo" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 12px; color: #93c5fd;">
                            <span id="resultCountText">Showing {{ count($serviceOffers ?? []) }} published service offers</span>
                        </div>

                        <hr style="margin-top: 4px; margin-bottom: 12px;">

                        <div id="noOfferFoundNotice" style="display: none; background: rgba(0,0,0,0.3); border: 1px dashed rgba(255,255,255,0.3); border-radius: 8px; padding: 20px; text-align: center; color: #cbd5e1; margin-bottom: 12px;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 24px; color: #93c5fd; margin-bottom: 8px;"></i>
                            <h4 style="font-size: 14px; color: white;">Walang nahanap na service offer na tumutugma sa iyong paghahanap.</h4>
                            <p style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Subukan ang ibang salita tulad ng Plumbing, Electrical, IT, o Carpentry.</p>
                        </div>

                        <div id="serviceOffersContainer">
                        @if(isset($serviceOffers) && count($serviceOffers) > 0)
                            @foreach($serviceOffers as $offer)
                                @php
                                    $wName = $offer->worker ? $offer->worker->name : $offer->posted_by;
                                    $wFullName = $offer->worker ? $offer->worker->full_name : $offer->posted_by;
                                    $wSkills = $offer->worker ? $offer->worker->skills : $offer->category;
                                    $wContact = $offer->worker ? ($offer->worker->contact_number ?? $offer->worker->phone_number ?? '') : '';
                                    $wRating = number_format($offer->worker->rating ?? 5.0, 1);
                                    $wRate = $offer->parsed_rate ?? ($offer->worker ? $offer->worker->service_rate_display : '₱500.00');
                                    $wVerified = $offer->worker && $offer->worker->is_verified;
                                    $wBarangay = $offer->barangay ?? ($offer->worker ? $offer->worker->barangay : 'Magalang');
                                    $isSaved = isset($savedWorkerIds) && $offer->worker && in_array($offer->worker->user_id, $savedWorkerIds);
                                @endphp
                                <div class="service-offer-card-item" 
                                     data-title="{{ strtolower($offer->title) }}" 
                                     data-category="{{ strtolower($offer->category) }}" 
                                     data-worker="{{ strtolower($wFullName . ' ' . $wName) }}" 
                                     data-desc="{{ strtolower($offer->description) }}" 
                                     data-barangay="{{ strtolower($wBarangay) }}"
                                     style="background: rgba(255,255,255,0.08); border-radius: 10px; padding: 14px 16px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.15); border-left: 4px solid #3b82f6; transition: all 0.2s;">
                                    
                                    <!-- Offer Header: Title, Category Badge, Price -->
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 200px;">
                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                <h4 style="font-size: 15px; color: white; margin: 0; font-weight: bold;">{{ $offer->title }}</h4>
                                                <span style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid #3b82f6; font-size: 11px; font-weight: bold; padding: 2px 8px; border-radius: 4px;">
                                                    <i class="fa-solid fa-tag"></i> {{ $offer->category }}
                                                </span>
                                            </div>
                                            <p style="font-size: 12.5px; color: #e2e8f0; margin: 6px 0 8px 0; line-height: 1.45;">
                                                {{ $offer->description }}
                                            </p>
                                        </div>

                                        <div style="text-align: right; flex-shrink: 0;">
                                            <span style="font-size: 13px; color: #86efac; font-weight: 900; background: rgba(16, 185, 129, 0.22); border: 1.5px solid #10b981; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-money-bill-wave"></i> {{ $wRate }} Fixed
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Worker Meta Info & Actions Row -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed rgba(255,255,255,0.15);">
                                        <div style="font-size: 11.5px; opacity: 0.85; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <span style="color: white; font-weight: bold;">
                                                <i class="fa-solid fa-user-check" style="color: #60a5fa;"></i> {{ $wFullName }}
                                            </span>
                                            <span style="background: #10b981; color: white; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px;">
                                                ★ {{ $wRating }}
                                            </span>
                                            <span>
                                                <i class="fa-solid fa-location-dot" style="color: #f87171;"></i> Brgy. {{ $wBarangay }}
                                            </span>
                                            <span style="color: {{ $wVerified ? '#86efac' : '#fde047' }};">
                                                <i class="fa-solid {{ $wVerified ? 'fa-certificate' : 'fa-clock' }}"></i> {{ $wVerified ? 'Verified PESO' : 'Accredited' }}
                                            </span>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                            <button type="button" onclick="openChatModal('{{ $wName }}', '{{ addslashes($wFullName) }}', '{{ addslashes($wSkills ?? $offer->category) }}', '{{ $wContact }}')" 
                                                    style="height: 34px; padding: 0 12px; background: #0284c7; border: 1px solid #38bdf8; color: white; border-radius: 6px; cursor: pointer; font-size: 11.5px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; gap: 5px; box-sizing: border-box;" title="Message worker to discuss details">
                                                <i class="fa-solid fa-comments"></i> Message
                                            </button>

                                            <button type="button" onclick="openBookModal('{{ $wName }}', '{{ addslashes($wFullName) }}', '{{ addslashes($wSkills ?? $offer->category) }}', '{{ $wRate }}')" 
                                                    style="height: 34px; padding: 0 14px; background: #0033a0; border: 1px solid #60a5fa; color: white; border-radius: 6px; cursor: pointer; font-size: 11.5px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; gap: 5px; box-sizing: border-box;" title="Book this service offer">
                                                <i class="fa-solid fa-calendar-check"></i> Book Now
                                            </button>

                                            @if($offer->worker)
                                                <form method="POST" action="{{ route('household_client.worker.toggle_save', $offer->worker->user_id) }}" style="display: inline-flex; margin: 0; padding: 0; align-items: center;">
                                                    @csrf
                                                    <button type="submit" title="{{ $isSaved ? 'Remove from Saved Bookmarks' : 'Save / Bookmark Worker' }}" 
                                                            style="height: 34px; width: 34px; padding: 0; background: {{ $isSaved ? 'rgba(234, 179, 8, 0.25)' : 'rgba(255,255,255,0.15)' }}; border: 1px solid {{ $isSaved ? '#eab308' : 'rgba(255,255,255,0.4)' }}; color: {{ $isSaved ? '#facc15' : 'white' }}; border-radius: 6px; cursor: pointer; font-size: 12.5px; display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box;">
                                                        <i class="{{ $isSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark' }}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-data">Walang aktibong service offer sa kasalukuyan.</div>
                        @endif
                        </div>
                    </div>

                </div>

                <!-- SIDE COLUMN -->
                <div class="side-column">
                    <div class="panel">
                        <h3>Notification</h3>
                        <hr>
                        <div class="no-data">No Data</div>
                    </div>

                    <div class="panel">
                        <h3>Service Request</h3>
                        <hr>
                        <div class="no-data">No Data</div>
                    </div>

                    <!-- AVAILABLE SKILLED WORKERS (SIDE PANEL CONTAINER) -->
                    <div class="panel" style="margin-top: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                            <h3 style="font-size: 13.5px; margin: 0; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-user-group" style="color: #60a5fa;"></i>
                                AVAILABLE SKILLED WORKER
                            </h3>
                            <a href="{{ route('household_client.saved_workers') }}" style="color: #93c5fd; font-size: 11px; text-decoration: none;" title="View Bookmarked Workers">
                                <i class="fa-solid fa-bookmark"></i> Saved ({{ count($savedWorkerIds ?? []) }})
                            </a>
                        </div>
                        <p style="font-size: 11px; opacity: 0.75; margin: 3px 0 8px 0;">Mga rehistradong manggagawa sa Magalang.</p>
                        <hr style="margin-top: 2px; margin-bottom: 10px;">

                        <div id="sideWorkersContainer" style="max-height: 480px; overflow-y: auto; padding-right: 2px;">
                        @if(isset($workersList) && count($workersList) > 0)
                            @foreach($workersList as $worker)
                                @php
                                    $isSaved = isset($savedWorkerIds) && in_array($worker->user_id, $savedWorkerIds);
                                @endphp
                                <div class="worker-side-card" 
                                     data-name="{{ strtolower($worker->full_name . ' ' . $worker->name) }}" 
                                     data-skills="{{ strtolower($worker->skills ?? '') }}" 
                                     data-barangay="{{ strtolower($worker->barangay ?? '') }}"
                                     style="background: rgba(255,255,255,0.08); border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.15); border-left: 3px solid #10b981;">
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 6px;">
                                        <div>
                                            <strong style="font-size: 13px; color: white; display: block;">{{ $worker->full_name }}</strong>
                                            <span style="font-size: 10.5px; color: #86efac; font-weight: bold;">
                                                <i class="fa-solid fa-money-bill-wave"></i> {{ $worker->service_rate_display }} Fixed
                                            </span>
                                        </div>
                                        <span style="background: #10b981; color: white; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 8px;">
                                            ★ {{ number_format($worker->rating ?? 5.0, 1) }}
                                        </span>
                                    </div>

                                    <div style="font-size: 11px; color: #fde047; font-weight: bold; margin: 3px 0;">
                                        <i class="fa-solid fa-wrench"></i> {{ $worker->skills ?? 'General Handyman' }}
                                    </div>
                                    <div style="font-size: 10.5px; opacity: 0.75; margin-bottom: 6px;">
                                        <i class="fa-solid fa-location-dot"></i> Brgy. {{ $worker->barangay }} &bull; <span style="color: {{ $worker->is_verified ? '#86efac' : '#fde047' }};">{{ $worker->is_verified ? 'Verified PESO' : 'Pending' }}</span>
                                    </div>

                                    <!-- Bottom Button Row (Katulad sa Mobile Application: Sama-samang Message, Book Now, at Bookmark) -->
                                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.1);">
                                        <button type="button" onclick="openChatModal('{{ $worker->name }}', '{{ addslashes($worker->full_name) }}', '{{ addslashes($worker->skills ?? 'General Handyman') }}', '{{ $worker->contact_number ?? $worker->phone_number ?? '' }}')" 
                                                style="flex: 1; height: 32px; background: #0284c7; border: 1px solid #38bdf8; color: white; border-radius: 5px; cursor: pointer; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" title="Send inquiry or clarify details before booking">
                                            <i class="fa-solid fa-comments"></i> Message
                                        </button>
                                        <button type="button" onclick="openBookModal('{{ $worker->name }}', '{{ $worker->full_name }}', '{{ $worker->skills }}', '{{ $worker->service_rate_display }}')" 
                                                style="flex: 1.2; height: 32px; background: #0033a0; border: 1px solid #60a5fa; color: white; border-radius: 5px; cursor: pointer; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" title="Book Worker">
                                            <i class="fa-solid fa-calendar-check"></i> Book Now
                                        </button>
                                        <form method="POST" action="{{ route('household_client.worker.toggle_save', $worker->user_id) }}" style="display: inline-flex; margin: 0; padding: 0;">
                                            @csrf
                                            <button type="submit" title="{{ $isSaved ? 'Remove from Saved Bookmarks' : 'Save / Bookmark Worker' }}" 
                                                    style="height: 32px; width: 32px; background: {{ $isSaved ? 'rgba(234, 179, 8, 0.25)' : 'rgba(255,255,255,0.15)' }}; border: 1px solid {{ $isSaved ? '#eab308' : 'rgba(255,255,255,0.3)' }}; color: {{ $isSaved ? '#facc15' : 'white' }}; border-radius: 5px; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="{{ $isSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-data">No Data</div>
                        @endif
                        </div>
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

    // Live Cloudbase Stats Synchronizer for Household Client Dashboard (5-second interval)
    function pollHouseholdClientLiveStats() {
        fetch("{{ route('dashboard.live_stats') }}?username={{ urlencode(session('user_name') ?? '') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) return;

            const elWorkers = document.getElementById('statAvailableWorkers');
            const elPosted = document.getElementById('statPostedJobs');

            if (elWorkers && data.availableWorkers !== undefined) {
                if (parseInt(elWorkers.textContent.trim(), 10) !== data.availableWorkers) {
                    elWorkers.textContent = data.availableWorkers;
                    if (elWorkers.parentElement && elWorkers.parentElement.parentElement) {
                        const card = elWorkers.parentElement.parentElement;
                        card.style.transition = 'all 0.3s ease';
                        card.style.boxShadow = '0 0 20px #10b981';
                        setTimeout(() => { card.style.boxShadow = ''; }, 1200);
                    }
                }
            }

            if (elPosted && data.myPostedJobs !== undefined) {
                if (parseInt(elPosted.textContent.trim(), 10) !== data.myPostedJobs) {
                    elPosted.textContent = data.myPostedJobs;
                    if (elPosted.parentElement && elPosted.parentElement.parentElement) {
                        const card = elPosted.parentElement.parentElement;
                        card.style.transition = 'all 0.3s ease';
                        card.style.boxShadow = '0 0 20px #3b82f6';
                        setTimeout(() => { card.style.boxShadow = ''; }, 1200);
                    }
                }
            }
        })
        .catch(err => {
            console.debug('HouseHold Client live sync error:', err);
        });
    }

    function syncAndFilterOffersAndWorkers(query) {
        const headerInput = document.getElementById('headerSearchInput');
        const panelInput = document.getElementById('panelSearchInput');
        
        if (headerInput && headerInput.value !== query) {
            headerInput.value = query;
        }
        if (panelInput && panelInput.value !== query) {
            panelInput.value = query;
        }

        const q = (query || '').trim().toLowerCase();
        
        // Filter Service Offers in Main Panel
        const offerItems = document.querySelectorAll('.service-offer-card-item');
        const countText = document.getElementById('resultCountText');
        const noNotice = document.getElementById('noOfferFoundNotice');

        let visibleOffers = 0;
        offerItems.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const category = card.getAttribute('data-category') || '';
            const worker = card.getAttribute('data-worker') || '';
            const desc = card.getAttribute('data-desc') || '';
            const barangay = card.getAttribute('data-barangay') || '';

            if (!q || title.includes(q) || category.includes(q) || worker.includes(q) || desc.includes(q) || barangay.includes(q)) {
                card.style.display = 'block';
                visibleOffers++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countText) {
            if (q) {
                countText.innerHTML = `Found <strong style="color: #fde047;">${visibleOffers}</strong> published service offers matching "<strong>${escapeHtml(q)}</strong>"`;
            } else {
                countText.innerText = `Showing ${visibleOffers} published service offers`;
            }
        }

        if (noNotice) {
            noNotice.style.display = (visibleOffers === 0 && offerItems.length > 0) ? 'block' : 'none';
        }

        // Also filter workers in the Side Panel
        const sideWorkerItems = document.querySelectorAll('.worker-side-card');
        sideWorkerItems.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const skills = card.getAttribute('data-skills') || '';
            const barangay = card.getAttribute('data-barangay') || '';

            if (!q || name.includes(q) || skills.includes(q) || barangay.includes(q)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function handleHeaderSearch(e) {
        syncAndFilterOffersAndWorkers(e.target.value);
        if (e.key === 'Enter') {
            document.getElementById('panelSearchInput')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function triggerSearch() {
        const val = document.getElementById('headerSearchInput')?.value || '';
        syncAndFilterOffersAndWorkers(val);
        document.getElementById('panelSearchInput')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function applyQuickFilter(trade) {
        syncAndFilterOffersAndWorkers(trade);
    }

    function clearSearch() {
        syncAndFilterOffersAndWorkers('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialSearch = "{{ $search ?? '' }}";
        if (initialSearch) {
            syncAndFilterOffersAndWorkers(initialSearch);
        }
    });

    setInterval(pollHouseholdClientLiveStats, 5000);
    </script>

    @include('partials.privacy_consent_modal')
    @include('partials.booking_modal_with_calendar')
    @include('partials.chat_popup')

</body>
</html>