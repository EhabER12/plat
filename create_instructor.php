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

// Default instructor user details
$name = "Sample Instructor";
$email = "instructor@example.com";
$password = password_hash("instructor123", PASSWORD_BCRYPT);
$role = "instructor";
$created_at = date("Y-m-d H:i:s");
$updated_at = date("Y-m-d H:i:s");

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $instructorId = $row['id'];
    echo "Instructor user already exists with ID: $instructorId.";
} else {
    // Insert instructor user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, detailed_description, website, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $detailed_description = "This is a sample instructor with extensive experience in teaching.";
    $website = "https://example.com/instructor";
    $stmt->bind_param("ssssssss", $name, $email, $password, $role, $detailed_description, $website, $created_at, $updated_at);
    
    if ($stmt->execute()) {
        $instructorId = $conn->insert_id;
        echo "Instructor user created successfully with ID: $instructorId<br>";
        
        // Add user role
        $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role, created_at, updated_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $instructorId, $role, $created_at, $updated_at);
        
        if ($stmt->execute()) {
            echo "Instructor role assigned successfully.<br>";
        } else {
            echo "Error assigning instructor role: " . $stmt->error . "<br>";
        }
    } else {
        echo "Error creating instructor user: " . $stmt->error . "<br>";
        exit;
    }
}

// Create sample earnings
$earnings = [
    [
        'amount' => 100.00,
        'type' => 'course_purchase',
        'description' => 'Course purchase: Introduction to PHP'
    ],
    [
        'amount' => 75.50,
        'type' => 'course_purchase',
        'description' => 'Course purchase: Advanced JavaScript'
    ],
    [
        'amount' => 50.25,
        'type' => 'subscription',
        'description' => 'Monthly subscription revenue'
    ],
    [
        'amount' => 120.00,
        'type' => 'other',
        'description' => 'Bonus payment for featured course'
    ]
];

// Add earnings
$stmt = $conn->prepare("INSERT INTO instructor_earnings (instructor_id, amount, description, type, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");

$earningsAdded = 0;
foreach ($earnings as $earning) {
    $stmt->bind_param("idssss", $instructorId, $earning['amount'], $earning['description'], $earning['type'], $created_at, $updated_at);
    
    if ($stmt->execute()) {
        $earningsAdded++;
    } else {
        echo "Error adding earning: " . $stmt->error . "<br>";
    }
}

echo "$earningsAdded sample earnings records added for instructor ID: $instructorId";

$stmt->close();
$conn->close(); 