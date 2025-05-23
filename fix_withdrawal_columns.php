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

// Check table structure
$result = $conn->query("DESCRIBE withdrawals");

// Check if columns need to be renamed
$hasUserIdColumn = false;
$hasWithdrawalIdColumn = false;
$hasInstructorIdColumn = false;
$hasIdColumn = false;

while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'user_id') {
        $hasUserIdColumn = true;
    }
    if ($row['Field'] == 'withdrawal_id') {
        $hasWithdrawalIdColumn = true;
    }
    if ($row['Field'] == 'instructor_id') {
        $hasInstructorIdColumn = true;
    }
    if ($row['Field'] == 'id') {
        $hasIdColumn = true;
    }
}

// First, rename the primary key from id to withdrawal_id if needed
if ($hasIdColumn && !$hasWithdrawalIdColumn) {
    $conn->query("ALTER TABLE withdrawals CHANGE id withdrawal_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
    echo "Renamed id to withdrawal_id<br>";
}

// Then, rename user_id to instructor_id if needed
if ($hasUserIdColumn && !$hasInstructorIdColumn) {
    $conn->query("ALTER TABLE withdrawals CHANGE user_id instructor_id BIGINT UNSIGNED NOT NULL");
    echo "Renamed user_id to instructor_id<br>";
}

// Make sure the instructor_id column exists, add it if it doesn't
if (!$hasUserIdColumn && !$hasInstructorIdColumn) {
    $conn->query("ALTER TABLE withdrawals ADD COLUMN instructor_id BIGINT UNSIGNED NOT NULL AFTER withdrawal_id");
    echo "Added instructor_id column<br>";
}

// Now ensure the sample data has valid instructor_id values
// Check if users table exists
$usersCheck = $conn->query("SHOW TABLES LIKE 'users'");
if ($usersCheck->num_rows > 0) {
    // First check the structure of the users table to determine its primary key
    $usersPrimaryKey = 'id'; // Default assumption
    $userTableCheck = $conn->query("DESCRIBE users");
    
    while ($col = $userTableCheck->fetch_assoc()) {
        if ($col['Key'] == 'PRI') {
            $usersPrimaryKey = $col['Field'];
            break;
        }
    }
    
    echo "Users table primary key is: " . $usersPrimaryKey . "<br>";
    
    // Now check if there are any users with instructor role
    $roleColumnExists = false;
    $userTableCheck->data_seek(0); // Reset the pointer
    while ($col = $userTableCheck->fetch_assoc()) {
        if ($col['Field'] == 'role') {
            $roleColumnExists = true;
            break;
        }
    }
    
    if ($roleColumnExists) {
        $instructorCheckQuery = "SELECT $usersPrimaryKey FROM users WHERE role = 'instructor' LIMIT 1";
    } else {
        // Fallback - just get any user
        $instructorCheckQuery = "SELECT $usersPrimaryKey FROM users LIMIT 1";
    }
    
    $instructorCheck = $conn->query($instructorCheckQuery);
    
    if ($instructorCheck->num_rows > 0) {
        $instructor = $instructorCheck->fetch_assoc();
        $instructorId = $instructor[$usersPrimaryKey];
        
        // Update existing withdrawals to have valid instructor_id
        $conn->query("UPDATE withdrawals SET instructor_id = $instructorId WHERE instructor_id = 0 OR instructor_id IS NULL");
        echo "Updated withdrawal records with valid instructor_id ($instructorId)<br>";
    } else {
        // Create a sample instructor user if none exists
        if ($roleColumnExists) {
            $createUserSql = "INSERT IGNORE INTO users (name, email, password, role, created_at, updated_at) 
                            VALUES ('Sample Instructor', 'instructor@example.com', '".password_hash('password', PASSWORD_DEFAULT)."', 'instructor', NOW(), NOW())";
        } else {
            $createUserSql = "INSERT IGNORE INTO users (name, email, password, created_at, updated_at) 
                            VALUES ('Sample Instructor', 'instructor@example.com', '".password_hash('password', PASSWORD_DEFAULT)."', NOW(), NOW())";
        }
        
        $conn->query($createUserSql);
        $instructorId = $conn->insert_id;
        
        $conn->query("UPDATE withdrawals SET instructor_id = $instructorId WHERE instructor_id = 0 OR instructor_id IS NULL");
        echo "Created sample instructor (ID: $instructorId) and assigned to withdrawal records<br>";
    }
} else {
    echo "Warning: users table doesn't exist. Cannot assign valid instructor_id values.<br>";
}

// Verify changes
$result = $conn->query("DESCRIBE withdrawals");
echo "Updated withdrawals table structure:<br>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

echo "Withdrawals table fixed successfully!";

$conn->close(); 