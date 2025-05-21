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

// SQL to create website_appearances table
$sql = "CREATE TABLE IF NOT EXISTS `website_appearances` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section` VARCHAR(255) NULL DEFAULT NULL,
    `primary_color` VARCHAR(255) NOT NULL DEFAULT '#4A6CF7',
    `secondary_color` VARCHAR(255) NOT NULL DEFAULT '#F9C254',
    `logo_path` VARCHAR(255) NULL DEFAULT NULL,
    `favicon_path` VARCHAR(255) NULL DEFAULT NULL,
    `banner_image` VARCHAR(255) NULL DEFAULT NULL,
    `footer_logo` VARCHAR(255) NULL DEFAULT NULL,
    `footer_text` TEXT NULL DEFAULT NULL,
    `custom_css` TEXT NULL DEFAULT NULL,
    `custom_js` TEXT NULL DEFAULT NULL,
    `social_links` JSON NULL DEFAULT NULL,
    `homepage_sections` JSON NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table website_appearances created successfully<br>";
    
    // Insert default footer data
    $footerInsertSql = "INSERT INTO `website_appearances` 
        (`section`, `footer_text`, `social_links`, `created_at`, `updated_at`) VALUES 
        ('footer', 'Copyright © 2023 All Rights Reserved.', '{\"facebook\":\"https://facebook.com\",\"twitter\":\"https://twitter.com\",\"instagram\":\"https://instagram.com\",\"linkedin\":\"https://linkedin.com\"}', NOW(), NOW())";
        
    if ($conn->query($footerInsertSql) === TRUE) {
        echo "Default footer data inserted successfully<br>";
    } else {
        echo "Error inserting footer data: " . $conn->error . "<br>";
    }
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close(); 