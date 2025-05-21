<?php

// Database connection parameters
$host = 'localhost';
$dbname = 'yousef_db2';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to database: $dbname\n";
    
    // Check if banner_image column exists in users table
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'banner_image'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add banner_image column
        echo "Adding 'banner_image' column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN banner_image VARCHAR(255) NULL AFTER profile_image";
        $conn->exec($sql);
        echo "Added 'banner_image' column successfully.\n";
    } else {
        echo "The 'banner_image' column already exists in the users table.\n";
    }
    
    // Check table structure
    echo "\nCurrent users table structure:\n";
    $stmt = $conn->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 