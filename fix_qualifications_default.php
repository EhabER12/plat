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
    
    // First check if the qualifications column exists
    $stmt = $conn->prepare("SHOW COLUMNS FROM instructor_verifications LIKE 'qualifications'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Get information about the column
        $column = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Current 'qualifications' column:\n";
        echo "- Type: {$column['Type']}\n";
        echo "- Null: {$column['Null']}\n";
        echo "- Default: " . ($column['Default'] === NULL ? "NULL" : $column['Default']) . "\n";
        
        // Try to modify it to have a default value
        try {
            echo "Modifying 'qualifications' column to have a default value...\n";
            // First try with TEXT type
            try {
                $sql = "ALTER TABLE instructor_verifications MODIFY qualifications TEXT NULL DEFAULT NULL";
                $conn->exec($sql);
                echo "Modified 'qualifications' column successfully using TEXT type.\n";
            } catch (PDOException $e1) {
                echo "Error with TEXT type: " . $e1->getMessage() . "\n";
                
                // Try with VARCHAR instead
                echo "Trying with VARCHAR type instead...\n";
                $sql = "ALTER TABLE instructor_verifications MODIFY qualifications VARCHAR(1000) NULL DEFAULT NULL";
                $conn->exec($sql);
                echo "Modified 'qualifications' column successfully using VARCHAR type.\n";
            }
        } catch (PDOException $e2) {
            echo "Error: " . $e2->getMessage() . "\n";
            echo "Could not modify the column. Trying an alternative approach...\n";
            
            // Alternative approach - drop and recreate the column
            echo "Dropping and recreating the column...\n";
            try {
                $conn->beginTransaction();
                
                // First, create a backup column
                $sql = "ALTER TABLE instructor_verifications ADD COLUMN qualifications_backup TEXT";
                $conn->exec($sql);
                
                // Copy data to backup
                $sql = "UPDATE instructor_verifications SET qualifications_backup = qualifications";
                $conn->exec($sql);
                
                // Drop original column
                $sql = "ALTER TABLE instructor_verifications DROP COLUMN qualifications";
                $conn->exec($sql);
                
                // Create new column with default
                $sql = "ALTER TABLE instructor_verifications ADD COLUMN qualifications TEXT NULL DEFAULT NULL";
                $conn->exec($sql);
                
                // Restore data
                $sql = "UPDATE instructor_verifications SET qualifications = qualifications_backup";
                $conn->exec($sql);
                
                // Drop backup column
                $sql = "ALTER TABLE instructor_verifications DROP COLUMN qualifications_backup";
                $conn->exec($sql);
                
                $conn->commit();
                echo "Column recreated successfully.\n";
            } catch (PDOException $e3) {
                $conn->rollBack();
                echo "Error in recreating column: " . $e3->getMessage() . "\n";
            }
        }
    } else {
        // Column doesn't exist, add it with a default value
        echo "Adding 'qualifications' column with a default value...\n";
        try {
            $sql = "ALTER TABLE instructor_verifications ADD COLUMN qualifications TEXT NULL DEFAULT NULL";
            $conn->exec($sql);
            echo "Added 'qualifications' column successfully.\n";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage() . "\n";
            
            // Try with VARCHAR instead
            echo "Trying with VARCHAR type instead...\n";
            $sql = "ALTER TABLE instructor_verifications ADD COLUMN qualifications VARCHAR(1000) NULL DEFAULT NULL";
            $conn->exec($sql);
            echo "Added 'qualifications' column successfully using VARCHAR type.\n";
        }
    }
    
    // Show the updated column structure
    $stmt = $conn->prepare("SHOW COLUMNS FROM instructor_verifications LIKE 'qualifications'");
    $stmt->execute();
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nUpdated qualifications column structure:\n";
    echo "- Name: {$column['Field']}\n";
    echo "- Type: {$column['Type']}\n";
    echo "- Null: {$column['Null']}\n";
    echo "- Default: " . ($column['Default'] === NULL ? "NULL" : $column['Default']) . "\n";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 