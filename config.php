<?php
/**
 * Configuration file for Audio Analysis System
 */

// Load environment variables
require_once __DIR__ . '/env-loader.php';

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USERNAME', env('DB_USERNAME', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', ''));
define('DB_NAME', env('DB_NAME', 'trackerbi_audio'));

// Gemini API Configuration
define('GEMINI_API_KEYS', [
    env('GEMINI_API_KEY_1', 'AIzaSyDIN5tcxE0hi04tPWdf_nVsiVPF9a5C9LA'),
    env('GEMINI_API_KEY_2', 'AIzaSyAiDU2YQRRHpz-ex_v91OHlaW0aIKg8gJE'),
    env('GEMINI_API_KEY_3', 'AIzaSyD6-IS8i1bEif5OtqoTmGlyPBQrWLAgaI0')
]);

// Available Gemini Models (uncomment the one you want to use)
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent');           // Gemini 1.5 Flash (Stable)
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-002:generateContent');       // Gemini 1.5 Flash Latest
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent');             // Gemini 1.5 Pro
define('GEMINI_API_URL', env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'));           // Gemini 2.0 Flash (Stable) - CURRENT
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent');        // Gemini 2.0 Flash (Experimental)
// define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-thinking-exp:generateContent'); // Gemini 2.0 Flash Thinking

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/' . env('UPLOAD_DIR', 'uploads/'));
define('MAX_FILE_SIZE', env('MAX_FILE_SIZE', 50 * 1024 * 1024)); // 50MB
define('ALLOWED_AUDIO_TYPES', [
    'audio/mpeg',
    'audio/mp3',
    'audio/wav',
    'audio/x-wav',
    'audio/wave',
    'audio/x-pn-wav',
    'audio/mp4',
    'audio/m4a',
    'audio/x-m4a',
    'audio/aac',
    'audio/ogg',
    'audio/x-ogg',
    'audio/webm',
    'audio/flac',
    'audio/x-flac'
]);

// Error Logging
define('LOG_FILE', __DIR__ . '/logs/audio_analysis.log');

// Create necessary directories
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (!file_exists(dirname(LOG_FILE))) {
    mkdir(dirname(LOG_FILE), 0755, true);
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', LOG_FILE);

// Set timezone
date_default_timezone_set('UTC');
