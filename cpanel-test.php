<?php
// Simple cPanel Test - Check if basic PHP and .env loading works
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cPanel Test - TrackerBI</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #333; }
        ul { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 10px 0; }
        li { margin: 5px 0; }
        .btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body { margin: 10px; }
            .container { padding: 15px; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            ul { padding: 10px; }
            .btn { display: block; text-align: center; margin: 10px 0; }
        }
        
        @media (max-width: 480px) {
            body { margin: 5px; }
            .container { padding: 10px; }
            h2 { font-size: 1.25rem; }
            h3 { font-size: 1.125rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔧 cPanel Basic Test</h2>
<?php

// Test 1: PHP Version
echo "<h3>1. PHP Environment:</h3>";
echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
echo "<li><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li><strong>Current Directory:</strong> " . __DIR__ . "</li>";
echo "</ul>";

// Test 2: File Existence
echo "<h3>2. Required Files:</h3>";
echo "<ul>";
$files = ['env-loader.php', '.env', 'config.php', 'meta-dashboard.php'];
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<li><strong>$file:</strong> " . ($exists ? "✅ Found" : "❌ Missing") . "</li>";
}
echo "</ul>";

// Test 3: .env Loading
echo "<h3>3. Environment Variables Test:</h3>";
try {
    if (file_exists(__DIR__ . '/env-loader.php')) {
        require_once 'env-loader.php';
        echo "<p>✅ env-loader.php loaded successfully</p>";
        
        // Test some env variables
        $testVars = [
            'FACEBOOK_ACCESS_TOKEN' => 'Facebook Access Token',
            'FACEBOOK_APP_SECRET' => 'Facebook App Secret',
            'DB_HOST' => 'Database Host',
            'DB_NAME' => 'Database Name'
        ];
        
        echo "<ul>";
        foreach ($testVars as $var => $name) {
            $value = env($var, '');
            $status = empty($value) ? "❌ Missing/Empty" : "✅ Present (" . substr($value, 0, 10) . "...)";
            echo "<li><strong>$name:</strong> $status</li>";
        }
        echo "</ul>";
        
    } else {
        echo "<p>❌ env-loader.php not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error loading environment: " . $e->getMessage() . "</p>";
}

// Test 4: cURL Support
echo "<h3>4. cURL Support (for API calls):</h3>";
if (function_exists('curl_init')) {
    echo "<p>✅ cURL is available</p>";
    
    // Test basic HTTP request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://httpbin.org/get',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response && $httpCode === 200) {
        echo "<p>✅ HTTP requests working (Test API call successful)</p>";
    } else {
        echo "<p>❌ HTTP request failed: $error (Code: $httpCode)</p>";
    }
} else {
    echo "<p>❌ cURL is not available - API calls will fail</p>";
}

// Test 5: JSON Support
echo "<h3>5. JSON Support:</h3>";
if (function_exists('json_encode') && function_exists('json_decode')) {
    echo "<p>✅ JSON functions available</p>";
} else {
    echo "<p>❌ JSON functions missing</p>";
}

echo "<br><hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If all tests pass ✅, try <a href='test-meta-api.php'>test-meta-api.php</a></li>";
echo "<li>If tests fail ❌, check the specific error messages above</li>";
echo "<li>Then try <a href='meta-dashboard.php'>meta-dashboard.php</a></li>";
echo "</ul>";
?>
    </div>
</body>
</html>
