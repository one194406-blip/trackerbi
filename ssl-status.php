<?php
/**
 * Quick SSL Status Check
 * Returns JSON status for AJAX calls
 */

header('Content-Type: application/json');

function testSSL() {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://graph.facebook.com/v22.0/me?access_token=test',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $result = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'working' => empty($error) && $result !== false,
        'error' => $error,
        'http_code' => $httpCode,
        'ssl_error' => strpos($error, 'SSL_ERROR_SYSCALL') !== false
    ];
}

$status = testSSL();
echo json_encode($status);
?>
