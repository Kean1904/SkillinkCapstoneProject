<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKILLINK - Reset Password</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            background-image: url('{{ asset('image/MP_Background.JPG') }}');
            background-size: cover; background-position: center; background-repeat: no-repeat;
            background-attachment: fixed; min-height: 100vh;
        }
        .header {
            position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
            background-color: #0033a0; color: white; display: flex;
            align-items: center; justify-content: space-between; padding: 10px 30px;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left img { width: 45px; height: 45px; border-radius: 50%; }
        .header-left h1 { font-size: 22px; }
        .header-left p { font-size: 12px; }

        .page-content { padding-top: 85px; min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 25, 70, 0.70); z-index: 0; pointer-events: none; }
        
        .card {
            position: relative; z-index: 2;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 16px; padding: 36px 32px; width: 92%; max-width: 440px;
            color: white; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .seal { width: 75px; height: 75px; border-radius: 50%; background: white; padding: 4px; margin-bottom: 12px; }
        h2 { font-size: 24px; margin-bottom: 4px; letter-spacing: 0.5px; }
        p.subtitle { font-size: 13px; opacity: 0.9; margin-bottom: 22px; }

        .input-group { margin-bottom: 16px; text-align: left; }
        .input-group label { display: block; font-size: 12px; margin-bottom: 5px; opacity: 0.9; }
        .input-group input {
            width: 100%; padding: 12px 14px; border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.5); background: rgba(255, 255, 255, 0.12);
            color: white; font-size: 14px; outline: none;
        }
        .input-group input:focus { border-color: #60a5fa; background: rgba(255, 255, 255, 0.2); }
        .input-group input::placeholder { color: rgba(255, 255, 255, 0.65); }

        .password-wrapper { position: relative; }
        .toggle-password { position: absolute; right: 12px; top: 38px; cursor: pointer; color: white; }

        .btn-submit {
            width: 100%; padding: 12px; background: #0047ab; color: white;
            border: none; border-radius: 8px; font-weight: bold; font-size: 15px;
            cursor: pointer; transition: background 0.2s ease; margin-top: 10px;
        }
        .btn-submit:hover { background: #0033a0; }

        .alert-box {
            background: rgba(239, 68, 68, 0.25); border: 1px solid #ef4444; color: #fca5a5;
            padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga &bull; Account Security</p>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="overlay"></div>
        <div class="card">
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Seal" class="seal">
            <h2>SET NEW PASSWORD</h2>
            <p class="subtitle">Enter your fresh credentials below</p>

            @if(isset($errors) && $errors->any())
                <div class="alert-box">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.submit') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="input-group">
                    <label><i class="fa-solid fa-envelope"></i> Account Email</label>
                    <input type="text" value="{{ $email }}" disabled style="opacity: 0.7; cursor: not-allowed;">
                </div>

                <div class="input-group password-wrapper">
                    <label><i class="fa-solid fa-lock"></i> New Password</label>
                    <input type="password" name="password" id="newPassword" placeholder="Minimum 6 characters" minlength="6" required>
                    <span class="toggle-password" onclick="togglePass('newPassword', 'eye1')">
                        <i class="fa-solid fa-eye-slash" id="eye1"></i>
                    </span>
                </div>

                <div class="input-group password-wrapper">
                    <label><i class="fa-solid fa-shield"></i> Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Repeat new password" minlength="6" required>
                    <span class="toggle-password" onclick="togglePass('confirmPassword', 'eye2')">
                        <i class="fa-solid fa-eye-slash" id="eye2"></i>
                    </span>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-check-circle"></i> UPDATE & SAVE PASSWORD
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
