<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if quizzes table exists
if (Schema::hasTable('quizzes')) {
    echo "Quizzes table exists\n";
    
    // Get the columns of the quizzes table
    $columns = Schema::getColumnListing('quizzes');
    echo "Columns in quizzes table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Check if quiz_id column exists
    if (in_array('quiz_id', $columns)) {
        echo "quiz_id column exists\n";
    } else {
        echo "quiz_id column does NOT exist\n";
    }
    
    // Check if id column exists
    if (in_array('id', $columns)) {
        echo "id column exists\n";
    } else {
        echo "id column does NOT exist\n";
    }
    
    // Check the primary key
    try {
        $primaryKey = DB::select("SHOW KEYS FROM quizzes WHERE Key_name = 'PRIMARY'");
        echo "Primary key column: " . $primaryKey[0]->Column_name . "\n";
    } catch (Exception $e) {
        echo "Error getting primary key: " . $e->getMessage() . "\n";
    }
    
    // Count records in the table
    $count = DB::table('quizzes')->count();
    echo "Number of records in quizzes table: $count\n";
    
    // Show a sample record
    if ($count > 0) {
        $sample = DB::table('quizzes')->first();
        echo "Sample record:\n";
        foreach ((array)$sample as $key => $value) {
            echo "- $key: $value\n";
        }
    }
} else {
    echo "Quizzes table does NOT exist\n";
}

// Check if quiz_attempts table exists
if (Schema::hasTable('quiz_attempts')) {
    echo "\nQuiz_attempts table exists\n";
    
    // Get the columns of the quiz_attempts table
    $columns = Schema::getColumnListing('quiz_attempts');
    echo "Columns in quiz_attempts table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Check if quiz_id column exists
    if (in_array('quiz_id', $columns)) {
        echo "quiz_id column exists\n";
    } else {
        echo "quiz_id column does NOT exist\n";
    }
    
    // Count records in the table
    $count = DB::table('quiz_attempts')->count();
    echo "Number of records in quiz_attempts table: $count\n";
}

// Check if courses table exists
if (Schema::hasTable('courses')) {
    echo "\nCourses table exists\n";
    
    // Get the columns of the courses table
    $columns = Schema::getColumnListing('courses');
    echo "Columns in courses table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Check if course_id column exists
    if (in_array('course_id', $columns)) {
        echo "course_id column exists\n";
    } else {
        echo "course_id column does NOT exist\n";
    }
    
    // Count records in the table
    $count = DB::table('courses')->count();
    echo "Number of records in courses table: $count\n";
}

// Try to run the query that's causing the error
echo "\nTrying to run the problematic query:\n";
try {
    $result = DB::select("SELECT * FROM `quizzes` WHERE EXISTS (SELECT * FROM `courses` WHERE `quizzes`.`course_id` = `courses`.`course_id` AND `instructor_id` = 4) AND `quizzes`.`quiz_id` = 1 LIMIT 1");
    echo "Query executed successfully. Result count: " . count($result) . "\n";
    if (count($result) > 0) {
        echo "Result data:\n";
        foreach ((array)$result[0] as $key => $value) {
            echo "- $key: $value\n";
        }
    }
} catch (Exception $e) {
    echo "Error executing query: " . $e->getMessage() . "\n";
}
