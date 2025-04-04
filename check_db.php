<?php

// Database connection parameters
$host = '127.0.0.1';
$db   = 'yousef_db2';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Connect to the database
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "Connected to database: $db\n\n";
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check if user_roles table exists
    if (in_array('user_roles', $tables)) {
        echo "\nuser_roles table exists\n";
        
        // Get structure of user_roles table
        $stmt = $pdo->query("DESCRIBE user_roles");
        $columns = $stmt->fetchAll();
        
        echo "\nStructure of user_roles table:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Get data from user_roles table
        $stmt = $pdo->query("SELECT * FROM user_roles LIMIT 10");
        $rows = $stmt->fetchAll();
        
        echo "\nData in user_roles table (up to 10 rows):\n";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "- User ID: {$row['user_id']}, Role: {$row['role']}\n";
            }
        } else {
            echo "No data found in user_roles table\n";
        }
    } else {
        echo "\nuser_roles table does not exist\n";
    }
    
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} 