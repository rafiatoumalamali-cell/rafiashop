<?php
// download_complete_stripe.php
echo "<h2>Downloading Complete Stripe Library</h2>";

$vendorDir = __DIR__ . '/vendor';
$stripeDir = $vendorDir . '/stripe-php';

// Remove incomplete files
if (file_exists($stripeDir)) {
    echo "Removing incomplete Stripe files...<br>";
    deleteDirectory($stripeDir);
}

// Create vendor directory
if (!file_exists($vendorDir)) {
    mkdir($vendorDir, 0777, true);
}

// Download fresh from GitHub
$url = 'https://github.com/stripe/stripe-php/archive/refs/tags/v10.14.0.zip';
$zipFile = $vendorDir . '/stripe_temp.zip';

echo "Downloading from GitHub...<br>";

// Use cURL for better error handling
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$zipData = curl_exec($ch);

if (curl_errno($ch)) {
    die("Download failed: " . curl_error($ch));
}
curl_close($ch);

if ($zipData) {
    file_put_contents($zipFile, $zipData);
    echo "✅ Downloaded successfully (".number_format(strlen($zipData))." bytes)<br>";
    
    // Extract
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($vendorDir);
        $zip->close();
        echo "✅ Extracted successfully<br>";
        
        // Rename extracted folder
        $extractedDir = $vendorDir . '/stripe-php-10.14.0';
        if (file_exists($extractedDir)) {
            rename($extractedDir, $stripeDir);
            echo "✅ Renamed to: stripe-php/<br>";
            
            // Verify
            echo "<h3>Verifying Download:</h3>";
            echo "Directory exists: " . (file_exists($stripeDir) ? '✅ YES' : '❌ NO') . "<br>";
            
            $initFile = $stripeDir . '/init.php';
            echo "init.php exists: " . (file_exists($initFile) ? '✅ YES' : '❌ NO') . "<br>";
            
            // Count files
            $files = countFiles($stripeDir);
            echo "Total files downloaded: " . $files . "<br>";
            
            if ($files > 50) {
                echo "✅ Download looks complete!<br>";
            } else {
                echo "⚠️ Warning: Only $files files - might still be incomplete<br>";
            }
        } else {
            echo "❌ Extracted directory not found!<br>";
        }
        
        // Clean up
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }
        
    } else {
        echo "❌ Failed to extract ZIP file<br>";
    }
} else {
    echo "❌ Failed to download<br>";
}

echo "<h3>Next Steps:</h3>";
echo "1. Test your Payment.php again<br>";
echo "2. Run: <a href='test_payment.php'>test_payment.php</a><br>";

function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . '/' . $item)) return false;
    }
    
    return rmdir($dir);
}

function countFiles($dir) {
    $count = 0;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $count += countFiles($path);
            } else {
                $count++;
            }
        }
    }
    return $count;
}
?>