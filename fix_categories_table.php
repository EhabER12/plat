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

// Check if categories table has id column
$result = $conn->query("DESCRIBE categories");
$hasId = false;
$hasCategoryId = false;

while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'id') {
        $hasId = true;
    } else if ($row['Field'] == 'category_id') {
        $hasCategoryId = true;
    }
}

if ($hasId && !$hasCategoryId) {
    // First, let's drop any foreign key constraints on courses.category_id
    try {
        $conn->query("ALTER TABLE courses DROP FOREIGN KEY IF EXISTS courses_category_id_foreign");
        echo "Dropped foreign key on courses table<br>";
    } catch (Exception $e) {
        echo "No foreign key to drop or error: " . $e->getMessage() . "<br>";
    }
    
    // Now rename id to category_id in categories table
    $conn->query("ALTER TABLE categories CHANGE COLUMN id category_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
    echo "Renamed id to category_id in categories table<br>";
}

// Add some default categories if none exist
$countResult = $conn->query("SELECT COUNT(*) as count FROM categories");
$count = $countResult->fetch_assoc()['count'];

if ($count == 0) {
    $timestamp = date("Y-m-d H:i:s");
    
    $categories = [
        ['Web Development', 'web-development', 'Learn web development with our courses', 'web.jpg', NULL],
        ['Mobile Development', 'mobile-development', 'Mobile app development courses', 'mobile.jpg', NULL],
        ['Data Science', 'data-science', 'Data science and analytics courses', 'data.jpg', NULL],
        ['Design', 'design', 'Design courses for all levels', 'design.jpg', NULL]
    ];
    
    $insertCount = 0;
    foreach ($categories as $category) {
        $insertSql = "INSERT INTO categories 
                     (name, slug, description, image, parent_id, status, created_at, updated_at) VALUES 
                     (?, ?, ?, ?, ?, 1, ?, ?)";
                     
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sssssss", 
                         $category[0], $category[1], $category[2], 
                         $category[3], $category[4], $timestamp, $timestamp);
        
        if ($stmt->execute()) {
            $insertCount++;
        } else {
            echo "Error inserting category: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    }
    
    echo "Inserted $insertCount default categories<br>";
}

// Now add foreign key in courses table if it exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'courses'");
if ($tableCheck->num_rows > 0) {
    try {
        // Update or create category_id column in courses table
        $conn->query("ALTER TABLE courses CHANGE COLUMN category_id category_id BIGINT UNSIGNED NOT NULL");
        
        // Add foreign key constraint
        $conn->query("ALTER TABLE courses ADD CONSTRAINT courses_category_id_foreign 
                     FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE");
        
        echo "Added foreign key constraint in courses table<br>";
    } catch (Exception $e) {
        echo "Error updating courses table: " . $e->getMessage() . "<br>";
    }
}

echo "Categories table fixed successfully!";

$conn->close(); 