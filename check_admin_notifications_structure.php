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
    echo "admin_notifications table does not exist<br>";
} else {
    echo "admin_notifications table exists<br>";
    
    // Show table structure
    $result = $conn->query("DESCRIBE admin_notifications");
    echo "admin_notifications table structure:<br>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
    
    // Show sample data
    $result = $conn->query("SELECT * FROM admin_notifications LIMIT 5");
    echo "Sample data:<br>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

$conn->close(); 