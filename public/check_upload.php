<?php
// Check PHP upload settings
echo "<h2>PHP File Upload Settings</h2>";
echo "<p>Maximum upload file size: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Maximum POST size: " . ini_get('post_max_size') . "</p>";
echo "<p>Temporary directory: " . ini_get('upload_tmp_dir') . "</p>";

// Check if temp directory exists and is writable
$temp_dir = ini_get('upload_tmp_dir');
if ($temp_dir) {
    echo "<p>Temp directory exists: " . (file_exists($temp_dir) ? 'Yes' : 'No') . "</p>";
    echo "<p>Temp directory is writable: " . (is_writable($temp_dir) ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p>No specific upload_tmp_dir set. PHP will use the system default.</p>";
}

// Display other relevant settings
echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";
echo "<p>max_execution_time: " . ini_get('max_execution_time') . " seconds</p>";

// Try to create a test file in the temp directory
if ($temp_dir && is_writable($temp_dir)) {
    $test_file = $temp_dir . '/php_test_' . time() . '.txt';
    $result = file_put_contents($test_file, 'Test file write');
    echo "<p>Test file creation: " . ($result !== false ? 'Success' : 'Failed') . "</p>";
    if ($result !== false) {
        echo "<p>Test file size: " . filesize($test_file) . " bytes</p>";
        unlink($test_file);
    }
}

// Server information
echo "<h2>Server Information</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Operating System: " . PHP_OS . "</p>";
?> 