<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$passwordsToTest = [
    'password123', 'worker123', 'client123', 'admin123', 'staff123',
    '123456', '12345678', 'password', 'kean123', 'vanhay123', 'kean2026', 'testing123'
];

$users = User::all();
echo "=== USER LIST & PASSWORD CHECK ===\n";
foreach ($users as $u) {
    $matched = 'UNKNOWN';
    foreach ($passwordsToTest as $p) {
        if (Hash::check($p, $u->password_hash)) {
            $matched = $p;
            break;
        }
    }
    echo "User: {$u->name} | Role: {$u->role} | Password: {$matched} | Hash: " . substr($u->password_hash, 0, 15) . "...\n";
}
