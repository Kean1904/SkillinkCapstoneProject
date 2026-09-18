<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Service Booking Alert - SKILLINK</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="background: #0033a0; color: white; padding: 20px 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 22px; letter-spacing: 1px;">SKILLINK</h1>
            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.85;">PESO Magalang Official Employment & Booking Platform</p>
        </div>
        <div style="padding: 24px;">
            <h2 style="color: #1e3a8a; font-size: 18px; margin-top: 0;">May Bagong Booking Request para sa Iyo!</h2>
            <p style="font-size: 14px; line-height: 1.6;">
                Magandang araw, <strong>{{ $booking->worker_name ?? $booking->worker_username }}</strong>!
            </p>
            <p style="font-size: 14px; line-height: 1.6;">
                Isang residente ng Magalang ang direktang nag-request ng iyong serbisyo sa pamamagitan ng SKILLINK mobile app:
            </p>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;">
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Booking Reference:</strong> <span style="color: #2563eb; font-weight: bold;">{{ $booking->booking_reference }}</span></p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Kliyente:</strong> {{ $booking->client_name ?? $booking->client_username }} (@ {{ $booking->client_username }})</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Serbisyo:</strong> {{ $booking->service_category }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Lokasyon:</strong> {{ $booking->service_address }}, Brgy. {{ $booking->barangay }}</p>
                <p style="margin: 0 0 8px 0; font-size: 13px;"><strong>Petsa at Oras:</strong> {{ $booking->scheduled_date }}</p>
                <p style="margin: 0; font-size: 13px;"><strong>Tinatayang Badyet:</strong> <span style="color: #16a34a; font-weight: bold;">{{ $booking->estimated_budget }}</span></p>
            </div>
            <p style="font-size: 13px; line-height: 1.6; color: #4b5563;">
                Mangyaring buksan ang inyong <strong>SKILLINK Mobile App</strong> at magtungo sa <strong>"Tracking Service"</strong> upang i-accept o i-update ang progress ng serbisyong ito.
            </p>
        </div>
        <div style="background: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 11px; color: #64748b;">
            Ipinadala ng Public Employment Service Office (PESO) — Municipality of Magalang, Pampanga.
        </div>
    </div>
</body>
</html>
