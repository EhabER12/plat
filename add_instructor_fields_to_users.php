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
    
    // Add detailed_description column if it doesn't exist
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'detailed_description'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "Adding 'detailed_description' column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN detailed_description TEXT NULL AFTER bio";
        $conn->exec($sql);
        echo "Added 'detailed_description' column successfully.\n";
    } else {
        echo "The 'detailed_description' column already exists in the users table.\n";
    }
    
    // Add website column if it doesn't exist
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'website'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "Adding 'website' column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN website VARCHAR(255) NULL AFTER detailed_description";
        $conn->exec($sql);
        echo "Added 'website' column successfully.\n";
    } else {
        echo "The 'website' column already exists in the users table.\n";
    }
    
    // Add linkedin_profile column if it doesn't exist
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'linkedin_profile'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "Adding 'linkedin_profile' column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN linkedin_profile VARCHAR(255) NULL AFTER website";
        $conn->exec($sql);
        echo "Added 'linkedin_profile' column successfully.\n";
    } else {
        echo "The 'linkedin_profile' column already exists in the users table.\n";
    }
    
    // Add twitter_profile column if it doesn't exist
    $stmt = $conn->prepare("SHOW COLUMNS FROM users LIKE 'twitter_profile'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "Adding 'twitter_profile' column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN twitter_profile VARCHAR(255) NULL AFTER linkedin_profile";
        $conn->exec($sql);
        echo "Added 'twitter_profile' column successfully.\n";
    } else {
        echo "The 'twitter_profile' column already exists in the users table.\n";
    }
    
    // Check table structure after changes
    echo "\nUpdated users table structure:\n";
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