<?php
echo "<h2>Simple Test</h2>";

try {
    echo "<p>1. Testing config.php...</p>";
    require_once 'config.php';
    echo "<p>✅ Config loaded</p>";
    
    echo "<p>2. Testing DatabaseManager...</p>";
    require_once 'DatabaseManager.php';
    echo "<p>✅ DatabaseManager loaded</p>";
    
    echo "<p>3. Testing database connection...</p>";
    $dbManager = new DatabaseManager();
    echo "<p>✅ DatabaseManager created</p>";
    
    echo "<p>4. Testing connection...</p>";
    $test = $dbManager->testConnection();
    if ($test['success']) {
        echo "<p>✅ Database connection working</p>";
    } else {
        echo "<p>❌ Database connection failed: " . htmlspecialchars($test['error']) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>
