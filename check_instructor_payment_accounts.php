<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the instructor_payment_accounts table
$columns = DB::select('SHOW COLUMNS FROM instructor_payment_accounts');
echo "Instructor Payment Accounts Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample row
$sample = DB::table('instructor_payment_accounts')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
}

// Count total rows
$count = DB::table('instructor_payment_accounts')->count();
echo "\nTotal records: {$count}\n";

// Run the query that was failing before
echo "\nTesting the previously failing query:\n";
$exists = DB::table('instructor_payment_accounts')
    ->where('instructor_id', 4)
    ->where('instructor_id', '!=', null)
    ->where('is_active', 1)
    ->exists();

echo "Does instructor_id = 4 have an active payment account? " . ($exists ? "Yes" : "No") . "\n"; 