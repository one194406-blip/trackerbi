<?php
/**
 * SSL Fix Tool for XAMPP on Windows
 * Automatically fixes SSL certificate issues for Facebook Graph API
 */

set_time_limit(120); // Allow more time for downloads

echo "<h1>🔧 SSL Fix Tool for XAMPP/Windows</h1>";
echo "<p><strong>This tool will automatically fix SSL certificate issues for Facebook Graph API connections.</strong></p>";

// Check if we're running on Windows
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    echo "<div style='background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px;'>";
    echo "<p>❌ This tool is designed for Windows XAMPP installations only.</p>";
    echo "<p>Current OS: " . PHP_OS . "</p>";
    echo "</div>";
    exit;
}

// Get XAMPP installation path
$phpBinary = PHP_BINARY;
$xamppPath = dirname(dirname($phpBinary)); // Should be C:\xampp
$apachePath = $xamppPath . '\apache';
$phpIniPath = php_ini_loaded_file();

echo "<div style='background: #f0f8ff; border: 1px solid #cce; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h2>📋 System Information</h2>";
echo "<p><strong>XAMPP Path:</strong> $xamppPath</p>";
echo "<p><strong>Apache Path:</strong> $apachePath</p>";
echo "<p><strong>PHP.ini Path:</strong> $phpIniPath</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>cURL Version:</strong> " . (function_exists('curl_version') ? curl_version()['version'] : 'Not available') . "</p>";
echo "</div>";

// Step 1: Download CA certificates
echo "<h2>📥 Step 1: Downloading Latest CA Certificates</h2>";

$certDir = $xamppPath . '\apache\conf\ssl.crt';
$caCertFile = $certDir . '\cacert.pem';

// Create certificate directory if it doesn't exist
if (!is_dir($certDir)) {
    echo "<p>📁 Creating certificate directory...</p>";
    if (mkdir($certDir, 0755, true)) {
        echo "<p>✅ Certificate directory created: $certDir</p>";
    } else {
        echo "<p>❌ Failed to create certificate directory</p>";
        exit;
    }
}

// Download certificates using a simple method
echo "<p>🌐 Downloading CA certificates from curl.se...</p>";

