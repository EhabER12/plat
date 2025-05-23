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

// Default admin user details
$name = "Admin User";
$email = "admin@example.com";
$password = password_hash("admin123", PASSWORD_BCRYPT);
$role = "admin";
$created_at = date("Y-m-d H:i:s");
$updated_at = date("Y-m-d H:i:s");

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists.";
} else {
    // Insert admin user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $role, $created_at, $updated_at);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        echo "Admin user created successfully with ID: $userId";
        
        // Add user role
        $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role, created_at, updated_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $role, $created_at, $updated_at);
        
        if ($stmt->execute()) {
            echo "<br>Admin role assigned successfully.";
        } else {
            echo "<br>Error assigning admin role: " . $stmt->error;
        }
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
}

$stmt->close();
$conn->close(); 