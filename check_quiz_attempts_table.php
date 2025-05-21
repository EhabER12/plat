<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if quiz_attempts table exists
if (Schema::hasTable('quiz_attempts')) {
    echo "\nQuiz_attempts table exists\n";
    
    // Get the columns of the quiz_attempts table
    $columns = Schema::getColumnListing('quiz_attempts');
    echo "Columns in quiz_attempts table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Check if attempt_id column exists
    if (in_array('attempt_id', $columns)) {
        echo "attempt_id column exists\n";
    } else {
        echo "attempt_id column does NOT exist\n";
    }
    
    // Count records in the table
    $count = DB::table('quiz_attempts')->count();
    echo "Number of records in quiz_attempts table: $count\n";
    
    // Try to get a record with attempt_id = 1
    $attempt = DB::table('quiz_attempts')->where('attempt_id', 1)->first();
    if ($attempt) {
        echo "Found record with attempt_id = 1\n";
        echo "Quiz ID: " . $attempt->quiz_id . "\n";
        echo "User ID: " . $attempt->user_id . "\n";
    } else {
        echo "No record found with attempt_id = 1\n";
    }
} else {
    echo "\nQuiz_attempts table does NOT exist\n";
}
