<?php
/**
 * Meta Dashboard Debug - Find out why data isn't loading
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meta Debug - TrackerBI</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        .info { color: #3b82f6; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body { margin: 10px; }
            .container { padding: 15px; }
            h1 { font-size: 1.5rem; }
            h2 { font-size: 1.25rem; }
            pre { font-size: 0.875rem; padding: 8px; }
            .btn { display: block; text-align: center; margin: 10px 0; }
        }
        
        @media (max-width: 480px) {
            body { margin: 5px; }
            .container { padding: 10px; }
            h1 { font-size: 1.25rem; }
            h2 { font-size: 1.125rem; }
            pre { font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Meta Dashboard Debug</h1>
<?php

// Test 1: Environment loading
echo "<h2>1. Environment Test</h2>";
try {
    require_once 'env-loader.php';
    echo "✅ env-loader.php loaded<br>";
    
    $ACCESS_TOKEN = env('FACEBOOK_ACCESS_TOKEN', '');
    $APP_SECRET = env('FACEBOOK_APP_SECRET', '');
    
    echo "Access Token: " . (!empty($ACCESS_TOKEN) ? "✅ Present (" . substr($ACCESS_TOKEN, 0, 20) . "...)" : "❌ Missing") . "<br>";
    echo "App Secret: " . (!empty($APP_SECRET) ? "✅ Present" : "❌ Missing") . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Facebook API Test
echo "<h2>2. Facebook API Test</h2>";
if (!empty($ACCESS_TOKEN)) {
    $url = "https://graph.facebook.com/v22.0/me?access_token=" . urlencode($ACCESS_TOKEN);
    
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
        echo "❌ cURL Error: $curlError<br>";
    } else {
        echo "HTTP Code: $httpCode<br>";
        $data = json_decode($response, true);
        if ($httpCode === 200 && isset($data['id'])) {
            echo "✅ API Working - User ID: {$data['id']}<br>";
        } else {
            echo "❌ API Error: " . ($data['error']['message'] ?? 'Unknown error') . "<br>";
            echo "Response: " . htmlspecialchars($response) . "<br>";
        }
    }
} else {
    echo "❌ No access token to test<br>";
}

// Test 3: Ad Account Test
echo "<h2>3. Ad Account Test</h2>";
if (!empty($ACCESS_TOKEN)) {
    $adAccounts = ['act_837599231811269', 'act_782436794463558'];
    
    foreach ($adAccounts as $accountId) {
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
            $status = $data['account_status'] ?? 'unknown';
            $name = $data['name'] ?? 'Unknown';
            echo "✅ $accountId: $name (Status: $status)<br>";
        } else {
            $error = $data['error']['message'] ?? 'Access denied';
            echo "❌ $accountId: $error<br>";
        }
    }
} else {
    echo "❌ No access token to test ad accounts<br>";
}

// Test 4: Show what the original dashboard is doing
echo "<h2>4. Dashboard Logic Test</h2>";
if (!empty($ACCESS_TOKEN) && !empty($APP_SECRET)) {
    echo "✅ Tokens available - Dashboard should show real data<br>";
    
    // Test the actual API call the dashboard makes
    $ad = 'act_837599231811269'; // Default ad account
    $appsecret_proof = hash_hmac('sha256', $ACCESS_TOKEN, $APP_SECRET);
    
    $url = "https://graph.facebook.com/v22.0/$ad/campaigns?fields=id,name,status&limit=5&access_token=" . urlencode($ACCESS_TOKEN) . "&appsecret_proof=" . urlencode($appsecret_proof);
    
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
    
    echo "Campaign API Test:<br>";
    echo "HTTP Code: $httpCode<br>";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['data']) && is_array($data['data'])) {
            echo "✅ Found " . count($data['data']) . " campaigns<br>";
            foreach ($data['data'] as $campaign) {
                echo "- " . ($campaign['name'] ?? 'Unnamed') . " (" . ($campaign['status'] ?? 'unknown') . ")<br>";
            }
        } else {
            echo "❌ No campaign data found<br>";
        }
    } else {
        $data = json_decode($response, true);
        echo "❌ Error: " . ($data['error']['message'] ?? 'Unknown error') . "<br>";
    }
    
} else {
    echo "❌ Missing tokens - Dashboard will show demo mode<br>";
}

echo "<h2>5. Solutions</h2>";
echo "<ul>";
echo "<li>If API tests fail: Check if tokens are expired in Facebook Developer Console</li>";
echo "<li>If ad account tests fail: Check permissions for your app</li>";
echo "<li>If everything passes but dashboard is blank: Check JavaScript console for errors</li>";
echo "<li>Try the simple dashboard: <a href='meta-simple.php'>meta-simple.php</a></li>";
echo "</ul>";

echo "<br><a href='meta-dashboard.php' class='btn'>🔙 Back to Meta Dashboard</a>";
?>
    </div>
</body>
</html>
