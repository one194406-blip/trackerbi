<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'AudioAnalyzer.php';

/**
 * API endpoint for audio analysis
 * Accepts POST requests with audio file uploads
 */

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

function logApiRequest($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [API] [$level] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse([
            'success' => false,
            'error' => 'Only POST requests are allowed',
            'usage' => [
                'method' => 'POST',
                'content_type' => 'multipart/form-data',
                'required_field' => 'audio_file',
                'supported_formats' => ['mp3', 'wav', 'm4a', 'ogg', 'webm'],
                'max_file_size' => '50MB'
            ]
        ], 405);
    }

    // Check if audio file is provided
    if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        $errorCode = $_FILES['audio_file']['error'] ?? UPLOAD_ERR_NO_FILE;
        $errorMessage = $errorMessages[$errorCode] ?? 'Unknown upload error';
        
        logApiRequest("Upload error: $errorMessage", 'ERROR');
        
        sendJsonResponse([
            'success' => false,
            'error' => $errorMessage,
            'error_code' => $errorCode
        ], 400);
    }

    logApiRequest("API request received for file: " . $_FILES['audio_file']['name']);

    // Initialize analyzer and process audio
    $analyzer = new AudioAnalyzer();
    $results = $analyzer->processAudio($_FILES['audio_file']);

    // Format response
    $response = [
        'success' => empty($results['errors']),
        'timestamp' => date('c'),
        'processing_time' => null, // Could be calculated if needed
        'data' => []
    ];

    // Add errors if any
    if (!empty($results['errors'])) {
        $response['errors'] = $results['errors'];
        logApiRequest("API request completed with errors: " . implode(', ', $results['errors']), 'ERROR');
    } else {
        logApiRequest("API request completed successfully");
    }

    // Add successful results
    if ($results['upload'] && $results['upload']['success']) {
        $response['data']['upload_info'] = [
            'filename' => $results['upload']['filename'],
            'size_bytes' => $results['upload']['size'],
            'mime_type' => $results['upload']['mime_type']
        ];
    }

    if ($results['transcription'] && $results['transcription']['success']) {
        $response['data']['transcription'] = $results['transcription']['transcription'];
    }

    if ($results['translation'] && $results['translation']['success']) {
        $response['data']['translation'] = $results['translation']['translation'];
    }

    if ($results['sentiment_analysis'] && $results['sentiment_analysis']['success']) {
        $response['data']['sentiment_analysis'] = $results['sentiment_analysis']['analysis'];
    }

    sendJsonResponse($response);

} catch (Exception $e) {
    logApiRequest("API exception: " . $e->getMessage(), 'ERROR');
    
    sendJsonResponse([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'timestamp' => date('c')
    ], 500);
} catch (Error $e) {
    logApiRequest("API fatal error: " . $e->getMessage(), 'FATAL');
    
    sendJsonResponse([
        'success' => false,
        'error' => 'Fatal server error',
        'message' => $e->getMessage(),
        'timestamp' => date('c')
    ], 500);
}
