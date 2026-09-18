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

            <button type="submit" class="login-btn">LOG IN</button>

            <p class="signup-text">Don't have an account? <a href="{{ route('Register') }}">Sign up here</a></p>
        </form>
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
    </script>

</body>
</html>