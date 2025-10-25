<?php
/**
 * Auto-redirect Meta Dashboard
 * Automatically detects SSL issues and redirects to demo mode
 */

// Quick SSL test
function testFacebookSSL() {
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
    curl_close($ch);
    
    return empty($error) || strpos($error, 'SSL_ERROR_SYSCALL') === false;
}

// Check if we should force demo mode
$forceDemo = !testFacebookSSL();

if ($forceDemo && !isset($_GET['demo']) && !isset($_GET['force_live'])) {
    // Redirect to demo mode
    header('Location: meta-dashboard.php?demo=1&auto_redirect=1');
    exit;
} else {
    // Include the regular dashboard
    include 'meta-dashboard.php';
}
?>
