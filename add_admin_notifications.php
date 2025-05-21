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

// Add sample notifications
$notifications = [
    [
        'type' => 'general',
        'title' => 'Welcome to Admin Dashboard',
        'message' => 'Welcome to the admin dashboard. You can manage users, courses, and more from here.',
        'severity' => 'low'
    ],
    [
        'type' => 'flagged_content',
        'title' => 'Content flagged for review',
        'message' => 'A course has been flagged for inappropriate content. Please review it.',
        'severity' => 'high'
    ],
    [
        'type' => 'system_alert',
        'title' => 'System Maintenance',
        'message' => 'System maintenance scheduled for tonight at 2 AM. The site may be offline for 30 minutes.',
        'severity' => 'medium'
    ]
];

$timestamp = date("Y-m-d H:i:s");
$insertCount = 0;

foreach ($notifications as $notification) {
    $sql = "INSERT INTO admin_notifications (type, title, message, is_read, severity, created_at) 
            VALUES (?, ?, ?, 0, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", 
                    $notification['type'], 
                    $notification['title'], 
                    $notification['message'], 
                    $notification['severity'],
                    $timestamp);
    
    if ($stmt->execute()) {
        $insertCount++;
    } else {
        echo "Error inserting notification: " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

echo "$insertCount sample notifications inserted successfully<br>";

// Verify data
$result = $conn->query("SELECT * FROM admin_notifications");
echo "Admin notifications in database:<br>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

echo "Admin notifications added successfully!";

$conn->close(); 