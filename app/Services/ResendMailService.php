<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class ResendMailService
{
    /**
     * Send email using Brevo or Resend HTTPS REST API (Port 443), with automatic fallback to Laravel SMTP.
     *
     * @param string $to
     * @param Mailable $mailable
     * @return bool
     */
    public static function sendMailable(string $to, Mailable $mailable): bool
    {
        $brevoKey = config('services.brevo.key') ?: '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';
        $resendKey = config('services.resend.key') ?: env('RESEND_API_KEY');

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
        }

        $fromName = config('mail.from.name', 'PESO Magalang - SKILLINK');
        // Brevo requires a sender address validated in your Brevo account (torreskeanashleym2021@gmail.com)
        $brevoSenderEmail = config('services.brevo.sender_email') ?: env('BREVO_SENDER_EMAIL', 'torreskeanashleym2021@gmail.com');

        // 🌟 TIER 1: Brevo HTTPS REST API (Supports sending to ANY recipient email over port 443)
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
                    return true;
                } else {
                    Log::warning("Brevo API failed for {$to} (Status {$brevoRes->status()}): " . $brevoRes->body());
                }
            } catch (\Throwable $e) {
                Log::warning("Brevo API exception for {$to}: " . $e->getMessage());
            }
        }

        // 🌟 TIER 2: Resend HTTPS REST API (Port 443)
        if (!empty($resendKey)) {
            try {
                $fromHeader = "{$fromName} <onboarding@resend.dev>";
                $resendRes = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $resendKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.resend.com/emails', [
                    'from' => $fromHeader,
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $html,
                ]);

                if ($resendRes->successful()) {
                    Log::info("Resend HTTPS API delivered email to {$to} [Subject: {$subject}]");
                    return true;
                } else {
                    Log::warning("Resend API failed for {$to} (Status {$resendRes->status()}): " . $resendRes->body());
                }
            } catch (\Throwable $e) {
                Log::warning("Resend API exception for {$to}: " . $e->getMessage());
            }
        }

        // 🌟 TIER 3: Fallback to standard Laravel SMTP
        try {
            Mail::to($to)->send($mailable);
            Log::info("Fallback SMTP delivered email to {$to}");
            return true;
        } catch (\Throwable $e) {
            Log::error("SMTP delivery also failed for {$to}: " . $e->getMessage());
            return false;
        }
    }
}
