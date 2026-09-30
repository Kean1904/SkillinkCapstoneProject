<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BrevoOtpService
{
    /**
     * Send OTP Authentication email via Brevo REST API (HTTPS port 443) or Brevo SMTP Relay.
     *
     * @param string $recipientEmail Email address fetched from the database
     * @param string $recipientName Name of the user
     * @param string $otp 6-digit OTP code
     * @param string $resetUrl Direct reset password URL
     * @return array [ 'success' => bool, 'message' => string, 'error' => ?string ]
     */
    public static function sendPasswordResetOtp(string $recipientEmail, string $recipientName, string $otp, string $resetUrl): array
    {
        $senderEmail = env('MAIL_FROM_ADDRESS') ?: config('mail.from.address', 'keanashleym@gmail.com');
        $senderName  = env('MAIL_FROM_NAME') ?: config('mail.from.name', 'PESO Magalang - SKILLINK');
        $smtpKey     = env('MAIL_PASSWORD') ?: config('mail.mailers.smtp.password', 'xsmtpsib-03926c431c6f9e1f62df9fbbe0cee52cd8ffd4aadc3277c43328da004e9c53b5-EGzNRtotcQ06vb0N');

        $htmlContent = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SKILLINK Password Reset OTP</title>
        </head>
        <body style="font-family: Arial, Helvetica, sans-serif; background-color: #f4f6f9; margin: 0; padding: 24px; color: #333333;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.08);">
                <!-- Header -->
                <tr>
                    <td style="background-color: #0033a0; padding: 24px; text-align: center; color: #ffffff;">
                        <h1 style="margin: 0; font-size: 22px; font-weight: bold; letter-spacing: 1px;">SKILLINK</h1>
                        <p style="margin: 4px 0 0 0; font-size: 13px; opacity: 0.9;">Municipality of Magalang &bull; PESO Portal</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding: 32px 28px;">
                        <h2 style="margin: 0 0 12px 0; font-size: 19px; color: #1e293b;">OTP Authentication Code</h2>
                        <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                            Magandang araw, <strong>' . htmlspecialchars($recipientName) . '</strong>!
                        </p>
                        <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                            Nakatanggap ang <strong>PESO Magalang - SKILLINK</strong> ng kahilingan na i-reset ang password ng iyong account. Gamitin ang 6-digit One-Time Password (OTP) na ito upang kumpirmahin ang iyong pagkakakilanlan:
                        </p>

                        <!-- OTP Box -->
                        <div style="background-color: #eef2ff; border: 2px dashed #4f46e5; border-radius: 10px; padding: 18px; text-align: center; margin: 24px 0;">
                            <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; color: #4f46e5; display: block; margin-bottom: 6px;">
                                Iyong 6-Digit Verification OTP
                            </span>
                            <span style="font-family: \'Courier New\', Courier, monospace; font-size: 34px; font-weight: 800; letter-spacing: 6px; color: #0033a0;">
                                ' . htmlspecialchars($otp) . '
                            </span>
                        </div>

                        <!-- Reset Link Button -->
                        <div style="text-align: center; margin: 28px 0;">
                            <a href="' . htmlspecialchars($resetUrl) . '" style="background-color: #0033a0; color: #ffffff; padding: 14px 28px; font-size: 14px; font-weight: bold; text-decoration: none; border-radius: 8px; display: inline-block; box-shadow: 0 3px 10px rgba(0, 51, 160, 0.3);">
                                I-reset ang Aking Password Ngayon &rarr;
                            </a>
                        </div>

                        <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 24px 0 0 0;">
                            O maaari mo ring i-copy at i-paste ang link na ito sa iyong browser kung hindi gumagana ang button sa itaas:<br>
                            <a href="' . htmlspecialchars($resetUrl) . '" style="color: #2563eb; word-break: break-all; font-size: 11.5px;">' . htmlspecialchars($resetUrl) . '</a>
                        </p>

                        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 28px 0 20px 0;">

                        <!-- Security Notice -->
                        <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 14px; border-radius: 4px;">
                            <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;">
                                <strong>Paalala sa Seguridad:</strong> Ang OTP code at reset link na ito ay may bisa lamang sa loob ng <strong>30 minuto</strong>. Huwag ibahagi ang iyong OTP kaninuman, kabilang ang mga opisyal o tauhan ng PESO.
                            </p>
                        </div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 11.5px; color: #94a3b8;">
                        <p style="margin: 0 0 4px 0;">Public Employment Service Office (PESO) &bull; Municipality of Magalang, Pampanga</p>
                        <p style="margin: 0;">Automated email notification mula sa SKILLINK System. Huwag sagutin ang sulat na ito.</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        // 1. FIRST ATTEMPT: Brevo REST API via HTTPS (Port 443 - Never blocked by Railway/Firewalls)
        $apiError = null;
        $activeApiKey = env('BREVO_API_KEY') ?: $smtpKey;

        try {
            $apiResponse = Http::timeout(6)
                ->withHeaders([
                    'api-key'      => $activeApiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name'  => $senderName,
                        'email' => $senderEmail,
                    ],
                    'to' => [
                        [
                            'email' => $recipientEmail,
                            'name'  => $recipientName,
                        ],
                    ],
                    'subject'     => "SKILLINK OTP Authentication Code: [{$otp}]",
                    'htmlContent' => $htmlContent,
                ]);

            if ($apiResponse->successful()) {
                Log::info("Brevo REST API successfully delivered OTP to {$recipientEmail}.");
                return [
                    'success' => true,
                    'message' => "Matagumpay na naipadala ang OTP sa iyong rehistradong email ({$recipientEmail}).",
                    'error'   => null,
                ];
            } else {
                $body = $apiResponse->json();
                $apiError = $body['message'] ?? $apiResponse->body();
                Log::warning("Brevo REST API response (" . $apiResponse->status() . "): " . $apiError);
            }
        } catch (\Throwable $apiEx) {
            $apiError = $apiEx->getMessage();
            Log::warning("Brevo REST API call failed: " . $apiError);
        }

        // 2. SECOND ATTEMPT: Brevo SMTP Relay
        $smtpErrors = [];
        if ($apiError) {
            if (str_starts_with($activeApiKey, 'xsmtpsib-')) {
                $smtpErrors[] = "Brevo API requires an API key (xkeysib-), while an SMTP key (xsmtpsib-) is currently configured.";
            } else {
                $smtpErrors[] = "Brevo API: " . $apiError;
            }
        }

        $portsToTry = [587, 2525];
        foreach ($portsToTry as $port) {
            try {
                config([
                    'mail.default'                 => 'smtp',
                    'mail.mailers.smtp.transport'  => 'smtp',
                    'mail.mailers.smtp.host'       => env('MAIL_HOST') ?: 'smtp-relay.brevo.com',
                    'mail.mailers.smtp.port'       => $port,
                    'mail.mailers.smtp.encryption' => 'tls',
                    'mail.mailers.smtp.username'   => env('MAIL_USERNAME') ?: 'ba9897001@smtp-brevo.com',
                    'mail.mailers.smtp.password'   => $smtpKey,
                    'mail.mailers.smtp.timeout'    => 6,
                    'mail.from.address'            => $senderEmail,
                    'mail.from.name'               => $senderName,
                ]);

                app()->forgetInstance('mailer');

                Mail::html($htmlContent, function ($message) use ($recipientEmail, $recipientName, $otp, $senderEmail, $senderName) {
                    $message->from($senderEmail, $senderName)
                            ->to($recipientEmail, $recipientName)
                            ->subject("SKILLINK OTP Authentication Code: [{$otp}]");
                });

                Log::info("Brevo SMTP successfully dispatched OTP to {$recipientEmail} via port {$port}.");

                return [
                    'success' => true,
                    'message' => "Matagumpay na naipadala ang OTP sa iyong rehistradong email ({$recipientEmail}).",
                    'error'   => null,
                ];
            } catch (\Throwable $smtpEx) {
                $msg = $smtpEx->getMessage();
                Log::error("Brevo SMTP Port {$port} Failed for {$recipientEmail}: " . $msg);
                $smtpErrors[] = "Railway SMTP Port {$port} blocked (Connection timed out)";
            }
        }

        // If all attempts failed
        return [
            'success' => false,
            'message' => "Hindi maipadala ang email sa pamamagitan ng Brevo.",
            'error'   => implode(' | ', $smtpErrors),
        ];
    }
}
