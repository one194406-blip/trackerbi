<?php
// Simple test version of audio analysis page
echo "<!DOCTYPE html>";
echo "<html><head><title>Audio Analysis Test</title></head>";
echo "<body>";
echo "<h1>Audio Analysis Page Test</h1>";
echo "<p>If you can see this, the basic PHP is working.</p>";

// Test if files exist
$files_to_check = [
    'AudioAnalyzer.php',
    'config.php', 
    'env-loader.php',
    'ErrorHandler.php',
    'DatabaseManager.php',
    'includes/header.php',
    'includes/footer.php'
];

echo "<h2>File Existence Check:</h2>";
echo "<ul>";
foreach ($files_to_check as $file) {
    $exists = file_exists($file);
    $status = $exists ? "✅ EXISTS" : "❌ MISSING";
    echo "<li>$file: $status</li>";
}
echo "</ul>";

// Test basic includes
echo "<h2>Include Test:</h2>";
try {
    echo "<p>Testing env-loader.php...</p>";
    require_once 'env-loader.php';
    echo "<p>✅ env-loader.php loaded successfully</p>";
    
    echo "<p>Testing config.php...</p>";
    require_once 'config.php';
    echo "<p>✅ config.php loaded successfully</p>";
    
    echo "<p>Testing ErrorHandler.php...</p>";
    require_once 'ErrorHandler.php';
    echo "<p>✅ ErrorHandler.php loaded successfully</p>";
    
    echo "<p>Testing DatabaseManager.php...</p>";
    require_once 'DatabaseManager.php';
    echo "<p>✅ DatabaseManager.php loaded successfully</p>";
    
    echo "<p>Testing AudioAnalyzer.php...</p>";
    require_once 'AudioAnalyzer.php';
    echo "<p>✅ AudioAnalyzer.php loaded successfully</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
