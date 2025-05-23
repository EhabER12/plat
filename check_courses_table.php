<?php
// Check courses table structure

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

// Check if courses table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'courses'");
if ($tableCheck->num_rows == 0) {
    echo "Courses table does not exist<br>";
} else {
    echo "Courses table exists<br>";
    
    // Get table structure
    $descResult = $conn->query("DESCRIBE courses");
    echo "<h3>Columns in courses table:</h3>";
    while ($row = $descResult->fetch_assoc()) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . ", Key: " . $row['Key'] . ", Default: " . $row['Default'] . "<br>";
    }
    
    // Check if any rows exist
    $countResult = $conn->query("SELECT COUNT(*) as count FROM courses");
    $countRow = $countResult->fetch_assoc();
    echo "<br>Total rows in courses table: " . $countRow['count'] . "<br>";
}

$conn->close(); 