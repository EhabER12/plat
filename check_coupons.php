<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the coupons table
$columns = DB::select('SHOW COLUMNS FROM coupons');
echo "Coupons Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample row
$sample = DB::table('coupons')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
}

// Count total rows
$count = DB::table('coupons')->count();
echo "\nTotal coupons: {$count}\n"; 