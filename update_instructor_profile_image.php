<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update instructor profile image
$updated = DB::table('users')
    ->where('user_id', 4)
    ->update([
        'profile_image' => '/profile_images/1747308990_1746453195_birth_suit2.jpg',
        'updated_at' => now()
    ]);

echo "Instructor profile image updated: " . ($updated ? "Success" : "Failed") . "\n";

// Verify the update
$instructor = DB::table('users')->where('user_id', 4)->first();
if ($instructor) {
    echo "\nUpdated Instructor (ID=4):\n";
    print_r($instructor);
} 