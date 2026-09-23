<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class BrevoMailService
{
    public static $lastError = null;

    /**
     * Send email using Brevo HTTPS REST API (Port 443), with automatic fallback to Laravel Mail.
     *
     * @param string $to
     * @param Mailable $mailable
     * @return bool
     */
    public static function sendMailable(string $to, Mailable $mailable): bool
    {
        self::$lastError = null;
        $brevoKey = config('services.brevo.key') ?: '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';

        // Siguraduhing ang verified Gmail mo ang laging sender:
        $senderEmail = config('services.brevo.sender_email') 
            ?: env('BREVO_SENDER_EMAIL') 
            ?: 'torreskeanashleym2021@gmail.com';
        $senderName = config('mail.from.name') ?: 'PESO Magalang - SKILLINK';

        $html = '';
        $subject = 'SKILLINK Magalang Notification';

        try {
            $html = $mailable->render();

            if (method_exists($mailable, 'envelope')) {
                $envelope = $mailable->envelope();
                if ($envelope && !empty($envelope->subject)) {
                    $subject = $envelope->subject;
                }
            } elseif (!empty($mailable->subject)) {
                $subject = $mailable->subject;
            }
        } catch (\Throwable $e) {
            Log::error("Failed to render mailable before sending: " . $e->getMessage());
            self::$lastError = "Render failed: " . $e->getMessage();
        }

        if (empty($html)) {
            $html = '<div style="font-family: Arial, sans-serif; padding: 20px;">'
                . '<h2>' . htmlspecialchars($subject) . '</h2>'
                . '<p>Pakisuri ang iyong SKILLINK account para sa detalye.</p>'
                . '</div>';
        }

        $fromName = config('mail.from.name', 'PESO Magalang - SKILLINK');
        // Brevo requires a sender address validated in your Brevo account (torreskeanashleym2021@gmail.com)
        $brevoSenderEmail = config('services.brevo.sender_email') ?: 'torreskeanashleym2021@gmail.com';

        // 🌟 BREVO HTTPS REST API (Delivers to ANY recipient email via Port 443)
        if (!empty($brevoKey)) {
            try {
                $brevoRes = Http::timeout(12)->withHeaders([
                    'api-key' => $brevoKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $fromName,
                        'email' => $brevoSenderEmail,
                    ],
                    'to' => [
                        ['email' => $to]
                    ],
                    'subject' => $subject,
                    'htmlContent' => $html,
                ]);

                if ($brevoRes->successful()) {
                    Log::info("Brevo HTTPS API delivered email to {$to} [Subject: {$subject}]");
                    self::$lastError = null;
                    return true;
                } else {
                    self::$lastError = "Brevo failed (HTTP " . $brevoRes->status() . "): " . $brevoRes->body();
                    Log::warning("Brevo API failed for {$to} (Status {$brevoRes->status()}): " . $brevoRes->body());
                }
            } catch (\Throwable $e) {
                self::$lastError = "Brevo exception: " . $e->getMessage();
                Log::warning("Brevo API exception for {$to}: " . $e->getMessage());
            }
        }

        // 🌟 Fallback to standard Laravel Mail
        try {
            Mail::to($to)->send($mailable);
            Log::info("Fallback Mail delivered email to {$to}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Mail delivery also failed for {$to}: " . $e->getMessage());
            return false;
        }
    }
}
