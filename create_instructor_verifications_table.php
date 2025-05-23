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
    
    // Check if the instructor_verifications table exists
    $tables = $conn->query("SHOW TABLES LIKE 'instructor_verifications'")->fetchAll();
    
    if (count($tables) == 0) {
        // Create the instructor_verifications table if it doesn't exist
        echo "Creating 'instructor_verifications' table...\n";
        
        $sql = "CREATE TABLE instructor_verifications (
            verification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            education TEXT NULL,
            expertise TEXT NULL,
            years_of_experience INT NULL,
            linkedin_profile VARCHAR(255) NULL,
            additional_info TEXT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            submitted_at TIMESTAMP NULL,
            payment_details TEXT NULL,
            certificate_file VARCHAR(255) NULL,
            id_document VARCHAR(255) NULL,
            cv_document VARCHAR(255) NULL,
            certificate_document VARCHAR(255) NULL,
            admin_notes TEXT NULL,
            verified_by BIGINT UNSIGNED NULL,
            verified_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL
        )";
        
        $conn->exec($sql);
        echo "Created 'instructor_verifications' table successfully.\n";
    } else {
        echo "The 'instructor_verifications' table already exists.\n";
        
        // Check and add each column if it doesn't exist
        $columns = [
            'education' => 'TEXT NULL',
            'expertise' => 'TEXT NULL',
            'years_of_experience' => 'INT NULL',
            'linkedin_profile' => 'VARCHAR(255) NULL',
            'additional_info' => 'TEXT NULL',
            'status' => "ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'",
            'submitted_at' => 'TIMESTAMP NULL',
            'payment_details' => 'TEXT NULL',
            'certificate_file' => 'VARCHAR(255) NULL',
            'id_document' => 'VARCHAR(255) NULL',
            'cv_document' => 'VARCHAR(255) NULL',
            'certificate_document' => 'VARCHAR(255) NULL',
            'admin_notes' => 'TEXT NULL',
            'verified_by' => 'BIGINT UNSIGNED NULL',
            'verified_at' => 'TIMESTAMP NULL'
        ];
        
        foreach ($columns as $column => $definition) {
            $stmt = $conn->prepare("SHOW COLUMNS FROM instructor_verifications LIKE '$column'");
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                echo "Adding '$column' column to instructor_verifications table...\n";
                $sql = "ALTER TABLE instructor_verifications ADD COLUMN $column $definition";
                $conn->exec($sql);
                echo "Added '$column' column successfully.\n";
            } else {
                echo "The '$column' column already exists in the instructor_verifications table.\n";
            }
        }
    }
    
    // Check table structure after changes
    echo "\nCurrent instructor_verifications table structure:\n";
    $stmt = $conn->prepare("DESCRIBE instructor_verifications");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 