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
    
    // Check if receiver_id column exists in messages table
    $stmt = $conn->prepare("SHOW COLUMNS FROM messages LIKE 'receiver_id'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add receiver_id column
        echo "Adding 'receiver_id' column to messages table...\n";
        $sql = "ALTER TABLE messages ADD COLUMN receiver_id BIGINT UNSIGNED NULL AFTER sender_id";
        $conn->exec($sql);
        
        // Add foreign key constraint
        $sql = "ALTER TABLE messages ADD CONSTRAINT messages_receiver_id_foreign FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE";
        $conn->exec($sql);
        
        echo "Added 'receiver_id' column successfully.\n";
    } else {
        echo "The 'receiver_id' column already exists in the messages table.\n";
    }
    
    // Check if is_read column exists in messages table
    $stmt = $conn->prepare("SHOW COLUMNS FROM messages LIKE 'is_read'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add is_read column
        echo "Adding 'is_read' column to messages table...\n";
        $sql = "ALTER TABLE messages ADD COLUMN is_read BOOLEAN NOT NULL DEFAULT 0 AFTER content";
        $conn->exec($sql);
        echo "Added 'is_read' column successfully.\n";
    } else {
        echo "The 'is_read' column already exists in the messages table.\n";
    }
    
    // Also check for user_id column which is used in the Message model
    $stmt = $conn->prepare("SHOW COLUMNS FROM messages LIKE 'user_id'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add user_id column
        echo "Adding 'user_id' column to messages table...\n";
        $sql = "ALTER TABLE messages ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER chat_id";
        $conn->exec($sql);
        
        // Add foreign key constraint
        $sql = "ALTER TABLE messages ADD CONSTRAINT messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE";
        $conn->exec($sql);
        
        echo "Added 'user_id' column successfully.\n";
    } else {
        echo "The 'user_id' column already exists in the messages table.\n";
    }
    
    echo "All missing columns have been added to the messages table.\n";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 