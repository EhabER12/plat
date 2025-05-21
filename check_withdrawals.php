<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

// Check the structure of the withdrawals table
$columns = DB::select('SHOW COLUMNS FROM withdrawals');
echo "Withdrawals Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample row
$sample = DB::table('withdrawals')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
} else {
    echo "\nNo withdrawals found. Adding a sample withdrawal...\n";
    
    // Create a sample withdrawal for instructor_id = 4
    $created = DB::table('withdrawals')->insert([
        'instructor_id' => 4,
        'amount' => 200.00,
        'requested_at' => Carbon::now(),
        'status' => 'pending',
        'payment_method' => 'bank_transfer',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);
    
    echo "Sample withdrawal created: " . ($created ? "Success" : "Failed") . "\n";
}

// Count total rows
$count = DB::table('withdrawals')->count();
echo "\nTotal withdrawals: {$count}\n";

// Test the query that was failing
echo "\nTesting the previously failing query:\n";
$withdrawals = DB::select("SELECT * FROM withdrawals WHERE instructor_id = 4 AND instructor_id IS NOT NULL ORDER BY requested_at DESC LIMIT 5");
echo "Found " . count($withdrawals) . " withdrawals for instructor_id = 4\n";

foreach ($withdrawals as $index => $withdrawal) {
    echo "Withdrawal " . ($index + 1) . ": Amount = " . $withdrawal->amount . ", Requested at: " . $withdrawal->requested_at . "\n";
} 