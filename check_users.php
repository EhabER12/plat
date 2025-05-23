<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the users table
$columns = DB::select('SHOW COLUMNS FROM users');
echo "Users Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample instructor user
$instructor = DB::table('users')->where('user_id', 4)->first();
if ($instructor) {
    echo "\nSample Instructor (ID=4):\n";
    print_r($instructor);
} 