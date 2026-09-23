<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - My Services</title>
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
        .page-inner { position: relative; z-index: 2; max-width: 1000px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: #0033a0; color: white; border: 1px solid #60a5fa; }
        .btn-primary:hover { background: #1d4ed8; }

        .tag { display: inline-block; background: rgba(37,99,235,0.4); border: 1px solid #60a5fa; padding: 6px 14px; border-radius: 20px; font-size: 13px; margin: 4px; }

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
                <p>Magalang, Pampanga &bull; Skilled Worker Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.SkilledWorker') }}" class="back-link"><i class="fa-solid fa-house"></i> Main Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_skilled_worker', ['active' => 'services'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="page-title">
                <span><i class="fa-solid fa-wrench"></i> MY SERVICES & SPECIALIZATIONS</span>
                <button type="button" class="btn btn-primary" onclick="openJobOfferModal()" style="font-size: 13.5px; padding: 10px 20px; border-radius: 8px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: 1px solid #60a5fa; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.4); display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus-circle"></i> Create Job Offer
                </button>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- CURRENT PROFILE & SKILLS -->
            <div class="card">
                <h3><i class="fa-solid fa-id-card"></i> Current Service Offerings</h3>
                <p style="font-size: 13px; opacity: 0.85;">Ito ang mga serbisyong nakikita ng mga residente sa Magalang kapag hinahanap ka nila.</p>
                <hr>

                <div style="margin-bottom: 20px;">
                    <strong style="font-size: 14px; color: #93c5fd;">Active Trade Skills:</strong>
                    <div style="margin-top: 10px;">
                        @php
                            $skills = explode(',', $worker->skills ?? 'Plumbing Repair, Pipe Fitting, Water Line Sealant');
                        @endphp
                        @foreach($skills as $skill)
                            <span class="tag"><i class="fa-solid fa-check"></i> {{ trim($skill) }}</span>
                        @endforeach
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; background: rgba(0,0,0,0.2); padding: 16px; border-radius: 8px;">
                    <div><strong>Availability:</strong> <span style="color: #4ade80; font-weight: bold;">Available for Service</span></div>
                    <div><strong>Accreditation:</strong> <span style="color: #60a5fa;">{{ $worker->is_verified ? 'Verified PESO Partner' : 'Under Review' }}</span></div>
                    <div><strong>Barangay:</strong> {{ $worker->barangay ?? 'San Nicolas 1st' }}</div>
                    <div><strong>Rating:</strong> ★ {{ number_format($worker->rating ?? 5.0, 1) }} / 5.0</div>
                </div>
            </div>

            <!-- UPDATE SERVICES FORM -->
            <div class="card">
                <h3><i class="fa-solid fa-pen-to-square"></i> Update Service Offerings</h3>
                <p style="font-size: 13px; opacity: 0.85;">Maaari mong baguhin o dagdagan ang iyong mga serbisyo at panimulang rates.</p>
                <hr>

                <form method="POST" action="{{ route('skilled_worker.services.update') }}">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 6px;">Skills & Trade Services (Comma-separated)</label>
                        <input type="text" name="skills" value="{{ $worker->skills ?? 'Plumbing Repair, Pipe Fitting, Water Line Sealant' }}" style="width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white;" required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 6px;">TESDA Certificate Proof / License</label>
                        <input type="text" name="certificate_proof" value="{{ $worker->certificate_proof ?? 'TESDA NC II - Plumbing' }}" style="width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white;">
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Service Changes</button>
                </form>
            </div>

            <!-- MY PUBLISHED SERVICE JOB OFFERS (LIVE SYNC PROOF) -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h3><i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i> My Published Service & Job Offers</h3>
                    <button type="button" class="btn btn-primary" onclick="openJobOfferModal()" style="font-size: 12px; padding: 6px 14px;">
                        <i class="fa-solid fa-plus"></i> Post Another Offer
                    </button>
                </div>
                <p style="font-size: 13px; opacity: 0.85; margin-top: 4px;">Mga aktibong alok na serbisyo na nakikita ng mga residente sa Magalang at naka-sync sa Android app at PESO.</p>
                <hr>

                @if(isset($myJobOffers) && count($myJobOffers) > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($myJobOffers as $offer)
                            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <h4 style="font-size: 15px; margin: 0; color: white;">{{ $offer->title }}</h4>
                                        <span style="font-size: 10.5px; padding: 2px 8px; border-radius: 12px; background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; font-weight: bold;">
                                            {{ $offer->status ?? 'Active' }}
                                        </span>
                                    </div>
                                    <p style="font-size: 12.5px; opacity: 0.85; margin: 4px 0 6px 0;">{{ $offer->description }}</p>
                                    <div style="font-size: 11px; opacity: 0.75; display: flex; gap: 14px;">
                                        <span><i class="fa-solid fa-tag"></i> {{ $offer->category }}</span>
                                        <span><i class="fa-solid fa-location-dot"></i> Brgy. {{ $offer->barangay }}</span>
                                        <span><i class="fa-regular fa-calendar"></i> Posted: {{ $offer->date_posted }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 11px; background: rgba(59, 130, 246, 0.25); border: 1px solid #60a5fa; color: #bfdbfe; padding: 4px 10px; border-radius: 6px;">
                                        <i class="fa-solid fa-globe"></i> Live Synced
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 25px; opacity: 0.75;">
                        <i class="fa-solid fa-clipboard-list" style="font-size: 28px; margin-bottom: 8px;"></i>
                        <p style="font-size: 13px;">Wala ka pang nai-post na Job Offer. Pindutin ang <strong>"Create Job Offer"</strong> sa itaas para mag-post!</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- CREATE JOB OFFER MODAL -->
    <div id="jobOfferModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: #1e3a8a; border: 1px solid rgba(255,255,255,0.3); border-radius: 14px; padding: 25px; width: 92%; max-width: 520px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="margin: 0; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus-circle" style="color: #60a5fa;"></i> Create a Service Job Offer
                </h3>
                <span onclick="closeJobOfferModal()" style="cursor: pointer; font-size: 20px; color: rgba(255,255,255,0.7);">&times;</span>
            </div>
            <p style="font-size: 12.5px; opacity: 0.85; margin-bottom: 16px;">I-post ang iyong alok na serbisyo upang makita ito ng mga residente sa Magalang at sa lahat ng portal.</p>

            <form method="POST" action="{{ route('skilled_worker.job_offer.create') }}">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Service Title / Alok na Trabaho</label>
                    <input type="text" name="title" placeholder="hal. Residential Plumbing & Pipe Leak Repair" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Category / Larangan</label>
                        <select name="category" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                            <option value="Plumbing Repair" style="color: black;">Plumbing Repair</option>
                            <option value="Electrical Installation" style="color: black;">Electrical Installation</option>
                            <option value="Carpentry & Roofing" style="color: black;">Carpentry & Roofing</option>
                            <option value="Welding & Fabrication" style="color: black;">Welding & Fabrication</option>
                            <option value="Appliance Repair" style="color: black;">Appliance Repair</option>
                            <option value="Masonry & Construction" style="color: black;">Masonry & Construction</option>
                            <option value="Automotive & Mechanical" style="color: black;">Automotive & Mechanical</option>
                            <option value="Other Home Services" style="color: black;">Other Home Services</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Barangay in Magalang</label>
                        <select name="barangay" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" required>
                            @php
                                $barangays = [
                                    "Ayala", "Bucanan", "Camias", "Dolores", "Escaler", "La Paz", "Navaling",
                                    "San Agustin", "San Antonio", "San Francisco", "San Ildefonso", "San Isidro",
                                    "San Jose", "San Miguel", "San Nicolas 1st", "San Nicolas 2nd", "San Pablo",
                                    "San Pedro 1st", "San Pedro 2nd", "San Roque", "San Vicente", "Santa Cruz",
                                    "Santa Lucia", "Santa Maria", "Santo Niño", "Santo Rosario", "Turu"
                                ];
                            @endphp
                            @foreach($barangays as $bgy)
                                <option value="{{ $bgy }}" {{ ($worker->barangay ?? '') == $bgy ? 'selected' : '' }} style="color: black;">{{ $bgy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Estimated Service Rate / Presyo (Opsyonal)</label>
                    <input type="text" name="estimated_rate" placeholder="hal. ₱500 - ₱1,200 o ₱500 / araw" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px;">Service Details & Description</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.4);" placeholder="Ilarawan ang sakop ng serbisyo, gamit na dala, garantiya, atbp..." required></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="closeJobOfferModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background: #2563eb;"><i class="fa-solid fa-paper-plane"></i> Publish Offer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        function openJobOfferModal() {
            document.getElementById('jobOfferModal').style.display = 'flex';
        }

        function closeJobOfferModal() {
            document.getElementById('jobOfferModal').style.display = 'none';
        }
    </script>
</body>
</html>
