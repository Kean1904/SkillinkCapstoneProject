<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('{{ asset('image/MP_Background.JPG') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
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
            padding: 10px 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }

        .header-left h1 {
            font-size: 22px;
        }

        .header-left p {
            font-size: 12px;
            font-weight: normal;
        }

        /* Back Botton */
        .back-btn {
            color: white;
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: background 0.2s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* LOGIN SECTION */
        .login-page {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-top: 65px;
        }

        .login-page::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: rgba(10, 25, 70, 0.65);
        }

        /* LOGIN CARD */
        .login-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 40px 45px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            color: white;
        }

        .login-card img.seal {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin-bottom: 15px;
        }

        .login-card h2 {
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .login-card p.subtitle {
            font-size: 13px;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            outline: none;
            -webkit-box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.1) inset !important;
            -webkit-text-fill-color: white !important;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        /* PASSWORD FIELD WITH EYE ICON */
        .password-wrapper {
            position: relative;
            margin-bottom: 8px;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            user-select: none;
        }

        .toggle-password:hover {
            color: white;
        }

        .forgot-link-wrapper {
            text-align: right;
            margin-bottom: 18px;
        }

        .forgot-link-wrapper a {
            color: #93c5fd;
            font-size: 12.5px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        .forgot-link-wrapper a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #0033a0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 5px;
            transition: background-color 0.2s ease;
        }

        .login-btn:hover {
            background-color: #002580;
        }

        .signup-text {
            margin-top: 15px;
            font-size: 13px;
        }

        .signup-text a {
            color: white;
            text-decoration: underline;
            cursor: pointer;
        }

        /* MODAL STYLES */
        .modal-wrap {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.65);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 440px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <a href="{{ route('home') }}" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga</p>
            </div>
        </div>
    </div>

    <!-- LOGIN SECTION -->
    <div class="login-page">
        @if (session('error'))
            <p style="color: #ffb3b3; font-size: 13px; margin-bottom: 15px;">
                {{ session('error') }}
            </p>
        @endif
        @if (session('success'))
            <!-- COMPACT REGISTRATION SUCCESS POP-UP BAR (Matching User Design) -->
            <div id="successPopupModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 99999; display: flex; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;">
                <div style="background: #ffffff; color: #1e293b; width: 100%; max-width: 380px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1); padding: 24px; box-sizing: border-box; position: relative; animation: popIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
                    
                    <!-- Top Row: Round Icon & Top-Right Close Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 46px; height: 46px; border-radius: 50%; background: #ecfdf5; border: 1px solid #a7f3d0; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 20px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <button type="button" onclick="document.getElementById('successPopupModal').remove()" style="width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e5e7eb; background: #ffffff; color: #9ca3af; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.color='#111827'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.color='#9ca3af'; this.style.borderColor='#e5e7eb';">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Title & Message -->
                    <h3 style="margin: 0 0 8px 0; font-size: 16.5px; font-weight: 700; color: #111827; text-align: left; line-height: 1.3;">
                        Matagumpay ang Pag-rehistro!
                    </h3>
                    <p style="margin: 0 0 22px 0; font-size: 13px; color: #6b7280; line-height: 1.5; text-align: left;">
                        {{ session('success') }}
                    </p>

                    <!-- Button: Confirm / Sige -->
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="button" onclick="document.getElementById('successPopupModal').remove()" style="width: 100%; padding: 10px 16px; border: none; background: #4f46e5; color: #ffffff; border-radius: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.35); transition: background 0.2s;" onmouseover="this.style.background='#4338ca';" onmouseout="this.style.background='#4f46e5';">
                            <i class="fa-solid fa-check"></i> Sige, Mag-log in
                        </button>
                    </div>

                </div>
            </div>

            <style>
                @keyframes popIn {
                    from {
                        opacity: 0;
                        transform: scale(0.92);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            </style>
        @endif

        <form class="login-card" method="POST" action="{{ route('Login.submit') }}">
            @csrf
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Seal" class="seal">
            <h2>LOGIN</h2>
            <p class="subtitle">Welcome back to Skillink</p>

            @if (isset($errors) && $errors->any())
                <p style="color: #ffbaba; font-size: 13px; margin-bottom: 15px;">
                    {{ $errors->first() }}
                </p>
            @endif

            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group password-wrapper">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
                </span>
            </div>

            <!-- FORGOT PASSWORD LINK -->
            <div class="forgot-link-wrapper">
                <a href="javascript:void(0)" onclick="openForgotModal()">Forgot Password?</a>
            </div>

            <button type="submit" class="login-btn">LOG IN</button>

            <p class="signup-text">Don't have an account? <a href="{{ route('Register') }}">Sign up here</a></p>
        </form>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotModal" class="modal-wrap">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 17px; font-weight: bold; color: white; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-key" style="color: #60a5fa;"></i> Reset Account Password
                </h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px; color: rgba(255,255,255,0.7);" onclick="closeForgotModal()"></i>
            </div>
            
            <p style="font-size: 13px; opacity: 0.85; margin-bottom: 18px; line-height: 1.5; text-align: left;">
                Enter your registered Email Address or Username. We will dispatch a secure password reset link to verify your identity.
            </p>

            <div id="forgotSuccessBox" style="display: none; background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #ecfdf5; padding: 18px; border-radius: 12px; margin-bottom: 18px; text-align: center;">
                <div style="font-size: 15px; font-weight: bold; margin-bottom: 6px; color: #34d399; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check"></i> Account Verified!
                </div>
                <p style="font-size: 13px; opacity: 0.9; margin-bottom: 12px;" id="forgotSuccessMsg"></p>
                <div style="background: rgba(0,0,0,0.3); border: 1px dashed #34d399; border-radius: 8px; padding: 10px; margin-bottom: 15px;">
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #a7f3d0; display: block; margin-bottom: 4px;">Verification OTP Code</span>
                    <span id="forgotOtpDisplay" style="font-size: 24px; font-weight: 800; letter-spacing: 4px; color: #ffffff; font-family: monospace;"></span>
                </div>
                <a id="forgotResetBtn" href="#" style="display: block; width: 100%; padding: 11px; background: #2563eb; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13.5px; transition: background 0.2s;">
                    <i class="fa-solid fa-arrow-right"></i> Proceed to Reset Password
                </a>
            </div>

            <form id="forgotForm" onsubmit="handleForgotSubmit(event)">
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 6px; letter-spacing: 0.5px; opacity: 0.9;">REGISTERED EMAIL OR USERNAME</label>
                    <input type="text" id="forgotInput" name="email_or_username" placeholder="e.g. resident@email.com or username" style="width: 100%; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white; font-size: 14px; outline: none;" required>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" style="padding: 10px 18px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; cursor: pointer; font-size: 13.5px;" onclick="closeForgotModal()">Cancel</button>
                    <button type="submit" id="forgotSubmitBtn" style="padding: 10px 20px; border-radius: 6px; border: none; background: #0033a0; color: white; cursor: pointer; font-weight: bold; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-paper-plane"></i> Send Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        function openForgotModal() {
            document.getElementById('forgotSuccessBox').style.display = 'none';
            document.getElementById('forgotForm').style.display = 'block';
            document.getElementById('forgotInput').value = '';
            document.getElementById('forgotModal').style.display = 'flex';
        }

        function closeForgotModal() {
            document.getElementById('forgotModal').style.display = 'none';
        }

        function handleForgotSubmit(e) {
            e.preventDefault();
            const val = document.getElementById('forgotInput').value.trim();
            if (!val) return;

            const btn = document.getElementById('forgotSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

            function sendRequest(url) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email_or_username: val })
                });
            }

            sendRequest('{{ route('password.forgot') }}')
            .then(res => {
                if (res.status === 419) {
                    // Seamless automatic fallback to stateless API if web CSRF session expired
                    return sendRequest('{{ url('/api/password/forgot') }}');
                }
                return res;
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Reset Link';
                if (data.success) {
                    document.getElementById('forgotSuccessMsg').textContent = 'Account: ' + (data.targetEmail || val);
                    if (data.otp) {
                        document.getElementById('forgotOtpDisplay').textContent = data.otp;
                    }
                    if (data.resetUrl) {
                        document.getElementById('forgotResetBtn').href = data.resetUrl;
                    }
                    document.getElementById('forgotSuccessBox').style.display = 'block';
                    document.getElementById('forgotForm').style.display = 'none';
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Reset Link';
                alert('Hindi maipadala ang password reset request sa ngayon. Pakisubukang muli.');
            });
        }
    </script>

</body>
</html>