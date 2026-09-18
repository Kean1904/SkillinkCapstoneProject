<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKILLINK - Municipality of Magalang</title>
      <link rel="icon" type="image/png" href="{{ asset('image/MP_Logo.png') }}">
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

        .login-btn {
            border: 1px solid white;
            color: white;
            background: transparent;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .login-btn:hover {
            background: white;
            color: #0033a0;
        }

        /* HERO / BACKGROUND SECTION */
        .hero {
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            color: white;
            padding-top: 50px; 
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: rgba(10, 25, 70, 0.65);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: bounceIn 0.8s ease;
        }

        .seal-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin-bottom: 15px;
        }

        .hero-content h2 {
            font-size: 32px;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .hero-content p.subtitle {
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* STAT CARDS */
        .stats-container {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            padding: 20px 40px;
            min-width: 220px;
        }

        .stat-card p.label {
            font-size: 13px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .stat-card p.number {
            font-size: 36px;
            font-weight: bold;
        }

        /* AVAILABLE JOBS SECTION */
        .available-jobs {
            max-width: 700px;
            margin: 40px auto 0 auto;
            padding: 0 20px;
            color: white;
            text-align: left;
        }

        .available-jobs h3 {
            font-size: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .jobs-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .job-card {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 25px 30px;
            min-height: 60px;
            display: flex;
            align-items: center;
        }

        .job-title {
            font-size: 16px;
            font-weight: bold;
            color: white;
            letter-spacing: 0.3px;
        }

        .job-card:hover {
            background: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            transition: background 0.2s ease;
        }

        /*Bounce Effect */
        @keyframes bounceIn {
            0% {
                transform: translateY(-40px);
                opacity: 0;
            }
            60% {
                transform: translateY(15px);
                opacity: 1;
            }
            80% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0);
            }
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
        <a href="{{ route('Login') }}" class="login-btn">Login</a>
    </div>

    <!-- HERO SECTION -->
    <div class="hero">
        <div class="hero-content">
            <img src="{{ asset('image/MP_Logo.png') }}" alt="Seal" class="seal-logo">
            <h2>MUNICIPALITY OF MAGALANG</h2>
            <p class="subtitle">Public Employment Service Office.</p>
        </div>
    </div>

</body>
</html>