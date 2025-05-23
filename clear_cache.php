<?php

// Delete cache files from storage directory
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

// Cache directories to clear
$directories = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions',
    'bootstrap/cache'
];

// Clear each directory
foreach ($directories as $directory) {
    echo "Clearing {$directory}... ";
    if (is_dir($directory)) {
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file) && basename($file) != '.' && basename($file) != '..') {
                deleteDirectory($file);
            }
        }
        echo "done<br>";
    } else {
        echo "directory not found<br>";
    }
}

echo "Cache cleared successfully!<br>";
echo "<a href='/'>Return to home page</a>"; 