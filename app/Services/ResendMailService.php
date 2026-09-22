<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class ResendMailService
{
    /**
     * Send email using Resend HTTPS REST API (Port 443), with automatic fallback to Laravel SMTP.
     *
     * @param string $to
     * @param Mailable $mailable
     * @return bool
     */
    public static function sendMailable(string $to, Mailable $mailable): bool
    {
        $apiKey = env('RESEND_API_KEY');

        if (!empty($apiKey)) {
            try {
                $html = $mailable->render();

                // Extract subject from envelope or property
                $subject = 'SKILLINK Magalang Notification';
                if (method_exists($mailable, 'envelope')) {
                    $envelope = $mailable->envelope();
                    if ($envelope && !empty($envelope->subject)) {
                        $subject = $envelope->subject;
                    }
                } elseif (!empty($mailable->subject)) {
                    $subject = $mailable->subject;
                }

                $fromName = config('mail.from.name', 'SKILLINK PESO Magalang');
                $fromHeader = "{$fromName} <onboarding@resend.dev>";

                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.resend.com/emails', [
                    'from' => $fromHeader,
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $html,
                ]);

                if ($response->successful()) {
                    Log::info("Resend HTTPS API delivered email to {$to} [Subject: {$subject}]");
                    return true;
                } else {
                    Log::warning("Resend HTTPS API failed for {$to} (Status {$response->status()}): " . $response->body() . ". Attempting SMTP fallback...");
                }
            } catch (\Throwable $e) {
                Log::warning("Resend HTTPS API exception for {$to}: " . $e->getMessage() . ". Attempting SMTP fallback...");
            }
        }

        // Fallback to standard Laravel SMTP Mail facade
        try {
            Mail::to($to)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error("SMTP delivery also failed for {$to}: " . $e->getMessage());
            return false;
        }
    }
}
