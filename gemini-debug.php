<?php
/**
 * Gemini API Debug Script for cPanel Troubleshooting
 * Upload this file to your cPanel and run it to diagnose issues
 */

// Load configuration
require_once 'config.php';

echo "<h1>Gemini API Debug Report - InfinityFree Hosting</h1>";
echo "<div style='background:#fff3cd;padding:10px;border-radius:5px;margin:10px 0;'><strong>⚠️ InfinityFree Hosting Detected:</strong> This hosting provider blocks external API calls. See solutions below.</div>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

// 1. Check Environment Variables
echo "<h2>1. Environment Variables Check</h2>";
echo "<strong>Environment file exists:</strong> " . (file_exists(__DIR__ . '/.env') ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>') . "<br>";

if (file_exists(__DIR__ . '/.env')) {
    echo "<strong>Environment file readable:</strong> " . (is_readable(__DIR__ . '/.env') ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>') . "<br>";
    echo "<strong>Environment file permissions:</strong> " . substr(sprintf('%o', fileperms(__DIR__ . '/.env')), -4) . "<br>";
}

echo "<h3>Gemini API Keys Status:</h3>";
$apiKeys = GEMINI_API_KEYS;
foreach ($apiKeys as $index => $key) {
    $keyName = "GEMINI_API_KEY_" . ($index + 1);
    $envValue = env($keyName, 'NOT_SET');
    echo "<strong>$keyName:</strong> " . ($envValue !== 'NOT_SET' ? '<span class="success">✓ SET</span>' : '<span class="error">✗ NOT SET</span>') . "<br>";
    echo "<strong>Key Value:</strong> " . (strlen($key) > 10 ? substr($key, 0, 10) . '...' . substr($key, -10) : $key) . "<br><br>";
}

// 2. Check PHP Configuration
echo "<h2>2. PHP Configuration</h2>";
echo "<strong>allow_url_fopen:</strong> " . (ini_get('allow_url_fopen') ? '<span class="success">✓ ENABLED</span>' : '<span class="error">✗ DISABLED</span>') . "<br>";
echo "<strong>cURL extension:</strong> " . (extension_loaded('curl') ? '<span class="success">✓ LOADED</span>' : '<span class="error">✗ NOT LOADED</span>') . "<br>";
echo "<strong>OpenSSL extension:</strong> " . (extension_loaded('openssl') ? '<span class="success">✓ LOADED</span>' : '<span class="error">✗ NOT LOADED</span>') . "<br>";
echo "<strong>JSON extension:</strong> " . (extension_loaded('json') ? '<span class="success">✓ LOADED</span>' : '<span class="error">✗ NOT LOADED</span>') . "<br>";

// 3. Test Network Connectivity
echo "<h2>3. Network Connectivity Test</h2>";

if (extension_loaded('curl')) {
    $testUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<strong>Google API Endpoint Reachable:</strong> ";
    if ($error) {
        echo '<span class="error">✗ ERROR: ' . $error . '</span><br>';
    } else {
        echo '<span class="success">✓ YES (HTTP ' . $httpCode . ')</span><br>';
    }
} else {
    echo '<span class="error">Cannot test - cURL not available</span><br>';
}

// 4. Test Gemini API Call
echo "<h2>4. Gemini API Test</h2>";

if (extension_loaded('curl') && !empty($apiKeys[0])) {
    $apiKey = $apiKeys[0];
    $url = GEMINI_API_URL . "?key=" . $apiKey;
    
    $testData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Hello, respond with just "API Working" if you receive this message.']
                ]
            ]
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<strong>API Test Result:</strong><br>";
    echo "<strong>HTTP Code:</strong> " . $httpCode . "<br>";
    
    if ($error) {
        echo '<span class="error">cURL Error: ' . $error . '</span><br>';
    } else {
        if ($httpCode == 200) {
            echo '<span class="success">✓ API Call Successful</span><br>';
            $responseData = json_decode($response, true);
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                echo "<strong>API Response:</strong> " . $responseData['candidates'][0]['content']['parts'][0]['text'] . "<br>";
            }
        } else {
            echo '<span class="error">✗ API Call Failed</span><br>';
        }
    }
    
    echo "<h3>Full Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} else {
    echo '<span class="error">Cannot test - Missing requirements</span><br>';
}