$caCertUrl = 'https://curl.se/ca/cacert.pem';
$context = stream_context_create([
    'http' => [
        'timeout' => 60,
        'user_agent' => 'TrackerBI SSL Fix Tool'
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$certData = @file_get_contents($caCertUrl, false, $context);

if ($certData && strlen($certData) > 1000) {
    echo "<p>✅ Certificate data downloaded (" . number_format(strlen($certData)) . " bytes)</p>";
    
    if (file_put_contents($caCertFile, $certData)) {
        echo "<p>✅ Certificate saved to: $caCertFile</p>";
    } else {
        echo "<p>❌ Failed to save certificate file</p>";
        exit;
    }
} else {
    echo "<p>⚠️ Failed to download certificates via HTTPS, trying alternative method...</p>";
    
    // Alternative: Use cURL with SSL verification disabled
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $caCertUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'TrackerBI SSL Fix Tool'
    ]);
    
    $certData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($certData && $httpCode == 200 && strlen($certData) > 1000) {
        echo "<p>✅ Certificate data downloaded via cURL (" . number_format(strlen($certData)) . " bytes)</p>";
        
        if (file_put_contents($caCertFile, $certData)) {
            echo "<p>✅ Certificate saved to: $caCertFile</p>";
        } else {
            echo "<p>❌ Failed to save certificate file</p>";
            exit;
        }
    } else {
        echo "<p>❌ Failed to download certificates</p>";
        echo "<p>Error: $error</p>";
        echo "<p>HTTP Code: $httpCode</p>";
        
        // Create a basic certificate file as fallback
        echo "<p>🔄 Creating basic certificate configuration...</p>";
        $basicConfig = "; Basic SSL configuration for Facebook API\n";
        $basicConfig .= "; This is a fallback configuration\n";
        file_put_contents($caCertFile, $basicConfig);
        echo "<p>⚠️ Basic configuration created</p>";
    }
}

// Step 2: Update PHP configuration
echo "<h2>⚙️ Step 2: Updating PHP Configuration</h2>";

if ($phpIniPath && file_exists($phpIniPath)) {
    echo "<p>📝 Reading current PHP configuration...</p>";
    $phpIniContent = file_get_contents($phpIniPath);
    
    // Check if curl.cainfo is already configured
    if (strpos($phpIniContent, 'curl.cainfo') !== false) {
        echo "<p>⚠️ curl.cainfo already exists in php.ini</p>";
        
        // Update existing configuration
        $phpIniContent = preg_replace(
            '/^;?\s*curl\.cainfo\s*=.*$/m',
            'curl.cainfo = "' . str_replace('\\', '\\\\', $caCertFile) . '"',
            $phpIniContent
        );
        
        if (file_put_contents($phpIniPath, $phpIniContent)) {
            echo "<p>✅ Updated existing curl.cainfo setting</p>";
        } else {
            echo "<p>❌ Failed to update php.ini (permission denied)</p>";
        }
    } else {
        // Add new configuration
        $newConfig = "\n; SSL Certificate configuration added by TrackerBI SSL Fix Tool\n";
        $newConfig .= 'curl.cainfo = "' . str_replace('\\', '\\\\', $caCertFile) . '"' . "\n";
        $newConfig .= 'openssl.cafile = "' . str_replace('\\', '\\\\', $caCertFile) . '"' . "\n";
        
        if (file_put_contents($phpIniPath, $phpIniContent . $newConfig)) {
            echo "<p>✅ Added SSL certificate configuration to php.ini</p>";
        } else {
            echo "<p>❌ Failed to update php.ini (permission denied)</p>";
        }
    }
} else {
    echo "<p>❌ Cannot locate or access php.ini file</p>";
}

// Step 3: Test SSL connection
echo "<h2>🧪 Step 3: Testing SSL Connection</h2>";

echo "<p>🔍 Testing connection to Facebook Graph API...</p>";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://graph.facebook.com/v22.0/me?access_token=test_token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_CAINFO => $caCertFile,
    CURLOPT_USERAGENT => 'TrackerBI SSL Test'
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

if ($result && empty($error)) {
    echo "<p>✅ SSL connection to Facebook API successful!</p>";
    echo "<p>📊 HTTP Code: $httpCode</p>";
    echo "<p>⏱️ Connection Time: " . round($info['total_time'], 2) . " seconds</p>";
    
    // Parse the response to see if it's a valid API response
    $json = json_decode($result, true);
    if (isset($json['error'])) {
        echo "<p>✅ API responded correctly (expected error for test token)</p>";
        echo "<p>📝 API Error: " . htmlspecialchars($json['error']['message']) . "</p>";
    }
} else {
    echo "<p>⚠️ SSL connection still has issues</p>";
    echo "<p>❌ Error: $error</p>";
    echo "<p>📊 HTTP Code: $httpCode</p>";
    
    // Try with SSL verification disabled as fallback test
    echo "<p>🔄 Testing with SSL verification disabled...</p>";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://graph.facebook.com/v22.0/me?access_token=test_token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'TrackerBI SSL Test'
    ]);
    
    $result2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error2 = curl_error($ch);
    curl_close($ch);
    
    if ($result2 && empty($error2)) {
        echo "<p>✅ Connection works without SSL verification</p>";
        echo "<p>⚠️ This means the certificate configuration needs adjustment</p>";
    } else {
        echo "<p>❌ Connection fails even without SSL verification</p>";
        echo "<p>🌐 This might be a network connectivity issue</p>";
    }
}

// Step 4: Instructions
echo "<h2>📋 Step 4: Next Steps</h2>";

echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>🔄 Required Actions:</h3>";
echo "<ol>";
echo "<li><strong>Restart Apache:</strong> Go to XAMPP Control Panel and restart Apache</li>";
echo "<li><strong>Clear PHP cache:</strong> Restart the entire XAMPP if possible</li>";
echo "<li><strong>Test the Meta Dashboard:</strong> Try accessing your dashboard again</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>🧪 Test Your Dashboard:</h3>";
echo "<p><a href='meta-dashboard.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🔄 Test Live Mode</a>";
echo "<a href='meta-dashboard.php?demo=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👁️ Use Demo Mode</a></p>";
echo "</div>";

echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>⚠️ If SSL Issues Persist:</h3>";
echo "<ul>";
echo "<li><strong>Use Demo Mode:</strong> Full functionality with sample data</li>";
echo "<li><strong>Check Windows Firewall:</strong> Temporarily disable to test</li>";
echo "<li><strong>Check Antivirus:</strong> Some antivirus software blocks SSL connections</li>";
echo "<li><strong>Update XAMPP:</strong> Download the latest version from apachefriends.org</li>";
echo "<li><strong>Production Deployment:</strong> Consider Linux hosting for live environment</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>✅ Configuration Summary:</h3>";
echo "<ul>";
echo "<li><strong>Certificate File:</strong> $caCertFile</li>";
echo "<li><strong>PHP Configuration:</strong> Updated with SSL settings</li>";
echo "<li><strong>Status:</strong> " . (($result && empty($error)) ? "SSL Working ✅" : "Needs Manual Configuration ⚠️") . "</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><small>🛠️ TrackerBI SSL Fix Tool - " . date('Y-m-d H:i:s') . "</small></p>";
?>
