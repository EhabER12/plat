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

// Check if withdrawals table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'withdrawals'");
if ($tableCheck->num_rows == 0) {
    // Create the withdrawals table with proper structure
    $createTableSql = "CREATE TABLE IF NOT EXISTS `withdrawals` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `amount` DECIMAL(10,2) NOT NULL,
        `status` ENUM('pending', 'processing', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
        `payment_method` VARCHAR(50) NOT NULL,
        `payment_details` TEXT NULL DEFAULT NULL,
        `payout_batch_id` VARCHAR(255) NULL DEFAULT NULL,
        `transaction_id` VARCHAR(255) NULL DEFAULT NULL,
        `notes` TEXT NULL DEFAULT NULL,
        `processed_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    if ($conn->query($createTableSql) === TRUE) {
        echo "Withdrawals table created successfully<br>";
    } else {
        echo "Error creating withdrawals table: " . $conn->error . "<br>";
        die();
    }
} else {
    // Table exists, let's check if it has the processed_at column
    $result = $conn->query("DESCRIBE withdrawals");
    $hasProcessedAt = false;
    
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] == 'processed_at') {
            $hasProcessedAt = true;
            break;
        }
    }
    
    if (!$hasProcessedAt) {
        // Add the processed_at column
        $conn->query("ALTER TABLE withdrawals ADD COLUMN processed_at TIMESTAMP NULL DEFAULT NULL AFTER notes");
        echo "Added processed_at column to withdrawals table<br>";
    } else {
        echo "Withdrawals table already has processed_at column<br>";
    }
}

// Add some sample withdrawal data
$timestamp = date("Y-m-d H:i:s");
$withdrawals = [
    [
        1, 100.00, 'completed', 'paypal', 'PayPal: instructor@example.com', 'BATCH123', 'TXN123', 'Completed payment', $timestamp
    ],
    [
        2, 200.00, 'pending', 'bank_transfer', 'Bank: IBAN1234567890', NULL, NULL, 'Pending approval', NULL
    ],
    [
        1, 150.00, 'rejected', 'paypal', 'PayPal: instructor@example.com', NULL, NULL, 'Insufficient balance', NULL
    ]
];

$insertCount = 0;
foreach ($withdrawals as $withdrawal) {
    $insertSql = "INSERT IGNORE INTO withdrawals 
                 (user_id, amount, status, payment_method, payment_details, payout_batch_id, transaction_id, notes, processed_at, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                 
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("idsssssssss", 
                     $withdrawal[0], $withdrawal[1], $withdrawal[2], $withdrawal[3], 
                     $withdrawal[4], $withdrawal[5], $withdrawal[6], $withdrawal[7], 
                     $withdrawal[8], $timestamp, $timestamp);
    
    if ($stmt->execute()) {
        $insertCount++;
    } else {
        echo "Error inserting withdrawal record: " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

echo "$insertCount sample withdrawal records inserted successfully<br>";
echo "Withdrawals table fixed successfully!";

$conn->close(); 