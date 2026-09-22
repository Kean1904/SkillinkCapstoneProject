<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Skilled Worker Official Accreditation - SKILLINK PESO Magalang</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- HEADER -->
        <div style="background: #0033a0; color: white; padding: 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px;">SKILLINK</h1>
            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.9;">Public Employment Service Office &bull; Municipality of Magalang</p>
        </div>

        <!-- BODY -->
        <div style="padding: 28px 24px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="display: inline-block; background: #ecfdf5; border: 2px solid #10b981; color: #065f46; padding: 6px 16px; border-radius: 20px; font-weight: bold; font-size: 13px; letter-spacing: 0.5px;">
                    &#10004; ACCREDITED & VERIFIED
                </div>
            </div>

            <h2 style="color: #065f46; font-size: 20px; margin-top: 0; text-align: center; margin-bottom: 12px;">
                Pagbati! Ang Iyong Profile ay Opisyal nang Accredited!
            </h2>

            <p style="font-size: 14px; line-height: 1.6;">
                Magandang araw, <strong>{{ $worker->full_name ?? $worker->name }}</strong>!
            </p>

            <p style="font-size: 14px; line-height: 1.6; color: #374151;">
                Matagumpay nang nasuri at naaprubahan ng <strong>PESO Staff ng Munisipyo ng Magalang</strong> ang iyong mga dokumento at kredensyal bilang <strong>Skilled Worker</strong>.
            </p>

            <!-- ACCREDITATION DETAILS CARD -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 18px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Pangalan ng Manggagawa:</strong> {{ $worker->full_name }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Username:</strong> @ {{ $worker->name }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Rehistradong Barangay:</strong> Brgy. {{ $worker->barangay }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Kasanayan (Skills):</strong> {{ $worker->skills ?? 'General Handyman' }}</p>
                <p style="margin: 0; font-size: 13px;"><strong>Accreditation Status:</strong> <span style="color: #059669; font-weight: bold;">VERIFIED PESO SKILLED WORKER</span></p>
            </div>

            <h3 style="font-size: 15px; color: #1e3a8a; margin-top: 20px; margin-bottom: 8px;">Ano ang mga benepisyo ng iyong Verified Badge?</h3>
            <ul style="font-size: 13px; line-height: 1.6; color: #4b5563; padding-left: 20px; margin-top: 0;">
                <li><strong>Mataas na Ranking sa Job Matching Algorithm</strong> kapag naghahanap ang mga residente ng iyong kasanayan sa Magalang.</li>
                <li><strong>Green Verified Badge</strong> na makikita sa iyong profile para sa tiwala at proteksyon ng kliyente.</li>
                <li>Karapatang makatanggap ng direktang <strong>Service Bookings</strong> at mag-apply sa municipal job postings (SPES / TUPAD).</li>
            </ul>

            <p style="font-size: 13px; line-height: 1.6; color: #4b5563; margin-top: 18px;">
                Maaari mo nang buksan ang iyong <strong>SKILLINK Mobile Application</strong> upang tingnan ang iyong na-update na profile at simulan ang pagtanggap ng mga booking.
            </p>
        </div>

        <!-- FOOTER -->
        <div style="background: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;">
            Public Employment Service Office (PESO) &bull; Pamahalaang Bayan ng Magalang, Lalawigan ng Pampanga.
        </div>
    </div>
</body>
</html>
