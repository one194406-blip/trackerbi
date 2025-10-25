<?php
/**
 * Gemini API Proxy for InfinityFree Hosting
 * Deploy this on a service that allows external API calls (Vercel, Railway, etc.)
 * Your InfinityFree site will call this proxy instead of Gemini directly
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Validate required fields
if (!isset($data['api_key']) || !isset($data['model']) || !isset($data['contents'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: api_key, model, contents']);
    exit;
}

$apiKey = $data['api_key'];
$model = $data['model'];
$contents = $data['contents'];

// Construct Gemini API URL
$geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

// Prepare request data for Gemini
$requestData = [
    'contents' => $contents
];

// Add generation config if provided
if (isset($data['generationConfig'])) {
    $requestData['generationConfig'] = $data['generationConfig'];
}

// Add safety settings if provided
if (isset($data['safetySettings'])) {
    $requestData['safetySettings'] = $data['safetySettings'];
}

// Make request to Gemini API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geminiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'Proxy error: ' . $error]);
    exit;
}

// Return the response with the same HTTP code
http_response_code($httpCode);
echo $response;
?>