// 5. Server Information
echo "<h2>5. Server Information</h2>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Current Directory:</strong> " . __DIR__ . "<br>";
echo "<strong>User Agent:</strong> " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Not set') . "<br>";

// 6. File Permissions Check
echo "<h2>6. File Permissions</h2>";
$files = ['.env', 'config.php', 'env-loader.php', 'AudioAnalyzer.php'];
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<strong>$file:</strong> " . substr(sprintf('%o', fileperms($path)), -4) . " ";
        echo (is_readable($path) ? '<span class="success">(readable)</span>' : '<span class="error">(not readable)</span>') . "<br>";
    } else {
        echo "<strong>$file:</strong> <span class="error">File not found</span><br>";
    }
}

echo "<hr>";
echo "<h2>🚨 InfinityFree Hosting Solutions</h2>";
echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;margin:10px 0;'>";
echo "<h3>❌ Why Gemini API Doesn't Work on InfinityFree:</h3>";
echo "<ul>";
echo "<li><strong>External API Blocking:</strong> InfinityFree blocks outbound requests to external APIs like Google's Gemini</li>";
echo "<li><strong>Security Restrictions:</strong> Free hosting providers restrict external connections for security</li>";
echo "<li><strong>Resource Limits:</strong> CPU/memory limits may timeout API requests</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#d4edda;padding:15px;border-radius:5px;margin:10px 0;'>";
echo "<h3>✅ Recommended Solutions:</h3>";
echo "<ol>";
echo "<li><strong>Upgrade to Premium Hosting:</strong>";
echo "<ul><li>Use paid hosting that allows external API calls</li>";
echo "<li>Consider: Hostinger, Namecheap, SiteGround, or DigitalOcean</li></ul></li>";

echo "<li><strong>Use a Proxy/Bridge Server:</strong>";
echo "<ul><li>Set up a simple proxy on a VPS that forwards requests to Gemini API</li>";
echo "<li>Your InfinityFree site calls your proxy, proxy calls Gemini</li></ul></li>";

echo "<li><strong>Client-Side Processing:</strong>";
echo "<ul><li>Use JavaScript to call Gemini API directly from user's browser</li>";
echo "<li>Requires exposing API key (security risk)</li></ul></li>";

echo "<li><strong>Alternative Free Hosting:</strong>";
echo "<ul><li>Try: Vercel, Netlify, or Railway (better for API calls)</li>";
echo "<li>GitHub Pages + Serverless functions</li></ul></li>";
echo "</ol>";
echo "</div>";

echo "<div style='background:#e2e3e5;padding:15px;border-radius:5px;margin:10px 0;'>";
echo "<h3>🔧 Quick Test - Alternative Hosting Options:</h3>";
echo "<ul>";
echo "<li><strong>Vercel:</strong> Free tier, excellent for PHP/API projects</li>";
echo "<li><strong>Railway:</strong> Free $5/month credit, supports PHP</li>";
echo "<li><strong>PlanetScale + Vercel:</strong> Free database + hosting combo</li>";
echo "<li><strong>Heroku:</strong> Free tier (limited hours)</li>";
echo "</ul>";
echo "</div>";

echo "<p><strong>Next Steps for InfinityFree Users:</strong></p>";
echo "<ul>";
echo "<li>❌ <strong>Don't waste time</strong> trying to make external APIs work on InfinityFree</li>";
echo "<li>✅ <strong>Migrate to paid hosting</strong> ($3-5/month) for full API access</li>";
echo "<li>✅ <strong>Use the proxy solution</strong> if you must stay on free hosting</li>";
echo "<li>✅ <strong>Test on Vercel/Netlify</strong> for free alternatives with API support</li>";
echo "</ul>";
?>
