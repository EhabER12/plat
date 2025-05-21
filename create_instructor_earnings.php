<?php

// Database connection settings - adjust these to match your .env settings
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

// SQL to create table
$sql = "CREATE TABLE IF NOT EXISTS `instructor_earnings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `instructor_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `description` VARCHAR(255) NULL DEFAULT NULL,
    `type` ENUM('course_purchase', 'subscription', 'other') NOT NULL DEFAULT 'course_purchase',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table instructor_earnings created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close(); 