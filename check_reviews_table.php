<?php

// Database connection parameters
$host = 'localhost';
$dbname = 'yousef_db2';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to database: $dbname\n";
    
    // Check if course_reviews table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'course_reviews'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "The table 'course_reviews' exists.\n";
        
        // Show the structure of the table
        $stmt = $conn->prepare("DESCRIBE course_reviews");
        $stmt->execute();
        $table_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure:\n";
        foreach ($table_structure as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check if there are any records
        $stmt = $conn->prepare("SELECT COUNT(*) FROM course_reviews");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        echo "Records count: $count\n";
    } else {
        echo "The table 'course_reviews' does not exist.\n";
        
        // Create the table if it doesn't exist
        echo "Creating 'course_reviews' table...\n";
        
        $sql = "CREATE TABLE course_reviews (
            review_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            course_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            rating INT NOT NULL,
            review TEXT NULL,
            is_approved BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            UNIQUE (course_id, user_id)
        )";
        
        $conn->exec($sql);
        echo "Table 'course_reviews' created successfully.\n";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 