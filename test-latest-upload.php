<?php
/**
 * Test Latest Upload Order
 * Check if the most recent upload appears first
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

$dbManager = new DatabaseManager();

echo "<h2>🔍 Test Latest Upload Order</h2>";

try {
    echo "<h3>1. 📊 Raw Database Order (Last 5 records)</h3>";
    
    // Direct database query to see raw order
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD
    );
    
    $stmt = $connection->query("
        SELECT id, session_id, filename, upload_timestamp, created_at, processing_status,
               COALESCE(upload_timestamp, created_at) as effective_timestamp
        FROM audio_analysis_results 
        ORDER BY 
            CASE WHEN upload_timestamp IS NOT NULL THEN upload_timestamp ELSE created_at END DESC,
            id DESC
        LIMIT 5
    ");
    
    $rawResults = $stmt->fetchAll();
    
    if (!empty($rawResults)) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>Order</th><th>ID</th><th>Filename</th><th>Upload Time</th><th>Created Time</th><th>Effective Time</th><th>Status</th>";
        echo "</tr>";
        
        foreach ($rawResults as $index => $result) {
            $bgColor = $index === 0 ? 'background: #e7f3ff;' : '';
            echo "<tr style='$bgColor'>";
            echo "<td><strong>" . ($index + 1) . ($index === 0 ? ' (SHOULD BE LATEST)' : '') . "</strong></td>";
            echo "<td>" . $result['id'] . "</td>";
            echo "<td>" . htmlspecialchars($result['filename']) . "</td>";
            echo "<td>" . ($result['upload_timestamp'] ?? 'NULL') . "</td>";
            echo "<td>" . ($result['created_at'] ?? 'NULL') . "</td>";
            echo "<td>" . ($result['effective_timestamp'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($result['processing_status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No records found in database</p>";
    }
    
    echo "<h3>2. 📈 DatabaseManager getRecentResults()</h3>";
    
    $recentResults = $dbManager->getRecentResults(5);
    
    if (!empty($recentResults)) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>Order</th><th>Session ID</th><th>Filename</th><th>Upload Time</th>";
        echo "</tr>";
        
        foreach ($recentResults as $index => $result) {
            $bgColor = $index === 0 ? 'background: #e7f3ff;' : '';
            echo "<tr style='$bgColor'>";
            echo "<td><strong>" . ($index + 1) . ($index === 0 ? ' (LATEST FROM METHOD)' : '') . "</strong></td>";
            echo "<td>" . htmlspecialchars($result['session_id']) . "</td>";
            echo "<td>" . htmlspecialchars($result['filename']) . "</td>";
            echo "<td>" . ($result['upload_timestamp'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ getRecentResults() returned no data</p>";
    }
    
    echo "<h3>3. 📊 Performance Trends (Latest Day)</h3>";
    
    $performanceTrends = $dbManager->getPerformanceTrends(7);
    
    if (!empty($performanceTrends)) {
        $latestDay = $performanceTrends[0];
        echo "<h4>Latest Day: " . $latestDay['analysis_date'] . " (" . $latestDay['daily_count'] . " analyses)</h4>";
        
        if (!empty($latestDay['parsed_details'])) {
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f5f5f5;'>";
            echo "<th>Order</th><th>Filename</th><th>Upload Time</th><th>Session ID</th>";
            echo "</tr>";
            
            foreach ($latestDay['parsed_details'] as $index => $detail) {
                $bgColor = $index === 0 ? 'background: #e7f3ff;' : '';
                echo "<tr style='$bgColor'>";
                echo "<td><strong>" . ($index + 1) . ($index === 0 ? ' (LATEST IN DAY)' : '') . "</strong></td>";
                echo "<td>" . htmlspecialchars($detail['filename']) . "</td>";
                echo "<td>" . ($detail['upload_timestamp'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($detail['session_id']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ getPerformanceTrends() returned no data</p>";
    }
    
    echo "<h3>4. 💡 Diagnosis</h3>";
    
    if (!empty($rawResults)) {
        $latestRecord = $rawResults[0];
        echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
        echo "<h4>🔍 Latest Record Analysis:</h4>";
        echo "<ul>";
        echo "<li><strong>Latest Record ID:</strong> " . $latestRecord['id'] . "</li>";
        echo "<li><strong>Filename:</strong> " . htmlspecialchars($latestRecord['filename']) . "</li>";
        echo "<li><strong>Upload Time:</strong> " . ($latestRecord['upload_timestamp'] ?? 'NULL') . "</li>";
        echo "<li><strong>Status:</strong> " . htmlspecialchars($latestRecord['processing_status']) . "</li>";
        echo "</ul>";
        
        if ($latestRecord['processing_status'] !== 'completed') {
            echo "<p style='color: red;'>⚠️ <strong>Issue:</strong> Latest record status is '" . htmlspecialchars($latestRecord['processing_status']) . "' not 'completed'</p>";
            echo "<p>This means it won't appear in analytics dashboard because queries filter for processing_status = 'completed'</p>";
        } else {
            echo "<p style='color: green;'>✅ Latest record has 'completed' status - should appear in dashboard</p>";
        }
        echo "</div>";
    }
    
    echo "<h3>5. 🛠️ Quick Fix</h3>";
    echo "<p>If your latest upload is not appearing:</p>";
    echo "<ol>";
    echo "<li><strong>Check processing_status:</strong> Must be 'completed'</li>";
    echo "<li><strong>Check timestamp:</strong> Should have upload_timestamp or created_at</li>";
    echo "<li><strong>Clear cache:</strong> Refresh analytics dashboard</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='analytics-dashboard.php'>← Back to Analytics Dashboard</a></p>";
?>
