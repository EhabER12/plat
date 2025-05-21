<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Quiz;
use App\Models\Course;

echo "Testing Quiz model after fix...\n\n";

// Check if quizzes table exists
if (Schema::hasTable('quizzes')) {
    echo "Quizzes table exists\n";
    
    // Get the columns of the quizzes table
    $columns = Schema::getColumnListing('quizzes');
    echo "Columns in quizzes table:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Count records in the table
    $count = DB::table('quizzes')->count();
    echo "Number of records in quizzes table: $count\n";
    
    if ($count > 0) {
        // Try to get a quiz using the model
        $quiz = Quiz::first();
        if ($quiz) {
            echo "\nSuccessfully retrieved a quiz using the model:\n";
            echo "- ID: {$quiz->id}\n";
            echo "- Title: {$quiz->title}\n";
            echo "- Course ID: {$quiz->course_id}\n";
            
            // Try to get the course
            $course = $quiz->course;
            if ($course) {
                echo "- Course Title: {$course->title}\n";
            } else {
                echo "Failed to retrieve the course for this quiz.\n";
            }
            
            // Try to get the attempts
            $attempts = $quiz->attempts;
            echo "- Number of attempts: {$attempts->count()}\n";
        } else {
            echo "Failed to retrieve a quiz using the model.\n";
        }
        
        // Try to run the query that was causing the error
        echo "\nTrying to run the previously problematic query:\n";
        try {
            $result = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', 4);
            })->where('id', 1)->first();
            
            if ($result) {
                echo "Query executed successfully and returned a result.\n";
                echo "- ID: {$result->id}\n";
                echo "- Title: {$result->title}\n";
            } else {
                echo "Query executed successfully but returned no results.\n";
            }
        } catch (Exception $e) {
            echo "Error executing query: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Quizzes table does NOT exist\n";
}
