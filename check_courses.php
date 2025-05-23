<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the courses table
$columns = DB::select('SHOW COLUMNS FROM courses');
echo "Courses Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

// Show a sample row
$sample = DB::table('courses')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
}

// Count total rows
$count = DB::table('courses')->count();
echo "\nTotal courses: {$count}\n"; 