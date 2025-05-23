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

// Array of SQL create table statements
$tables = [
    // Users table - core table
    "users" => "CREATE TABLE IF NOT EXISTS `users` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('admin', 'instructor', 'student', 'parent') NOT NULL DEFAULT 'student',
        `profile_picture` VARCHAR(255) NULL DEFAULT NULL,
        `phone` VARCHAR(20) NULL DEFAULT NULL,
        `address` TEXT NULL DEFAULT NULL,
        `bio` TEXT NULL DEFAULT NULL,
        `status` TINYINT NOT NULL DEFAULT 1,
        `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
        `remember_token` VARCHAR(100) NULL DEFAULT NULL,
        `detailed_description` TEXT NULL DEFAULT NULL,
        `website` VARCHAR(255) NULL DEFAULT NULL,
        `banner_image` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // User Roles table
    "user_roles" => "CREATE TABLE IF NOT EXISTS `user_roles` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `role` VARCHAR(50) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Categories table
    "categories" => "CREATE TABLE IF NOT EXISTS `categories` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `description` TEXT NULL DEFAULT NULL,
        `image` VARCHAR(255) NULL DEFAULT NULL,
        `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `status` TINYINT NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Courses table
    "courses" => "CREATE TABLE IF NOT EXISTS `courses` (
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
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Course Sections table
    "course_sections" => "CREATE TABLE IF NOT EXISTS `course_sections` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `position` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Course Videos table
    "course_videos" => "CREATE TABLE IF NOT EXISTS `course_videos` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `section_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL DEFAULT NULL,
        `url` VARCHAR(255) NULL DEFAULT NULL,
        `duration` VARCHAR(50) NULL DEFAULT NULL,
        `duration_seconds` INT NULL DEFAULT NULL,
        `position` INT NOT NULL DEFAULT 0,
        `sequence_order` INT NOT NULL DEFAULT 0,
        `is_free_preview` TINYINT(1) NOT NULL DEFAULT 0,
        `is_encrypted` TINYINT(1) NOT NULL DEFAULT 0,
        `storage_disk` VARCHAR(50) NULL DEFAULT 'public',
        `video_path` VARCHAR(255) NULL DEFAULT NULL,
        `hls_path` VARCHAR(255) NULL DEFAULT NULL,
        `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Course Materials table
    "course_materials" => "CREATE TABLE IF NOT EXISTS `course_materials` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `section_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL DEFAULT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `file_url` VARCHAR(255) NULL DEFAULT NULL,
        `file_type` VARCHAR(50) NOT NULL DEFAULT 'pdf',
        `file_size` BIGINT NULL DEFAULT NULL,
        `download_count` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Enrollments table
    "enrollments" => "CREATE TABLE IF NOT EXISTS `enrollments` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `enrolled_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `payment_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `completion_date` TIMESTAMP NULL DEFAULT NULL,
        `progress` INT NOT NULL DEFAULT 0,
        `status` ENUM('active', 'completed', 'abandoned') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Ratings table
    "ratings" => "CREATE TABLE IF NOT EXISTS `ratings` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `rating` INT NOT NULL,
        `review` TEXT NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Course Reviews table
    "course_reviews" => "CREATE TABLE IF NOT EXISTS `course_reviews` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `rating` INT NOT NULL,
        `review` TEXT NULL DEFAULT NULL,
        `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Instructor Verifications table
    "instructor_verifications" => "CREATE TABLE IF NOT EXISTS `instructor_verifications` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `identification_type` VARCHAR(50) NOT NULL,
        `identification_number` VARCHAR(100) NOT NULL,
        `identification_image` VARCHAR(255) NOT NULL,
        `qualifications` TEXT NULL DEFAULT NULL,
        `teaching_experience` TEXT NULL DEFAULT NULL,
        `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        `admin_notes` TEXT NULL DEFAULT NULL,
        `payment_details` TEXT NULL DEFAULT NULL,
        `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Instructor Earnings table
    "instructor_earnings" => "CREATE TABLE IF NOT EXISTS `instructor_earnings` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `instructor_id` BIGINT UNSIGNED NOT NULL,
        `amount` DECIMAL(10,2) NOT NULL,
        `description` VARCHAR(255) NULL DEFAULT NULL,
        `type` ENUM('course_purchase', 'subscription', 'other') NOT NULL DEFAULT 'course_purchase',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Payments table
    "payments" => "CREATE TABLE IF NOT EXISTS `payments` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `student_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `course_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `amount` DECIMAL(10,2) NOT NULL,
        `payment_method` VARCHAR(50) NOT NULL,
        `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `transaction_id` VARCHAR(255) NULL DEFAULT NULL,
        `status` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
        `notes` TEXT NULL DEFAULT NULL,
        `payment_details` TEXT NULL DEFAULT NULL,
        `coupon_code` VARCHAR(50) NULL DEFAULT NULL,
        `coupon_discount` DECIMAL(10,2) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Chats table
    "chats" => "CREATE TABLE IF NOT EXISTS `chats` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NULL DEFAULT NULL,
        `type` ENUM('private', 'group') NOT NULL DEFAULT 'private',
        `last_message_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Chat Participants table
    "chat_participants" => "CREATE TABLE IF NOT EXISTS `chat_participants` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `chat_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Messages table
    "messages" => "CREATE TABLE IF NOT EXISTS `messages` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `chat_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `receiver_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `contains_banned_words` TINYINT(1) NOT NULL DEFAULT 0,
        `filtered_message` TEXT NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Quizzes table
    "quizzes" => "CREATE TABLE IF NOT EXISTS `quizzes` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `section_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL DEFAULT NULL,
        `duration_minutes` INT NULL DEFAULT 30,
        `passing_percentage` INT NOT NULL DEFAULT 70,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `is_published` TINYINT(1) NOT NULL DEFAULT 0,
        `questions_json` JSON NULL DEFAULT NULL,
        `max_attempts` INT NULL DEFAULT NULL,
        `start_date` TIMESTAMP NULL DEFAULT NULL,
        `end_date` TIMESTAMP NULL DEFAULT NULL,
        `created_by` BIGINT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Quiz Attempts table
    "quiz_attempts" => "CREATE TABLE IF NOT EXISTS `quiz_attempts` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `quiz_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `end_time` TIMESTAMP NULL DEFAULT NULL,
        `answers_json` JSON NULL DEFAULT NULL,
        `score` INT NULL DEFAULT NULL,
        `score_percentage` DECIMAL(5,2) NULL DEFAULT NULL,
        `correct_answers_count` INT NULL DEFAULT NULL,
        `status` ENUM('in_progress', 'completed', 'abandoned') NOT NULL DEFAULT 'in_progress',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Exams table
    "exams" => "CREATE TABLE IF NOT EXISTS `exams` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL DEFAULT NULL,
        `duration_minutes` INT NOT NULL DEFAULT 60,
        `passing_score` INT NOT NULL DEFAULT 60,
        `questions_json` JSON NULL DEFAULT NULL,
        `is_published` TINYINT(1) NOT NULL DEFAULT 0,
        `available_to` TIMESTAMP NULL DEFAULT NULL,
        `available_from` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Exam Attempts table
    "exam_attempts" => "CREATE TABLE IF NOT EXISTS `exam_attempts` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `exam_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `score` INT NULL DEFAULT NULL,
        `answers_json` JSON NULL DEFAULT NULL,
        `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `end_time` TIMESTAMP NULL DEFAULT NULL,
        `status` ENUM('in_progress', 'completed', 'abandoned') NOT NULL DEFAULT 'in_progress',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Student Progress table
    "student_progress" => "CREATE TABLE IF NOT EXISTS `student_progress` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `student_id` BIGINT UNSIGNED NOT NULL,
        `course_id` BIGINT UNSIGNED NOT NULL,
        `video_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `watched_duration` INT NULL DEFAULT 0,
        `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
        `last_watched_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Parent Student Relations table
    "parent_student_relations" => "CREATE TABLE IF NOT EXISTS `parent_student_relations` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `parent_id` BIGINT UNSIGNED NOT NULL,
        `student_id` BIGINT UNSIGNED NOT NULL,
        `relationship` VARCHAR(50) NOT NULL DEFAULT 'parent',
        `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        `token` VARCHAR(100) NULL DEFAULT NULL,
        `verified_by` VARCHAR(50) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Notifications table
    "notifications" => "CREATE TABLE IF NOT EXISTS `notifications` (
        `id` CHAR(36) NOT NULL,
        `type` VARCHAR(255) NOT NULL,
        `notifiable_type` VARCHAR(255) NOT NULL,
        `notifiable_id` BIGINT UNSIGNED NOT NULL,
        `data` TEXT NOT NULL,
        `read_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Admin Notifications table
    "admin_notifications" => "CREATE TABLE IF NOT EXISTS `admin_notifications` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `severity` ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Settings table
    "settings" => "CREATE TABLE IF NOT EXISTS `settings` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `key` VARCHAR(255) NOT NULL UNIQUE,
        `value` TEXT NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Video Views table
    "video_views" => "CREATE TABLE IF NOT EXISTS `video_views` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `video_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `ip_address` VARCHAR(45) NULL DEFAULT NULL,
        `user_agent` TEXT NULL DEFAULT NULL,
        `viewed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Coupons table
    "coupons" => "CREATE TABLE IF NOT EXISTS `coupons` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `code` VARCHAR(50) NOT NULL UNIQUE,
        `type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
        `value` DECIMAL(10,2) NOT NULL,
        `max_uses` INT NULL DEFAULT NULL,
        `used_count` INT NOT NULL DEFAULT 0,
        `expires_at` TIMESTAMP NULL DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Withdrawals table
    "withdrawals" => "CREATE TABLE IF NOT EXISTS `withdrawals` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `amount` DECIMAL(10,2) NOT NULL,
        `status` ENUM('pending', 'processing', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
        `payment_method` VARCHAR(50) NOT NULL,
        `payment_details` TEXT NULL DEFAULT NULL,
        `payout_batch_id` VARCHAR(255) NULL DEFAULT NULL,
        `transaction_id` VARCHAR(255) NULL DEFAULT NULL,
        `notes` TEXT NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Migrations table (for Laravel's migration system)
    "migrations" => "CREATE TABLE IF NOT EXISTS `migrations` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `migration` VARCHAR(255) NOT NULL,
        `batch` INT(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

// Counter variables
$success = 0;
$failed = 0;
$errors = [];

// Create tables
foreach ($tables as $tableName => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table $tableName created successfully.<br>";
        $success++;
    } else {
        echo "Error creating table $tableName: " . $conn->error . "<br>";
        $errors[] = "Table $tableName: " . $conn->error;
        $failed++;
    }
}

// Print summary
echo "<br><strong>Summary:</strong><br>";
echo "$success tables created successfully.<br>";
echo "$failed tables failed to create.<br>";

if (!empty($errors)) {
    echo "<br><strong>Errors:</strong><br>";
    foreach ($errors as $error) {
        echo "$error<br>";
    }
}

$conn->close(); 