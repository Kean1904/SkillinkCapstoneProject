<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Booking;
use App\Models\JobPost;
use App\Mail\PasswordResetMail;
use App\Mail\WorkerVerificationMail;
use App\Mail\SystemAnnouncementMail;
use App\Mail\BookingAlertMail;
use App\Mail\ApplicationAlertMail;

echo "=== TESTING SKILLINK EMAIL TEMPLATES RENDERING ===\n\n";

try {
    // 1. Test PasswordResetMail
    $mockUser = new User([
        'name' => 'kean_user',
        'first_name' => 'Kean',
        'last_name' => 'Admin',
        'email' => 'kean@example.com'
    ]);
    $resetMail = new PasswordResetMail($mockUser, 'https://skillink-magalang.up.railway.app/password/reset/testtoken123', '482910');
    $resetHtml = $resetMail->render();
    echo "[PASS] PasswordResetMail rendered successfully! (HTML length: " . strlen($resetHtml) . " chars)\n";

    // 2. Test WorkerVerificationMail
    $mockWorker = new User([
        'name' => 'juan_welder',
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan.worker@example.com',
        'skills' => 'Welding NC II, Electrical'
    ]);
    $workerMail = new WorkerVerificationMail($mockWorker);
    $workerHtml = $workerMail->render();
    echo "[PASS] WorkerVerificationMail rendered successfully! (HTML length: " . strlen($workerHtml) . " chars)\n";

    // 3. Test SystemAnnouncementMail
    $announceMail = new SystemAnnouncementMail(
        $mockUser,
        'Scheduled Server Maintenance & Optimization',
        'Mangyaring maabisuhan na ang SKILLINK Portal ay magkakaroon ng routine technical maintenance ngayong darating na Linggo mula 11:00 PM hanggang 3:00 AM.',
        'maintenance'
    );
    $announceHtml = $announceMail->render();
    echo "[PASS] SystemAnnouncementMail rendered successfully! (HTML length: " . strlen($announceHtml) . " chars)\n";

    // 4. Test BookingAlertMail
    $mockBooking = new Booking([
        'booking_reference' => 'BK-849201',
        'client_name' => 'Khane Hendrix Torres',
        'client_username' => 'khane_resident',
        'worker_name' => 'Juan Dela Cruz',
        'worker_username' => 'juan_welder',
        'service_category' => 'Plumbing Repair',
        'task_description' => 'Fix leaking water pipes under the kitchen sink and check main valve pressure.',
        'service_address' => 'Blk 12 Lot 4, Sta. Ines',
        'barangay' => 'Sta. Ines',
        'estimated_budget' => '₱850.00',
        'scheduled_date' => 'Sept 25, 2026',
        'status' => 'PENDING',
    ]);
    $bookingMail = new BookingAlertMail($mockBooking);
    $bookingHtml = $bookingMail->render();
    echo "[PASS] BookingAlertMail rendered successfully! (HTML length: " . strlen($bookingHtml) . " chars)\n";

    // 5. Test ApplicationAlertMail
    $mockJob = new JobPost([
        'request_id' => 101,
        'title' => 'Emergency Roofing Repair',
        'category' => 'Carpentry & Roofing',
        'barangay' => 'San Nicolas 1st',
        'description' => 'Need experienced carpenter to repair roof leaks and damaged trusses.',
        'preferred_schedule' => 'Urgent / Within 24h',
        'posted_by' => 'Khane Hendrix Torres',
    ]);
    $appMail = new ApplicationAlertMail($mockJob, $mockWorker);
    $appHtml = $appMail->render();
    echo "[PASS] ApplicationAlertMail rendered successfully! (HTML length: " . strlen($appHtml) . " chars)\n";

    echo "\n>>> ALL 5 EMAIL NOTIFICATION TEMPLATES COMPILED & RENDERED WITH ZERO ERRORS! <<<\n";
} catch (\Throwable $e) {
    echo "\n[FAIL] Error occurred during rendering: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
