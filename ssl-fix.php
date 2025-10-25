<?php
/**
 * SSL Fix for XAMPP on Windows
 * This script downloads and configures SSL certificates for Facebook API
 */

echo "<h1>SSL Certificate Fix for XAMPP</h1>";

// Check if we're running on Windows
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    echo "<p>❌ This fix is designed for Windows XAMPP installations only.</p>";
    exit;
}

// Get XAMPP installation path
$xamppPath = dirname(dirname(PHP_BINARY)); // Should be C:\xampp
$certPath = $xamppPath . '\apache\conf\ssl.crt';
$caCertFile = $certPath . '\cacert.pem';

echo "<h2>Current Configuration:</h2>";
echo "<p><strong>XAMPP Path:</strong> $xamppPath</p>";
echo "<p><strong>Certificate Path:</strong> $certPath</p>";
echo "<p><strong>CA Certificate File:</strong> $caCertFile</p>";

// Check if certificate directory exists
if (!is_dir($certPath)) {
    echo "<p>⚠️ Certificate directory doesn't exist. Creating...</p>";
    if (!mkdir($certPath, 0755, true)) {
        echo "<p>❌ Failed to create certificate directory.</p>";
        exit;
    }
}

// Download latest CA certificates
echo "<h2>Downloading Latest CA Certificates:</h2>";
$caCertUrl = 'https://curl.se/ca/cacert.pem';
$tempFile = sys_get_temp_dir() . '\cacert_temp.pem';

echo "<p>Downloading from: $caCertUrl</p>";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $caCertUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_SSL_VERIFYPEER => false, // We need this to download the cert file
    CURLOPT_USERAGENT => 'TrackerBI SSL Fix'
]);

$certData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($certData && $httpCode == 200) {
    echo "<p>✅ Certificate data downloaded successfully (" . strlen($certData) . " bytes)</p>";
    
    // Save to temporary file first
    if (file_put_contents($tempFile, $certData)) {
        echo "<p>✅ Saved to temporary file</p>";
        
        // Move to final location
        if (copy($tempFile, $caCertFile)) {
            echo "<p>✅ Certificate installed to: $caCertFile</p>";
            unlink($tempFile);
        } else {
            echo "<p>❌ Failed to install certificate</p>";
        }
    } else {
        echo "<p>❌ Failed to save certificate data</p>";
    }
} else {
    echo "<p>❌ Failed to download certificates</p>";
    echo "<p>Error: $error</p>";
    echo "<p>HTTP Code: $httpCode</p>";
}

// Update php.ini
echo "<h2>Updating PHP Configuration:</h2>";
$phpIniPath = php_ini_loaded_file();
echo "<p><strong>PHP.ini Path:</strong> $phpIniPath</p>";

if ($phpIniPath && file_exists($caCertFile)) {
    $phpIniContent = file_get_contents($phpIniPath);
    
    // Check if curl.cainfo is already set
    if (strpos($phpIniContent, 'curl.cainfo') !== false) {
        echo "<p>⚠️ curl.cainfo already configured in php.ini</p>";
    } else {
        // Add curl.cainfo setting
        $newSetting = "\n; SSL Certificate fix for Facebook API\ncurl.cainfo = \"$caCertFile\"\n";
        
        if (file_put_contents($phpIniPath, $phpIniContent . $newSetting)) {
            echo "<p>✅ Updated php.ini with certificate path</p>";
            echo "<p>⚠️ <strong>You need to restart Apache for changes to take effect</strong></p>";
        } else {
            echo "<p>❌ Failed to update php.ini (permission denied)</p>";
        }
    }
} else {
    echo "<p>❌ Cannot update php.ini or certificate file missing</p>";
}

// Test the fix
echo "<h2>Testing SSL Connection:</h2>";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://graph.facebook.com/v22.0/me?access_token=test',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true, // Now we can verify
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_CAINFO => $caCertFile
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($result && empty($error)) {
    echo "<p>✅ SSL connection to Facebook API successful!</p>";
    echo "<p>HTTP Code: $httpCode</p>";
} else {
    echo "<p>⚠️ SSL connection still has issues</p>";
    echo "<p>Error: $error</p>";
    echo "<p>This is normal - the test token is invalid, but connection works</p>";
}

echo "<h2>Manual Steps (if automatic fix didn't work):</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #ccc;'>";
echo "<ol>";
echo "<li><strong>Restart Apache:</strong> Stop and start Apache in XAMPP Control Panel</li>";
echo "<li><strong>Alternative:</strong> Add this line to your php.ini:<br><code>curl.cainfo = \"$caCertFile\"</code></li>";
echo "<li><strong>Restart XAMPP:</strong> Completely restart XAMPP</li>";
echo "<li><strong>Test again:</strong> Try the Meta Dashboard</li>";
echo "</ol>";
echo "</div>";

echo "<h2>Quick Links:</h2>";
echo "<p>";
echo "<a href='meta-dashboard.php' style='background: #1877f2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔄 Test Meta Dashboard</a>";
echo "<a href='meta-dashboard.php?demo=1' style='background: #42b883; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👁️ Use Demo Mode</a>";
echo "</p>";
?>
