<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Request - SKILLINK</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- HEADER -->
        <div style="background: #0033a0; color: white; padding: 22px 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px;">SKILLINK</h1>
            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.9;">Public Employment Service Office (PESO) &bull; Magalang, Pampanga</p>
        </div>

        <!-- BODY -->
        <div style="padding: 28px 24px;">
            <h2 style="color: #1e3a8a; font-size: 19px; margin-top: 0; margin-bottom: 12px;">Kahilingan sa Pag-Reset ng Password</h2>
            
            <p style="font-size: 14px; line-height: 1.6; margin-bottom: 14px;">
                Magandang araw, <strong>{{ $user->full_name ?? $user->name }}</strong>!
            </p>

            <p style="font-size: 14px; line-height: 1.6; color: #374151; margin-bottom: 20px;">
                Nakatanggap ang SKILLINK ng kahilingan na i-reset ang password para sa iyong account (<strong>{{ $user->name }}</strong>). Kung ikaw ang humiling nito, i-click ang asul na button sa ibaba upang makapagtala ng bagong password:
            </p>

            <!-- ACTION BUTTON -->
            <div style="text-align: center; margin: 26px 0;">
                <a href="{{ $resetUrl }}" style="background: #0033a0; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 4px 10px rgba(0, 51, 160, 0.3);">
                    I-RESET ANG AKING PASSWORD
                </a>
            </div>

            @if(!empty($otp))
            <!-- OTP DISPLAY FOR MOBILE APP -->
            <div style="background: #f8fafc; border: 1px dashed #2563eb; border-radius: 8px; padding: 16px; margin: 20px 0; text-align: center;">
                <p style="margin: 0 0 6px 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">6-Digit Mobile Verification Code:</p>
                <div style="font-size: 28px; font-weight: 900; letter-spacing: 6px; color: #1e3a8a;">{{ $otp }}</div>
                <p style="margin: 6px 0 0 0; font-size: 11px; color: #94a3b8;">Maaari mo ring gamitin ang code na ito sa SKILLINK Mobile App.</p>
            </div>
            @endif

            <p style="font-size: 12px; line-height: 1.6; color: #6b7280; margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 14px;">
                <strong>Paalala sa Seguridad:</strong> Ang link at code na ito ay magpapaso sa loob ng <strong>30 minuto</strong>. Kung hindi mo hiniling ang pag-reset na ito, maaari mo itong balewalain at mananatiling ligtas ang iyong kasalukuyang password.
            </p>

            <p style="font-size: 11px; color: #9ca3af; word-break: break-all; margin-top: 12px;">
                Kung hindi mapindot ang button, kopyahin at i-paste ang URL na ito sa iyong browser:<br>
                <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
            </p>
        </div>

        <!-- FOOTER -->
        <div style="background: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;">
            Opisyal na abiso mula sa Public Employment Service Office (PESO) &bull; Munisipyo ng Magalang, Pampanga.
        </div>
    </div>
</body>
</html>
