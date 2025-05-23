<?php

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yousef_db2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database successfully<br>";

// Check if admin_notifications table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
if ($tableCheck->num_rows == 0) {
    // Create admin_notifications table
    $createTableSql = "CREATE TABLE IF NOT EXISTS `admin_notifications` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `type` VARCHAR(50) NOT NULL DEFAULT 'general',
        `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `related_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `related_type` VARCHAR(255) NULL DEFAULT NULL,
        `content` TEXT NOT NULL,
        `data` TEXT NULL DEFAULT NULL,
        `is_read` BOOLEAN NOT NULL DEFAULT FALSE,
        `read_at` TIMESTAMP NULL DEFAULT NULL,
        `severity` TINYINT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    if ($conn->query($createTableSql) === TRUE) {
        echo "admin_notifications table created successfully<br>";
    } else {
        echo "Error creating admin_notifications table: " . $conn->error . "<br>";
        die();
    }
} else {
    echo "admin_notifications table exists<br>";
    
    // Check if type column exists
    $typeColumnExists = false;
    $result = $conn->query("DESCRIBE admin_notifications");
    
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] == 'type') {
            $typeColumnExists = true;
            break;
        }
    }
    
    if (!$typeColumnExists) {
        // Add type column
        if ($conn->query("ALTER TABLE admin_notifications ADD COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'general' AFTER `id`") === TRUE) {
            echo "Added 'type' column to admin_notifications table<br>";
        } else {
            echo "Error adding 'type' column: " . $conn->error . "<br>";
        }
    } else {
        echo "'type' column already exists in admin_notifications table<br>";
    }
}

// Add sample data only if no records exist
$checkData = $conn->query("SELECT COUNT(*) as count FROM admin_notifications");
$row = $checkData->fetch_assoc();

if ($row['count'] == 0) {
    // Add sample notification data
    $timestamp = date("Y-m-d H:i:s");
    
    // Insert general notification
    $stmt = $conn->prepare("INSERT INTO admin_notifications 
                (type, content, is_read, severity, created_at, updated_at) 
                VALUES ('general', 'Welcome to the admin dashboard', 0, 0, ?, ?)");
    $stmt->bind_param("ss", $timestamp, $timestamp);
    
    if ($stmt->execute()) {
        echo "Inserted general notification<br>";
    } else {
        echo "Error inserting general notification: " . $stmt->error . "<br>";
    }
    $stmt->close();
    
    // Insert flagged_content notification
    $stmt = $conn->prepare("INSERT INTO admin_notifications 
                (type, user_id, related_id, related_type, content, data, is_read, severity, created_at, updated_at) 
                VALUES ('flagged_content', 1, 1, 'Course', 'A course has been flagged for review', ?, 0, 2, ?, ?)");
    $data = '{"course_id": 1}';
    $stmt->bind_param("sss", $data, $timestamp, $timestamp);
    
    if ($stmt->execute()) {
        echo "Inserted flagged_content notification<br>";
    } else {
        echo "Error inserting flagged_content notification: " . $stmt->error . "<br>";
    }
    $stmt->close();
    
    // Insert system_alert notification
    $stmt = $conn->prepare("INSERT INTO admin_notifications 
                (type, content, is_read, severity, created_at, updated_at) 
                VALUES ('system_alert', 'System maintenance scheduled for tonight', 0, 1, ?, ?)");
    $stmt->bind_param("ss", $timestamp, $timestamp);
    
    if ($stmt->execute()) {
        echo "Inserted system_alert notification<br>";
    } else {
        echo "Error inserting system_alert notification: " . $stmt->error . "<br>";
    }
    $stmt->close();
    
    echo "Sample notifications inserted successfully<br>";
}

// Verify changes
$result = $conn->query("DESCRIBE admin_notifications");
echo "Updated admin_notifications table structure:<br>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

echo "Admin notifications table fixed successfully!";

$conn->close(); 