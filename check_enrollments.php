<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check the structure of the enrollments table
$columns = DB::select('SHOW COLUMNS FROM enrollments');
echo "Enrollments Table Structure:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}\n";
}

// Show a sample row
$sample = DB::table('enrollments')->first();
if ($sample) {
    echo "\nSample Row:\n";
    print_r($sample);
}

// Print out the error query to see what might be wrong
echo "\nSQL Query in Error:\n";
echo "SELECT `courses`.*, (SELECT COUNT(*) FROM `users` INNER JOIN `enrollments` ON `users`.`user_id` = `enrollments`.`student_id` WHERE `courses`.`course_id` = `enrollments`.`course_id`) AS `students_count`"; 