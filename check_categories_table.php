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

// Check if categories table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'categories'");
if ($tableCheck->num_rows == 0) {
    echo "Categories table does not exist<br>";
    
    // Let's create it
    $sql = "CREATE TABLE IF NOT EXISTS `categories` (
        `category_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `description` TEXT NULL DEFAULT NULL,
        `image` VARCHAR(255) NULL DEFAULT NULL,
        `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `status` TINYINT NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    if ($conn->query($sql) === TRUE) {
        echo "Categories table created successfully<br>";
    } else {
        echo "Error creating categories table: " . $conn->error . "<br>";
    }
} else {
    echo "Categories table exists<br>";
    
    // Get table structure
    $descResult = $conn->query("DESCRIBE categories");
    echo "<h3>Columns in categories table:</h3>";
    while ($row = $descResult->fetch_assoc()) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . ", Key: " . $row['Key'] . ", Default: " . $row['Default'] . "<br>";
    }
    
    // Check if any rows exist
    $countResult = $conn->query("SELECT COUNT(*) as count FROM categories");
    $countRow = $countResult->fetch_assoc();
    echo "<br>Total rows in categories table: " . $countRow['count'] . "<br>";
    
    // If the table exists but doesn't have category_id column, rename id to category_id
    $hasIdField = false;
    $hasCategoryIdField = false;
    
    $descResult = $conn->query("DESCRIBE categories");
    while ($row = $descResult->fetch_assoc()) {
        if ($row['Field'] == 'id' && $row['Key'] == 'PRI') {
            $hasIdField = true;
        }
        if ($row['Field'] == 'category_id' && $row['Key'] == 'PRI') {
            $hasCategoryIdField = true;
        }
    }
    
    if ($hasIdField && !$hasCategoryIdField) {
        // Table has id but not category_id - need to rename
        $conn->query("ALTER TABLE categories CHANGE COLUMN id category_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        echo "<br>Renamed id column to category_id<br>";
    }
}

$conn->close(); 