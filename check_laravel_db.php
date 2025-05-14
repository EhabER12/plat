<?php
// Check Laravel database settings

// Load environment variables
$envFile = file_get_contents('.env');
$lines = explode("\n", $envFile);
$env = [];

foreach ($lines as $line) {
    if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
        continue;
    }
    
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $env[$key] = $value;
    }
}

echo "Laravel Environment Settings:\n";
echo "--------------------------------\n";
echo "DB_CONNECTION: " . ($env['DB_CONNECTION'] ?? 'Not set') . "\n";
echo "DB_HOST: " . ($env['DB_HOST'] ?? 'Not set') . "\n";
echo "DB_PORT: " . ($env['DB_PORT'] ?? 'Not set') . "\n";
echo "DB_DATABASE: " . ($env['DB_DATABASE'] ?? 'Not set') . "\n";
echo "DB_USERNAME: " . ($env['DB_USERNAME'] ?? 'Not set') . "\n";
echo "DB_PASSWORD: " . ($env['DB_PASSWORD'] ?? 'Not set') . "\n";

// Check MySQL Server
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';

echo "\nChecking MySQL server connection...\n";
echo "--------------------------------\n";

try {
    $conn = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Successfully connected to MySQL server\n";
    
    // List all databases
    $stmt = $conn->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Available databases:\n";
    foreach ($databases as $database) {
        echo "- " . $database . ($database === ($env['DB_DATABASE'] ?? '') ? " (selected in .env)" : "") . "\n";
    }
    
    // Check if the database exists
    $dbName = $env['DB_DATABASE'] ?? 'yousef_db2';
    if (in_array($dbName, $databases)) {
        echo "\nDatabase '$dbName' exists.\n";
        
        // Check tables in the database
        try {
            $dbConn = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $user, $pass);
            $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $dbConn->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "Tables in the database:\n";
            if (count($tables) > 0) {
                foreach ($tables as $table) {
                    echo "- " . $table . "\n";
                }
            } else {
                echo "No tables found in the database.\n";
            }
        } catch (PDOException $e) {
            echo "Error accessing database: " . $e->getMessage() . "\n";
        }
    } else {
        echo "\nDatabase '$dbName' does not exist!\n";
    }
    
} catch (PDOException $e) {
    echo "MySQL connection failed: " . $e->getMessage() . "\n";
} 