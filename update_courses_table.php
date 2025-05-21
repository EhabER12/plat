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

// Create a default category if none exists
$result = $conn->query("SELECT COUNT(*) as count FROM categories");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $timestamp = date("Y-m-d H:i:s");
    $defaultCategory = "INSERT INTO categories (name, slug, description, status, created_at, updated_at) 
                        VALUES ('Uncategorized', 'uncategorized', 'Default category', 1, '$timestamp', '$timestamp')";
    
    if ($conn->query($defaultCategory) === TRUE) {
        echo "Default category created<br>";
    } else {
        echo "Error creating default category: " . $conn->error . "<br>";
        die();
    }
}

// Create sample course data
echo "Creating sample course data...<br>";

// First, let's insert a sample instructor if needed
$conn->query("INSERT IGNORE INTO users (id, name, email, password, role, created_at, updated_at) 
              VALUES (10, 'Sample Instructor', 'instructor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', NOW(), NOW())");

// Get a category ID to use
$catResult = $conn->query("SELECT category_id FROM categories LIMIT 1");
$category = $catResult->fetch_assoc();
$categoryId = $category['category_id'];

// Get an instructor ID
$instrResult = $conn->query("SELECT id FROM users WHERE role='instructor' LIMIT 1");
$instructor = $instrResult->fetch_assoc();
$instructorId = $instructor['id'];

// Check if courses table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'courses'")->num_rows > 0;

if (!$tableExists) {
    // Create the courses table with proper structure
    $createCoursesTable = "CREATE TABLE IF NOT EXISTS `courses` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `description` TEXT NOT NULL,
        `instructor_id` BIGINT UNSIGNED NOT NULL,
        `category_id` BIGINT UNSIGNED NOT NULL,
        `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `discount_price` DECIMAL(10,2) NULL DEFAULT NULL,
        `image` VARCHAR(255) NULL DEFAULT NULL,
        `promotional_video` VARCHAR(255) NULL DEFAULT NULL,
        `language` VARCHAR(50) NOT NULL DEFAULT 'English',
        `level` ENUM('beginner', 'intermediate', 'advanced', 'all-levels') NOT NULL DEFAULT 'all-levels',
        `duration` INT NULL DEFAULT NULL,
        `status` ENUM('draft', 'pending', 'published', 'rejected') NOT NULL DEFAULT 'draft',
        `approval_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        `certificate_available` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
        CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    if ($conn->query($createCoursesTable) === TRUE) {
        echo "Courses table created successfully<br>";
    } else {
        echo "Error creating courses table: " . $conn->error . "<br>";
        die();
    }
} else {
    // Alter the existing courses table
    try {
        // First remove any existing foreign keys
        $conn->query("ALTER TABLE courses DROP FOREIGN KEY IF EXISTS courses_category_id_foreign");
        $conn->query("ALTER TABLE courses DROP FOREIGN KEY IF EXISTS courses_instructor_id_foreign");
        
        // Ensure the columns have the right type
        $conn->query("ALTER TABLE courses MODIFY category_id BIGINT UNSIGNED NOT NULL");
        $conn->query("ALTER TABLE courses MODIFY instructor_id BIGINT UNSIGNED NOT NULL");
        
        // Add the foreign keys
        $conn->query("ALTER TABLE courses ADD CONSTRAINT courses_category_id_foreign 
                      FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE");
        $conn->query("ALTER TABLE courses ADD CONSTRAINT courses_instructor_id_foreign 
                      FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE");
        
        echo "Courses table updated successfully<br>";
    } catch (Exception $e) {
        echo "Error updating courses table: " . $e->getMessage() . "<br>";
        // Continue anyway
    }
}

// Add some sample courses
$timestamp = date("Y-m-d H:i:s");
$sampleCourses = [
    [
        'Introduction to Web Development', 
        'intro-to-web-dev',
        'Learn the basics of web development including HTML, CSS, and JavaScript.',
        $instructorId,
        $categoryId,
        29.99,
        19.99,
        'web-dev.jpg',
        'published',
        'approved'
    ],
    [
        'Advanced JavaScript Programming', 
        'advanced-javascript',
        'Take your JavaScript skills to the next level with this advanced course.',
        $instructorId,
        $categoryId,
        49.99,
        39.99,
        'javascript.jpg',
        'published',
        'approved'
    ],
    [
        'Python for Beginners', 
        'python-beginners',
        'Start your journey with Python programming from scratch.',
        $instructorId,
        $categoryId,
        24.99,
        NULL,
        'python.jpg',
        'published',
        'approved'
    ]
];

$insertCount = 0;
foreach ($sampleCourses as $course) {
    $insertSql = "INSERT IGNORE INTO courses 
                 (title, slug, description, instructor_id, category_id, price, discount_price, image, 
                 status, approval_status, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                 
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("sssiidbsssss", 
                      $course[0], $course[1], $course[2], $course[3], $course[4], 
                      $course[5], $course[6], $course[7], $course[8], $course[9], 
                      $timestamp, $timestamp);
    
    if ($stmt->execute()) {
        $insertCount++;
    } else {
        echo "Error inserting course: " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

echo "$insertCount sample courses inserted successfully<br>";

$conn->close(); 