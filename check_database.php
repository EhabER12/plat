<?php
// Simple script to check database connection

try {
    $host = '127.0.0.1';
    $dbname = 'yousef_db2';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Successfully connected to database: " . $dbname . PHP_EOL;
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in the database:" . PHP_EOL;
    foreach ($tables as $table) {
        echo "- " . $table . PHP_EOL;
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . PHP_EOL;
} 