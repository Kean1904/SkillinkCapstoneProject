<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BrevoOtpService
{
    /**
     * Send OTP Authentication email via Brevo SMTP
     *
     * @param string $recipientEmail Email address fetched from the database
     * @param string $recipientName Name of the user
     * @param string $otp 6-digit OTP code
     * @param string $resetUrl Direct reset password URL
     * @return array [ 'success' => bool, 'message' => string, 'error' => ?string ]
     */
    public static function sendPasswordResetOtp(string $recipientEmail, string $recipientName, string $otp, string $resetUrl): array
    {
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

        try {
            Mail::html($htmlContent, function ($message) use ($recipientEmail, $recipientName, $otp) {
                $fromAddress = config('mail.from.address', 'peso.skillink.magalang@gmail.com');
                $fromName    = config('mail.from.name', 'PESO Magalang - SKILLINK');

                $message->from($fromAddress, $fromName)
                        ->to($recipientEmail, $recipientName)
                        ->subject("SKILLINK OTP Authentication Code: [{$otp}]");
            });

            Log::info("Brevo OTP email successfully dispatched to {$recipientEmail}.");

            return [
                'success' => true,
                'message' => "Matagumpay na naipadala ang OTP sa iyong rehistradong email ({$recipientEmail}).",
                'error'   => null,
            ];
        } catch (\Throwable $e) {
            Log::error("Brevo OTP Email Dispatch Failed for {$recipientEmail}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => "Hindi maipadala ang email sa pamamagitan ng Brevo: " . $e->getMessage(),
                'error'   => $e->getMessage(),
            ];
        }
    }
}
