<?php
$upload_dir = 'C:/xampp/htdocs/rafiashop/public/uploads/products/';

echo "Checking upload directory:<br>";
echo "Directory: " . $upload_dir . "<br>";
echo "Exists: " . (is_dir($upload_dir) ? 'YES' : 'NO') . "<br>";
echo "Writable: " . (is_writable($upload_dir) ? 'YES' : 'NO') . "<br>";
echo "Readable: " . (is_readable($upload_dir) ? 'YES' : 'NO') . "<br>";

// Test creating a file
$test_file = $upload_dir . 'test_permissions.txt';
if (file_put_contents($test_file, 'test')) {
    echo "File creation: SUCCESS<br>";
    unlink($test_file); // Delete test file
} else {
    echo "File creation: FAILED<br>";
}

// Check absolute path
echo "Current directory: " . __DIR__ . "<br>";
?>