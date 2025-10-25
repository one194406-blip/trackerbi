<?php
/**
 * Test Meta/Facebook API Credentials
 * Checks if your Facebook API tokens are working correctly
 */

// Start session for cPanel compatibility
session_start();

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'env-loader.php';

echo "<h2>🔍 Testing Meta/Facebook API Credentials</h2>";

// Get credentials from .env
$ACCESS_TOKEN = env('FACEBOOK_ACCESS_TOKEN', '');
$APP_SECRET = env('FACEBOOK_APP_SECRET', '');
$APP_ID = env('FACEBOOK_APP_ID', '');

$HARISHSHOPPY_TOKEN = env('FACEBOOK_PAGE_HARISHSHOPPY_TOKEN', '');
$ADAMANDEVE_TOKEN = env('FACEBOOK_PAGE_ADAMANDEVE_TOKEN', '');

echo "<h3>📋 Credential Status:</h3>";
echo "<ul>";
echo "<li><strong>Main Access Token:</strong> " . (empty($ACCESS_TOKEN) ? "❌ Missing" : "✅ Present (" . substr($ACCESS_TOKEN, 0, 20) . "...)") . "</li>";
echo "<li><strong>App Secret:</strong> " . (empty($APP_SECRET) ? "❌ Missing" : "✅ Present") . "</li>";
echo "<li><strong>App ID:</strong> " . (empty($APP_ID) ? "❌ Missing" : "✅ Present ($APP_ID)") . "</li>";
echo "<li><strong>Harishshoppy Token:</strong> " . (empty($HARISHSHOPPY_TOKEN) ? "❌ Missing" : "✅ Present (" . substr($HARISHSHOPPY_TOKEN, 0, 20) . "...)") . "</li>";
echo "<li><strong>Adamandeve Token:</strong> " . (empty($ADAMANDEVE_TOKEN) ? "❌ Missing" : "✅ Present (" . substr($ADAMANDEVE_TOKEN, 0, 20) . "...)") . "</li>";
echo "</ul>";

// Test function
function testFacebookAPI($token, $name) {
    if (empty($token)) {
        return "<span style='color: red;'>❌ No token provided</span>";
    }
    
    $url = "https://graph.facebook.com/v22.0/me?access_token=" . urlencode($token);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($response === false || !empty($curlError)) {
        return "<span style='color: red;'>❌ Connection Error: $curlError</span>";
    }
    
    $data = json_decode($response, true);
    
    if ($httpCode === 200 && isset($data['id'])) {
        return "<span style='color: green;'>✅ Valid - ID: {$data['id']}" . (isset($data['name']) ? ", Name: {$data['name']}" : "") . "</span>";
    } else {
        $error = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';
        return "<span style='color: red;'>❌ Error: $error (HTTP: $httpCode)</span>";
    }
}

echo "<h3>🧪 API Connection Tests:</h3>";
echo "<ul>";
echo "<li><strong>Main Access Token:</strong> " . testFacebookAPI($ACCESS_TOKEN, 'Main Token') . "</li>";
echo "<li><strong>Harishshoppy Page Token:</strong> " . testFacebookAPI($HARISHSHOPPY_TOKEN, 'Harishshoppy') . "</li>";
echo "<li><strong>Adamandeve Page Token:</strong> " . testFacebookAPI($ADAMANDEVE_TOKEN, 'Adamandeve') . "</li>";
echo "</ul>";

// Test Ad Account Access
if (!empty($ACCESS_TOKEN)) {
    echo "<h3>📊 Ad Account Access Test:</h3>";
    
    $adAccounts = [
        'act_837599231811269' => 'Instagram',
        'act_782436794463558' => 'Facebook'
    ];
    
    echo "<ul>";
    foreach ($adAccounts as $accountId => $accountName) {
        $url = "https://graph.facebook.com/v22.0/$accountId?fields=name,account_status&access_token=" . urlencode($ACCESS_TOKEN);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['id'])) {
            $status = isset($data['account_status']) ? $data['account_status'] : 'unknown';
            $name = isset($data['name']) ? $data['name'] : $accountName;
            echo "<li><strong>$accountName ($accountId):</strong> <span style='color: green;'>✅ Accessible - $name (Status: $status)</span></li>";
        } else {
            $error = isset($data['error']['message']) ? $data['error']['message'] : 'Access denied';
            echo "<li><strong>$accountName ($accountId):</strong> <span style='color: red;'>❌ $error</span></li>";
        }
    }
    echo "</ul>";
}

echo "<h3>💡 Troubleshooting Tips:</h3>";
echo "<ul>";
echo "<li>If tokens show as <strong>expired</strong>, you need to regenerate them in Facebook Developer Console</li>";
echo "<li>If <strong>permissions</strong> errors occur, check if your app has the required permissions</li>";
echo "<li>If <strong>connection</strong> errors occur, check your server's internet connection</li>";
echo "<li>Make sure your <strong>.env file</strong> has the correct token values</li>";
echo "</ul>";

echo "<br><p><a href='meta-dashboard.php'>🔙 Back to Meta Dashboard</a></p>";
?>
