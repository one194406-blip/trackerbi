<?php
/**
 * Test Database Connection for InfinityFree
 * This script tests the enhanced DatabaseManager with connection recovery
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

echo "<h2>🔍 Testing Database Connection for InfinityFree</h2>";

try {
    echo "<p>📡 Initializing DatabaseManager...</p>";
    $dbManager = new DatabaseManager();
    echo "<p>✅ DatabaseManager initialized successfully</p>";
    
    echo "<p>🔍 Testing basic connection...</p>";
    $summary = $dbManager->getAnalyticsSummary();
    
    if ($summary) {
        echo "<p>✅ Database connection working! Found {$summary['total_analyses']} total analyses</p>";
    } else {
        echo "<p>⚠️ Database connected but no data found (this is normal for new installations)</p>";
    }
    
    echo "<p>📊 Testing recent results query...</p>";
    $recentResults = $dbManager->getRecentResults(5);
    echo "<p>✅ Recent results query successful - found " . count($recentResults) . " results</p>";
    
    echo "<p>📈 Testing performance trends query...</p>";
    $trends = $dbManager->getPerformanceTrends(7);
    echo "<p>✅ Performance trends query successful - found " . count($trends) . " trend days</p>";
    
    echo "<h3>🎉 All Database Tests Passed!</h3>";
    echo "<p><strong>Your database connection is working properly with InfinityFree optimizations.</strong></p>";
    
    echo "<h4>📋 Connection Details:</h4>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
    echo "<li><strong>Database:</strong> " . DB_NAME . "</li>";
    echo "<li><strong>Connection Timeout:</strong> 30 seconds</li>";
    echo "<li><strong>Session Timeout:</strong> 300 seconds</li>";
    echo "<li><strong>Persistent Connections:</strong> Disabled (InfinityFree optimized)</li>";
    echo "<li><strong>Auto-Reconnect:</strong> Enabled</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Database Connection Failed:</strong> " . $e->getMessage() . "</p>";
    
    echo "<h4>🔧 Troubleshooting Steps:</h4>";
    echo "<ol>";
    echo "<li>Check your database credentials in config.php</li>";
    echo "<li>Ensure your InfinityFree database is active</li>";
    echo "<li>Verify the database name matches exactly (case-sensitive)</li>";
    echo "<li>Check if your InfinityFree account has database access enabled</li>";
    echo "<li>Try accessing your database through InfinityFree's phpMyAdmin</li>";
    echo "</ol>";
    
    echo "<h4>📝 Error Details:</h4>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Main Dashboard</a></p>";
?>
