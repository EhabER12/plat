<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the instructor_earnings table
$columns = DB::select('SHOW COLUMNS FROM instructor_earnings');
echo "Instructor Earnings Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample row
$sample = DB::table('instructor_earnings')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
}

// Count total rows
$count = DB::table('instructor_earnings')->count();
echo "\nTotal records: {$count}\n";

// Add a sample earnings record for instructor_id = 4 if none exists
$instructorExists = DB::table('users')->where('user_id', 4)->exists();
if ($instructorExists) {
    $hasEarnings = DB::table('instructor_earnings')->where('instructor_id', 4)->exists();
    
    if (!$hasEarnings) {
        $inserted = DB::table('instructor_earnings')->insert([
            'instructor_id' => 4,
            'course_id' => 1, // Replace with actual course ID if needed
            'amount' => 100.00,
            'status' => 'available',
            'description' => 'Sample earnings for testing',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "Inserted sample earnings for instructor_id = 4: " . ($inserted ? "Success" : "Failed") . "\n";
    }
}

// Test the query that was failing
echo "\nTesting the failing query:\n";
$result = DB::table('instructor_earnings')
    ->where('instructor_id', 4)
    ->where('instructor_id', '!=', null)
    ->where('status', 'available')
    ->sum('amount');

echo "Sum of available earnings for instructor_id = 4: " . $result . "\n"; 