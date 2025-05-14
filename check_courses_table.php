<?php
// Check courses table structure

try {
    $host = '127.0.0.1';
    $dbname = 'yousef_db2';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking courses table structure:\n";
    echo "--------------------------------\n";
    
    // Check if courses table exists
    $stmt = $pdo->query("DESCRIBE courses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Courses table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")" . 
             ($column['Key'] ? " [" . $column['Key'] . "]" : "") . 
             ($column['Default'] ? " Default: " . $column['Default'] : "") . "\n";
    }
    
    // Count records in courses table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "\nTotal courses in database: " . $count . "\n";
    
    if ($count > 0) {
        // Show sample courses
        $stmt = $pdo->query("SELECT * FROM courses LIMIT 5");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nSample courses:\n";
        foreach ($courses as $course) {
            echo "- ID: " . $course['course_id'] . ", Title: " . $course['title'] . 
                 ", Status: " . $course['approval_status'] . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 