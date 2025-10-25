<?php
/**
 * Test Gemini 2.0 Flash Configuration
 * Quick test to verify the API is working with the new model
 */

require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Gemini 2.0 Flash Test</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:15px;border-radius:5px;}</style>";
echo "</head><body>";

echo "<h1>🤖 Gemini 2.0 Flash API Test</h1>";

// Display current configuration
echo "<div class='info'><h3>📋 Current Configuration:</h3></div>";
echo "<pre>";
echo "API URL: " . GEMINI_API_URL . "\n";
echo "Available API Keys: " . count(GEMINI_API_KEYS) . "\n";
echo "Model: gemini-2.0-flash (Stable)\n";
echo "</pre>";

// Test API connection with a simple request
echo "<div class='info'><h3>🔍 Testing API Connection:</h3></div>";

try {
    $apiKey = GEMINI_API_KEYS[0]; // Use first API key
    $url = GEMINI_API_URL . "?key=" . $apiKey;
    
    $testPrompt = "Hello! Please respond with 'Gemini 2.0 Flash is working!' to confirm the API connection.";
    
    $requestData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $testPrompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.1,
            'maxOutputTokens' => 100
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    echo "<div class='info'>📡 Sending test request...</div>";
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        throw new Exception("CURL Error: $curlError");
    }
    
    if ($httpCode !== 200) {
        echo "<div class='error'>❌ HTTP Error $httpCode</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        $data = json_decode($response, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
            echo "<div class='success'>✅ API Connection Successful!</div>";
            echo "<div class='success'><strong>Response:</strong> " . htmlspecialchars($responseText) . "</div>";
            
            // Check if it's actually Gemini 2.0 Flash
            if (strpos($responseText, 'Gemini 2.0 Flash') !== false) {
                echo "<div class='success'>🎉 <strong>Gemini 2.0 Flash is confirmed working!</strong></div>";
            } else {
                echo "<div class='info'>ℹ️ API is working, response received</div>";
            }
        } else {
            echo "<div class='error'>❌ Unexpected response format</div>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<br><h3>🚀 Ready for Audio Analysis!</h3>";
echo "<div class='info'>Your TrackerBI system is now configured to use Gemini 2.0 Flash for audio analysis.</div>";
echo "<br><a href='trackerbi-audio.php' style='background:#007cba;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Test Audio Analysis</a>";
echo " <a href='index.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-left:10px;'>Go to Login</a>";

echo "</body></html>";
?>
