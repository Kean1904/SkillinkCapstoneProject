<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Job Application Alert - SKILLINK</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="background: #0033a0; color: white; padding: 20px 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 22px; letter-spacing: 1px;">SKILLINK</h1>
            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.85;">PESO Magalang Official Employment & Booking Platform</p>
        </div>
        <div style="padding: 24px;">
            <h2 style="color: #1e3a8a; font-size: 18px; margin-top: 0;">May Bagong Aplikante sa Trabaho!</h2>
            <p style="font-size: 14px; line-height: 1.6;">
                Magandang araw! May nagsumite ng aplikasyon para sa municipal job opening:
            </p>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;">
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Trabaho / Gawain:</strong> <span style="color: #2563eb; font-weight: bold;">{{ $job->title }}</span></p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Kategorya:</strong> {{ $job->category }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Lokasyon:</strong> Brgy. {{ $job->barangay ?? $job->location_tag }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Aplikanteng Manggagawa:</strong> <span style="color: #15803d; font-weight: bold;">{{ $applicant->name ?? $applicant->username }}</span> (@ {{ $applicant->username }})</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Kasanayan (Skills):</strong> {{ $applicant->skills ?? 'Certified Worker' }}</p>
                <p style="margin: 0; font-size: 13px;"><strong>Contact / Telepono:</strong> {{ $applicant->phone_number ?? 'Walang nakalagay' }}</p>
            </div>
            <p style="font-size: 13px; line-height: 1.6; color: #4b5563;">
                Maaari ninyong tingnan ang detalye at i-review ang accreditation sa <strong>PESO Staff Web Dashboard</strong> o sa inyong mobile application.
            </p>
        </div>
        <div style="background: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 11px; color: #64748b;">
            Ipinadala ng Public Employment Service Office (PESO) — Municipality of Magalang, Pampanga.
        </div>
    </div>
</body>
</html>
