<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - SKILLINK Magalang</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- HEADER -->
        <div style="background: {{ $type === 'maintenance' ? '#b45309' : '#0033a0' }}; color: white; padding: 22px 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: 1px;">SKILLINK</h1>
            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.9;">
                {{ $type === 'maintenance' ? 'System Maintenance & Technical Advisory' : 'Public Employment Service Office (PESO) Bulletin' }}
            </p>
        </div>

        <!-- BODY -->
        <div style="padding: 28px 24px;">
            <div style="margin-bottom: 16px;">
                <span style="display: inline-block; background: {{ $type === 'maintenance' ? '#fef3c7' : '#dbeafe' }}; color: {{ $type === 'maintenance' ? '#92400e' : '#1e40af' }}; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ $type === 'maintenance' ? 'System Advisory' : 'Official Announcement' }}
                </span>
            </div>

            <h2 style="color: #111827; font-size: 20px; margin-top: 0; margin-bottom: 14px;">
                {{ $title }}
            </h2>

            <p style="font-size: 14px; line-height: 1.6;">
                Magandang araw, <strong>{{ is_string($recipient) ? $recipient : ($recipient->full_name ?? $recipient->name ?? 'Ka-Barangay') }}</strong>!
            </p>

            <div style="font-size: 14px; line-height: 1.7; color: #374151; background: #f8fafc; border-left: 4px solid {{ $type === 'maintenance' ? '#f59e0b' : '#3b82f6' }}; padding: 16px; margin: 18px 0; border-radius: 0 8px 8px 0;">
                {!! nl2br(e($messageBody)) !!}
            </div>

            <p style="font-size: 13px; line-height: 1.6; color: #4b5563; margin-top: 20px;">
                Para sa karagdagang impormasyon o mga katanungan, mangyaring makipag-ugnayan sa PESO Magalang sa pamamagitan ng opisyal na portal o mag-login sa inyong SKILLINK app.
            </p>
        </div>

        <!-- FOOTER -->
        <div style="background: #f1f5f9; padding: 14px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;">
            Pamahalaang Bayan ng Magalang &bull; Public Employment Service Office (PESO) &bull; SKILLINK System
        </div>
    </div>
</body>
</html>
