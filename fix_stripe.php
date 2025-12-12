<?php
// fix_stripe.php
echo "<h2>Fixing Stripe Installation</h2>";

// Target directory
$vendorDir = __DIR__ . '/vendor';
$stripeDir = $vendorDir . '/stripe-php';

// Create vendor directory if needed
if (!file_exists($vendorDir)) {
    mkdir($vendorDir, 0777, true);
    echo "Created vendor directory<br>";
}

// Remove old Stripe if exists
if (file_exists($stripeDir)) {
    echo "Removing old Stripe installation...<br>";
    system("rmdir /s /q " . escapeshellarg($stripeDir));
    echo "Old Stripe removed<br>";
}

// Download Stripe directly from GitHub
echo "Downloading Stripe PHP library...<br>";

// Use a direct download link
$downloadUrl = 'https://github.com/stripe/stripe-php/archive/refs/tags/v10.14.0.zip';
$zipFile = __DIR__ . '/stripe_latest.zip';

// Download using file_get_contents with context
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
]);

$zipData = @file_get_contents($downloadUrl, false, $context);

if ($zipData) {
    file_put_contents($zipFile, $zipData);
    echo "✅ Downloaded successfully (" . strlen($zipData) . " bytes)<br>";
    
    // Extract
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($vendorDir);
        $zip->close();
        echo "✅ Extracted successfully<br>";
        
        // Rename folder
        $extractedPath = $vendorDir . '/stripe-php-10.14.0';
        if (file_exists($extractedPath)) {
            rename($extractedPath, $stripeDir);
            echo "✅ Renamed to: $stripeDir<br>";
        }
        
        // Clean up
        unlink($zipFile);
        
        // Verify installation
        echo "<h3>Verifying Installation</h3>";
        $initFile = $stripeDir . '/init.php';
        if (file_exists($initFile)) {
            echo "✅ init.php exists<br>";
            
            // Test loading
            require_once $initFile;
            if (class_exists('Stripe\Stripe')) {
                echo "✅ Stripe class loaded successfully!<br>";
                
                // Test API key setting
                \Stripe\Stripe::setApiKey('sk_test_dummy');
                echo "✅ Stripe API configured<br>";
                
                echo "<h3 style='color:green'>✅ STRIPE INSTALLATION SUCCESSFUL!</h3>";
            } else {
                echo "❌ Stripe class not found after loading<br>";
            }
        } else {
            echo "❌ init.php not found! Checking contents...<br>";
            
            // List files
            $files = scandir($stripeDir);
            echo "Files in stripe-php:<br>";
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "- $file<br>";
                }
            }
        }
    } else {
        echo "❌ Failed to open zip file<br>";
    }
} else {
    echo "❌ Failed to download. Trying alternative method...<br>";
    
    // Alternative: Use command line if available
    if (function_exists('shell_exec')) {
        echo "Trying with curl/wget...<br>";
        
        // Try curl
        $cmd = "cd " . escapeshellarg($vendorDir) . " && curl -L https://github.com/stripe/stripe-php/archive/refs/tags/v10.14.0.zip -o stripe.zip 2>&1";
        $output = shell_exec($cmd);
        echo "Curl output: $output<br>";
        
        if (file_exists($vendorDir . '/stripe.zip')) {
            $zip = new ZipArchive;
            if ($zip->open($vendorDir . '/stripe.zip') === TRUE) {
                $zip->extractTo($vendorDir);
                $zip->close();
                rename($vendorDir . '/stripe-php-10.14.0', $stripeDir);
                unlink($vendorDir . '/stripe.zip');
                echo "✅ Installed via curl<br>";
            }
        }
    }
}

// Create a simple test file
echo "<h3>Creating Test File</h3>";
$testFile = __DIR__ . '/test_stripe_working.php';
$testContent = '<?php
require_once __DIR__ . \'/vendor/stripe-php/init.php\';
echo "Stripe Version: " . (\Stripe\Stripe::VERSION ?? "Unknown") . "<br>";
echo "Stripe Class Exists: " . (class_exists(\'Stripe\Stripe\') ? "YES" : "NO") . "<br>";
?>';

file_put_contents($testFile, $testContent);
echo "✅ Test file created: <a href='test_stripe_working.php'>test_stripe_working.php</a><br>";

echo "<hr><h3>Next Steps:</h3>";
echo "1. <a href='test_stripe_working.php'>Run the test file</a><br>";
echo "2. Update your Payment.php with the code below<br>";
echo "3. Test checkout again<br>";
?>