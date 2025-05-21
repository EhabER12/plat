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

// SQL to create categories table
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

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table categories created successfully<br>";
    
    // Insert some default categories
    $categories = [
        ['Web Development', 'web-development', 'Learn web development with our courses', 'web.jpg', NULL],
        ['Mobile Development', 'mobile-development', 'Mobile app development courses', 'mobile.jpg', NULL],
        ['Data Science', 'data-science', 'Data science and analytics courses', 'data.jpg', NULL],
        ['Design', 'design', 'Design courses for all levels', 'design.jpg', NULL],
        ['Business', 'business', 'Business courses for professionals', 'business.jpg', NULL],
        ['Marketing', 'marketing', 'Digital marketing courses', 'marketing.jpg', NULL],
        ['Photography', 'photography', 'Photography courses from beginner to advanced', 'photo.jpg', NULL],
        ['Music', 'music', 'Music theory and instrument lessons', 'music.jpg', NULL]
    ];
    
    $timestamp = date("Y-m-d H:i:s");
    $insertCount = 0;
    
    foreach ($categories as $category) {
        $insertSql = "INSERT INTO `categories` 
            (`name`, `slug`, `description`, `image`, `parent_id`, `created_at`, `updated_at`) VALUES 
            (?, ?, ?, ?, ?, ?, ?)";
            
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sssssss", $category[0], $category[1], $category[2], $category[3], $category[4], $timestamp, $timestamp);
        
        if ($stmt->execute()) {
            $insertCount++;
        } else {
            echo "Error inserting category: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    }
    
    echo "$insertCount default categories inserted successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Now update the references in the courses table to match our new category_id primary key
$alterCoursesTableSql = "ALTER TABLE `courses` 
                        CHANGE COLUMN `category_id` `category_id` BIGINT UNSIGNED NOT NULL,
                        ADD CONSTRAINT `courses_category_id_foreign`
                        FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
                        ON DELETE CASCADE";

if ($conn->query($alterCoursesTableSql) === TRUE) {
    echo "Courses table updated with foreign key constraint to categories<br>";
} else {
    echo "Error updating courses table: " . $conn->error . "<br>";
}

$conn->close(); 