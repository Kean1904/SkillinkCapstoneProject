<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Mail\WorkerVerificationMail;
use App\Mail\SystemAnnouncementMail;
use App\Services\ResendMailService;

$targetEmail = $argv[1] ?? null;

if (!$targetEmail) {
    echo "===============================================================\n";
    echo "  SKILLINK - LIVE SMTP TRANSMISSION TESTER                     \n";
    echo "===============================================================\n";
    echo "Usage:\n";
    echo "  php send_test_email.php <target_email> [template_type]\n\n";
    echo "Template types:\n";
    echo "  1 = reset         (Password Reset Email with OTP)\n";
    echo "  2 = accreditation (PESO Worker Verification Letter)\n";
    echo "  3 = announcement  (Municipal Maintenance / System Advisory)\n\n";
    echo "Example:\n";
    echo "  php send_test_email.php myemail@gmail.com reset\n";
    echo "===============================================================\n";
    exit(0);
}

$type = $argv[2] ?? 'reset';

echo "\nInitiating real SMTP transmission to: {$targetEmail}...\n";
echo "Current SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Current SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Current Mail From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")\n\n";

$mockUser = new User([
    'name' => 'test_user',
    'first_name' => 'Juan',
    'last_name' => 'Dela Cruz',
    'email' => $targetEmail,
    'skills' => 'Plumbing & Electrical Repair',
]);

try {
    if ($type === 'accreditation') {
        echo "Rendering and sending: WorkerVerificationMail...\n";
        ResendMailService::sendMailable($targetEmail, new WorkerVerificationMail($mockUser));
    } elseif ($type === 'announcement') {
        echo "Rendering and sending: SystemAnnouncementMail...\n";
        ResendMailService::sendMailable($targetEmail, new SystemAnnouncementMail(
            $mockUser,
            'Scheduled System Maintenance & Server Upgrade',
            "Ang SKILLINK Portal ay magkakaroon ng routine scheduled system maintenance sa darating na Linggo mula 11:00 PM hanggang 3:00 AM para sa pag-optimize ng aming database at cloud servers.\n\nMaraming salamat sa inyong kooperasyon!\n— PESO LGU Magalang",
            'maintenance'
        ));
    } else {
        echo "Rendering and sending: PasswordResetMail...\n";
        $resetUrl = url('/password/reset/' . \Illuminate\Support\Str::random(60) . '?email=' . urlencode($targetEmail));
        ResendMailService::sendMailable($targetEmail, new PasswordResetMail($mockUser, $resetUrl, strval(rand(100000, 999999))));
    }

    echo "\n>>> [SUCCESS] Email transmitted successfully! Check your inbox or spam folder at {$targetEmail}! <<<\n\n";
} catch (\Throwable $e) {
    echo "\n>>> [ERROR] Email transmission failed: " . $e->getMessage() . "\n\n";
    echo "Tip: Make sure MAIL_USERNAME and MAIL_PASSWORD in your .env are configured with your actual email credentials or a 16-character Google App Password (not your personal Gmail password).\n\n";
    exit(1);
}
