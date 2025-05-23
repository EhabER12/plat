<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

// Verify the created_by column was added
$columns = DB::select('SHOW COLUMNS FROM coupons');
echo "Coupons Table Structure (after migration):\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Add a sample coupon with created_by = 4 (instructor)
$sampleCode = 'INST' . rand(1000, 9999);
$inserted = DB::table('coupons')->insert([
    'code' => $sampleCode,
    'type' => 'percentage',
    'value' => 15.00,
    'max_uses' => 50,
    'used_count' => 0,
    'expires_at' => Carbon::now()->addMonths(3),
    'status' => 1,
    'created_by' => 4,
    'created_at' => Carbon::now(),
    'updated_at' => Carbon::now()
]);

echo "\nSample coupon created for instructor_id=4: " . ($inserted ? "Success" : "Failed") . "\n";

// Test the query that was failing
echo "\nTesting the previously failing query:\n";
$count = DB::table('coupons')
    ->where('created_by', 4)
    ->count();

echo "Count of coupons created by instructor_id=4: " . $count . "\n";

// Show all coupons
$coupons = DB::table('coupons')->get();
echo "\nAll coupons:\n";
foreach ($coupons as $coupon) {
    echo "ID: {$coupon->id}, Code: {$coupon->code}, Created by: {$coupon->created_by}\n";
} 