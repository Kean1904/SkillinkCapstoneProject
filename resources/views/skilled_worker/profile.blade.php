<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Worker Profile</title>
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
        .page-inner { position: relative; z-index: 2; max-width: 900px; margin: 0 auto; }
        .page-title { color: white; font-size: 24px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .back-link { color: #93c5fd; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        .card { background: rgba(20, 60, 130, 0.55); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; padding: 24px; color: white; margin-bottom: 25px; }
        .card h3 { font-size: 18px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card hr { border: none; border-top: 1px solid rgba(255, 255, 255, 0.3); margin: 15px 0; }

        .btn { padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
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
                <p>Magalang, Pampanga &bull; Skilled Worker Portal</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.SkilledWorker') }}" class="back-link"><i class="fa-solid fa-house"></i> Main Dashboard</a>
        </div>
    </div>

    <!-- UNIFIED SIDEBAR -->
    @include('partials.sidebar_skilled_worker', ['active' => 'profile'])

    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner" style="max-width: 680px;">

            @php $worker = $worker ?? $user ?? auth()->user(); @endphp
            <div class="page-title">
                <span><i class="fa-solid fa-user"></i> MY PROFILE</span>
                <a href="{{ route('dashboard.SkilledWorker') }}" class="back-link">&larr; Back to Dashboard</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.25); border: 1px solid #4ade80; color: #bbf7d0; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div style="background: rgba(239, 68, 68, 0.25); border: 1px solid #f87171; color: #fecaca; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- PROFILE HEADER (AVATAR + CAMERA + BADGES) -->
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="position: relative; display: inline-block; cursor: pointer;" onclick="openEditProfileModal()" title="Click to Change Photo">
                    <img id="profileHeaderImg" src="{{ $worker->profile_image_uri ? asset($worker->profile_image_uri) : asset('image/MP_Profile.png') }}" alt="Profile" style="width: 115px; height: 115px; border-radius: 50%; object-fit: cover; background: white; padding: 4px; border: 3px solid rgba(255,255,255,0.7); box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                    <div style="position: absolute; bottom: 4px; right: 4px; width: 34px; height: 34px; border-radius: 50%; background: #0033a0; border: 2px solid white; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.35);">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                </div>

                <h2 style="color: white; font-size: 22px; font-weight: 900; margin-top: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ $worker->full_name }}
                </h2>

                <div style="margin-top: 6px;">
                    <span style="background: #1e3a8a; color: #93c5fd; padding: 4px 14px; border-radius: 12px; font-size: 12px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); display: inline-block;">
                        SKILLED WORKER
                    </span>
                </div>

                <div style="margin-top: 8px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    @if($worker->is_verified)
                        <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-certificate"></i> VERIFIED SKILLED WORKER
                        </span>
                    @else
                        <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-hourglass-half"></i> PENDING PESO ACCREDITATION
                        </span>
                    @endif
                    <span style="background: rgba(234, 179, 8, 0.2); color: #fde047; border: 1px solid #eab308; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">
                        ★ {{ number_format($worker->rating ?? 5.0, 1) }} Rating
                    </span>
                </div>
            </div>

            <!-- ACCOUNT INFORMATION CARD -->
            <div class="card" style="background: rgba(30, 58, 138, 0.35); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 22px;">
                <h3 style="font-size: 14px; font-weight: bold; color: white; letter-spacing: 0.5px; margin-bottom: 12px;">
                    ACCOUNT INFORMATION
                </h3>
                <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.2); margin-bottom: 16px;">

                <div style="display: flex; flex-direction: column; gap: 14px; font-size: 13.5px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-id-card" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">USERNAME</span><strong>@ {{ $worker->name }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-location-dot" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">BARANGAY</span><strong>Brgy. {{ $worker->barangay ?? 'Magalang' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-house" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">ADDRESS</span><strong>{{ $worker->address ?? 'Not Provided' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-phone" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">CONTACT</span><strong>{{ $worker->contact_number ?? 'Not Provided' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-envelope" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">EMAIL</span><strong>{{ $worker->email ?? 'Not Provided' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-cake-candles" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">AGE / GENDER</span><strong>{{ $worker->age ?? 35 }} yrs old &bull; {{ $worker->gender ?? 'Male' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-wrench" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">SKILLS</span><strong style="color: #60a5fa;">{{ $worker->skills ?? 'General Handyman' }}</strong></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-certificate" style="width: 20px; text-align: center; color: #93c5fd;"></i>
                        <div style="flex: 1;"><span style="opacity: 0.7; font-size: 11px; display: block;">CERTIFICATE / TESDA</span><strong style="color: #86efac;">{{ $worker->certificate_proof ?? 'TESDA NC II Certified' }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- EDIT DETAILS BUTTON -->
            <button type="button" onclick="openEditProfileModal()" style="width: 100%; padding: 14px; background: #0033a0; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.25); margin-top: 15px;">
                <i class="fa-solid fa-pen-to-square"></i> EDIT PROFILE DETAILS
            </button>

        </div>
    </div>

    <!-- EDIT PROFILE MODAL -->
    <div id="editProfileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); z-index: 3000; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #1e293b; border: 1px solid rgba(255,255,255,0.25); border-radius: 18px; padding: 25px; width: 100%; max-width: 520px; color: white; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 18px; font-weight: bold;"><i class="fa-solid fa-user-pen" style="color: #60a5fa;"></i> Edit Worker Profile</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 20px; opacity: 0.8;" onclick="closeEditProfileModal()"></i>
            </div>

            <form method="POST" action="{{ route('skilled_worker.profile.update') }}" enctype="multipart/form-data">
                @csrf

                <!-- AVATAR UPLOAD PREVIEW -->
                <div style="text-align: center; margin-bottom: 18px;">
                    <div style="position: relative; display: inline-block;">
                        <img id="avatarPreview" src="{{ $worker->profile_image_uri ? asset($worker->profile_image_uri) : asset('image/MP_Profile.png') }}" style="width: 85px; height: 85px; border-radius: 50%; object-fit: cover; background: white; padding: 3px; border: 2px solid #60a5fa;">
                        <label for="avatarInput" style="position: absolute; bottom: 0; right: 0; width: 28px; height: 28px; border-radius: 50%; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white; font-size: 12px;" title="Upload Photo">
                            <i class="fa-solid fa-camera"></i>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
                    </div>
                    <p style="font-size: 11px; opacity: 0.75; margin-top: 6px;">Tap camera to upload new avatar image</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">FIRST NAME</label>
                        <input type="text" name="first_name" value="{{ $worker->first_name }}" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">LAST NAME</label>
                        <input type="text" name="last_name" value="{{ $worker->last_name }}" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);" required>
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">CONTACT / PHONE NUMBER</label>
                    <input type="text" name="contact_number" value="{{ $worker->contact_number }}" placeholder="e.g. 09123456789" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">BARANGAY (MAGALANG)</label>
                    <select name="barangay" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);" required>
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
                            <option value="{{ $bgy }}" style="color: black;" {{ ($worker->barangay ?? '') == $bgy ? 'selected' : '' }}>{{ $bgy }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">STREET / ADDRESS</label>
                    <input type="text" name="address" value="{{ $worker->address }}" placeholder="e.g. Purok 3, Rizal St." style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">PRIMARY SKILLS / TRADE</label>
                    <input type="text" name="skills" value="{{ $worker->skills }}" placeholder="e.g. Plumbing Repair, Pipe Fitting" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">TESDA / CERTIFICATE PROOF</label>
                    <input type="text" name="certificate_proof" value="{{ $worker->certificate_proof }}" placeholder="e.g. TESDA NC II Plumbing" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px;">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">AGE</label>
                        <input type="number" name="age" value="{{ $worker->age ?? 35 }}" min="15" max="120" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 4px; opacity: 0.85;">GENDER</label>
                        <select name="gender" style="width: 100%; padding: 9px; border-radius: 6px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">
                            <option value="Male" style="color: black;" {{ ($worker->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" style="color: black;" {{ ($worker->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 6px;" onclick="closeEditProfileModal()">Cancel</button>
                    <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; font-weight: bold;"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }
        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
