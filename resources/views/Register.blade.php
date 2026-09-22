<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Sign Up</title>
    <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* FIXED BACKGROUND */
        body {
            background-image: url('{{ asset('image/MP_Background.JPG') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* FIXED HEADER */
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

        /* PAGE WRAPPER */
        .page-content {
            padding-top: 65px;
            position: relative;
            min-height: 100vh;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 25, 70, 0.65);
            z-index: 0;
            pointer-events: none;
        }

        .page-inner {
            position: relative;
            z-index: 2;
        }

        /* REGISTER PAGE */
        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px 60px 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 40px 45px;
            width: 100%;
            max-width: 650px;
            text-align: center;
            color: white;
        }

        .register-card img.seal {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin-bottom: 15px;
        }

        .register-card h2 {
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .register-card p.subtitle {
            font-size: 13px;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .input-group {
            flex: 1;
            margin-bottom: 0;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .input-group.select-wrapper {
            position: relative;
        }

        .input-group.select-wrapper::after {
            content: "▼";
            font-size: 11px;
            color: white;
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .input-group select option {
            color: #000;
            background: #fff;
        }

        /* PASSWORD FIELD WITH EYE ICON */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            user-select: none;
        }

        .toggle-password:hover {
            color: white;
        }

        .register-btn {
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
            margin-top: 10px;
        }

        .register-btn:hover {
            background-color: #002580;
        }

        .login-text {
            margin-top: 15px;
            font-size: 13px;
        }

        .login-text a {
            color: white;
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        .password-hint {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: -10px;
            margin-bottom: 15px;
            text-align: left;
        }

        .password-hint.valid {
            color: #7fffa0;
        }

        .password-hint.invalid {
            color: #ffb3b3;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Logo">
            <div>
                <h1>SKILLINK</h1>
                <p>Magalang, Pampanga</p>
            </div>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">
        <div class="overlay"></div>
        <div class="page-inner">

            <div class="register-page">
                <form class="register-card" method="POST" action="{{ route('Register.submit') }}">
                    @csrf
                    <img src="{{ asset('image/MP_Logo.png') }}" alt="Seal" class="seal">
                    <h2>SIGN UP</h2>
                    <p class="subtitle">Create a new account</p>

                    @if (isset($errors) && $errors->any())
                    <div style="background: rgba(220, 38, 38, 0.25); border: 1px solid #ef4444; color: #fca5a5; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; text-align: left; line-height: 1.4;">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #f87171; margin-right: 6px;"></i>
                        <strong>Error:</strong> {{ $errors->first() }}
                    </div>
                    @endif

                    <!-- First name / Last name -->
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" name="first_name" placeholder="First name" required>
                        </div>
                        <div class="input-group">
                            <input type="text" name="last_name" placeholder="Last name" required>
                        </div>
                    </div>

                    <!-- Age / Sex-Gender -->
                    <div class="form-row">
                        <div class="input-group">
                            <input type="number" name="age" placeholder="Age" min="15" max="100" required>
                        </div>
                        <div class="input-group select-wrapper">
                            <select name="gender" required>
                                <option value="" disabled selected hidden>Sex/Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="input-group">
                        <input type="text" name="address" placeholder="Address" required>
                    </div>

                    <!-- Barangay Dropdown -->
                    <div class="input-group select-wrapper">
                        <select name="barangay" required>
                            <option value="" disabled selected hidden>Barangay</option>
                            <option value="Ayala">Ayala</option>
                            <option value="Bucanan">Bucanan</option>
                            <option value="Camias">Camias</option>
                            <option value="Dolores">Dolores</option>
                            <option value="Escaler">Escaler</option>
                            <option value="La Paz">La Paz</option>
                            <option value="Navaling">Navaling</option>
                            <option value="San Agustin">San Agustin</option>
                            <option value="San Antonio">San Antonio</option>
                            <option value="San Franciso">San Franciso</option>
                            <option value="San Ildefonso">San Ildefonso</option>
                            <option value="San Isidro">San Isidro</option>
                            <option value="San Jose">San Jose</option>
                            <option value="San Miguel">San Miguel</option>
                            <option value="San Nicolas 1">San Nicolas 1</option>
                            <option value="San Nicolas 2">San Nicolas 2</option>
                            <option value="San Pablo">San Pablo</option>
                            <option value="San Pedro 1">San Pedro 1</option>
                            <option value="San Pedro 2">San Pedro 2</option>
                            <option value="San Roque">San Roque</option>
                            <option value="San Vicente">San Vicente</option>
                            <option value="Santa Cruz">Santa Cruz</option>
                            <option value="Santa Lucia">Santa Lucia</option>
                            <option value="Santa Maria">Santa Maria</option>
                            <option value="Santo Niño">Santo Niño</option>
                            <option value="Santo Rosario">Santo Rosario</option>
                            <option value="Turu">Turu</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <!-- Cellphone Number -->
                    <div class="input-group">
                        <input type="text" name="cellphone" placeholder="Cellphone Number" pattern="[0-9]{11}" maxlength="11" required>
                    </div>

                    <!-- Role Dropdown -->
                    <div class="input-group select-wrapper">
                        <select name="role" id="roleSelect" onchange="onRoleChange()" required>
                            <option value="" disabled selected hidden>Role</option>
                            <option value="Skilled Worker">Skilled Worker</option>
                            <option value="Residential">Residential</option>
                            <option value="Peso Staff">Peso Staff</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <!-- Dynamic Skilled Worker Accreditation Fields -->
                    <div id="skilledWorkerFields" style="display: none; background: rgba(0, 51, 160, 0.25); border: 1px dashed rgba(255, 255, 255, 0.4); border-radius: 8px; padding: 14px; margin-bottom: 15px; text-align: left;">
                        <p style="font-size: 12px; margin-bottom: 10px; color: #93c5fd; font-weight: bold;">
                            <i class="fa-solid fa-certificate"></i> PESO Skilled Worker Accreditation
                        </p>
                        <div class="input-group" style="margin-bottom: 10px;">
                            <input type="text" name="skills" placeholder="Skills / Trabaho (hal. Plumbing, Electrical, Carpentry)">
                        </div>
                        <div class="input-group" style="margin-bottom: 0;">
                            <input type="text" name="certificate_proof" placeholder="TESDA Certificate / Proof (hal. TESDA NC II - Plumbing)">
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="input-group">
                        <input type="text" name="username" id="usernameInput" placeholder="Username" value="{{ old('username') }}" oninput="checkUsernameExtension()" required>
                    </div>
                    <p id="usernameHint" style="display: none; font-size: 11px; margin-top: -10px; margin-bottom: 15px; text-align: left; line-height: 1.4;"></p>

                    <!-- Password with eye toggle -->
                    <!-- Password with eye toggle -->
                    <div class="input-group password-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Password" 
                            minlength="12"
                            maxlength="16"
                            pattern="^(?=.*[0-9])(?=.*[A-Z])(?=.*[@_.%$])[A-Za-z0-9@_.%$]{12,16}$"
                            title="12-16 characters, at least 1 uppercase letter, 1 number, and 1 special character (@ _ . % $)"
                            oninput="checkPasswordStrength()"
                            required
                        >
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
                        </span>
                    </div>
                    <p id="passwordHint" class="password-hint">
                        Must be 12-16 characters, <br> with at least 1 uppercase letter, <br> 1 number, and 1 special character (@ _ . % $)
                    </p>

                    <button type="submit" class="register-btn">SIGN UP</button>

                    <p class="login-text">Already have an account? <a href="{{ route('Login') }}">Log in</a></p>
                </form>
            </div>

        </div>
    </div>

    <script>
        function onRoleChange() {
            const roleSelect = document.getElementById('roleSelect');
            const skilledFields = document.getElementById('skilledWorkerFields');
            const usernameInput = document.getElementById('usernameInput');
            const usernameHint = document.getElementById('usernameHint');
            const role = roleSelect.value;

            // Show/hide skilled worker fields
            if (role === 'Skilled Worker') {
                skilledFields.style.display = 'block';
            } else {
                skilledFields.style.display = 'none';
            }

            // Update placeholder at paalala ayon sa piniling role
            if (role === 'Admin') {
                usernameInput.placeholder = "Username (kailangan ng @Admin o @admin)";
                usernameHint.style.display = 'block';
                usernameHint.style.color = '#fde047';
                usernameHint.innerHTML = '<i class="fa-solid fa-circle-info"></i> <strong>Admin Requirement:</strong> Ang username ay dapat magtapos sa <strong>@admin</strong> o <strong>@Admin</strong> (hal. <code>juan@Admin</code>)';
            } else if (role === 'Peso Staff') {
                usernameInput.placeholder = "Username (kailangan ng @Staff o @staff)";
                usernameHint.style.display = 'block';
                usernameHint.style.color = '#93c5fd';
                usernameHint.innerHTML = '<i class="fa-solid fa-circle-info"></i> <strong>PESO Staff Requirement:</strong> Ang username ay dapat magtapos sa <strong>@staff</strong> o <strong>@Staff</strong> (hal. <code>maria@Staff</code>)';
            } else {
                usernameInput.placeholder = "Username";
                usernameHint.style.display = 'none';
                usernameHint.innerHTML = '';
            }

            checkUsernameExtension();
        }

        function checkUsernameExtension() {
            const roleSelect = document.getElementById('roleSelect');
            const usernameInput = document.getElementById('usernameInput');
            const usernameHint = document.getElementById('usernameHint');
            const role = roleSelect.value;
            const val = usernameInput.value.trim().toLowerCase();

            if (!val) {
                if (role === 'Admin' || role === 'Peso Staff') {
                    return;
                }
                usernameHint.style.display = 'none';
                return;
            }

            if (role === 'Admin') {
                usernameHint.style.display = 'block';
                if (val.endsWith('@admin')) {
                    usernameHint.style.color = '#86efac';
                    usernameHint.innerHTML = '<i class="fa-solid fa-circle-check"></i> <strong>Tamang format:</strong> May <code>@Admin</code> / <code>@admin</code> extension.';
                    usernameInput.setCustomValidity('');
                } else {
                    usernameHint.style.color = '#fca5a5';
                    usernameHint.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> <strong>Kulang ng extension:</strong> Ang Admin username ay dapat magtapos sa <strong>@admin</strong> o <strong>@Admin</strong> (hal. <code>' + (val.includes('@') ? val.split('@')[0] : val) + '@Admin</code>).';
                    usernameInput.setCustomValidity('Ang Admin username ay dapat magtapos sa @admin o @Admin');
                }
            } else if (role === 'Peso Staff') {
                usernameHint.style.display = 'block';
                if (val.endsWith('@staff')) {
                    usernameHint.style.color = '#86efac';
                    usernameHint.innerHTML = '<i class="fa-solid fa-circle-check"></i> <strong>Tamang format:</strong> May <code>@Staff</code> / <code>@staff</code> extension.';
                    usernameInput.setCustomValidity('');
                } else {
                    usernameHint.style.color = '#fca5a5';
                    usernameHint.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> <strong>Kulang ng extension:</strong> Ang PESO Staff username ay dapat magtapos sa <strong>@staff</strong> o <strong>@Staff</strong> (hal. <code>' + (val.includes('@') ? val.split('@')[0] : val) + '@Staff</code>).';
                    usernameInput.setCustomValidity('Ang PESO Staff username ay dapat magtapos sa @staff o @Staff');
                }
            } else {
                if (val.endsWith('@admin') || val.endsWith('@staff')) {
                    usernameHint.style.display = 'block';
                    usernameHint.style.color = '#fca5a5';
                    usernameHint.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Bawal gamitin ang extension na <strong>@admin</strong> o <strong>@staff</strong> para sa mga Residential o Skilled Worker.';
                    usernameInput.setCustomValidity('Ang @admin at @staff ay para lamang sa mga opisyal.');
                } else {
                    usernameHint.style.display = 'none';
                    usernameInput.setCustomValidity('');
                }
            }
        }

        function checkPasswordStrength() {
            const passwordInput = document.getElementById('password');
            const hint = document.getElementById('passwordHint');
            const pattern = /^(?=.*[0-9])(?=.*[A-Z])(?=.*[@_.%$])[A-Za-z0-9@_.%$]{12,16}$/;
            if (pattern.test(passwordInput.value)) {
                hint.classList.remove('invalid');
                hint.classList.add('valid');
            } else {
                hint.classList.remove('valid');
                hint.classList.add('invalid');
            }
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            onRoleChange();
        });
    </script>

</body>
</html>