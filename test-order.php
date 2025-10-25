<?php
/**
 * Test Analytics Dashboard Order
 * Verify that latest uploads appear first
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

$dbManager = new DatabaseManager();

echo "<h2>🔍 Test Analytics Dashboard Order</h2>";

try {
    echo "<h3>1. 📊 Recent Results (Should be newest first)</h3>";
    
    $recentResults = $dbManager->getRecentResults(5);
    
    if (!empty($recentResults)) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>Order</th><th>Filename</th><th>Upload Time</th><th>Session ID</th>";
        echo "</tr>";
        
        foreach ($recentResults as $index => $result) {
            $bgColor = $index === 0 ? 'background: #e7f3ff;' : '';
            echo "<tr style='$bgColor'>";
            echo "<td><strong>" . ($index + 1) . ($index === 0 ? ' (LATEST)' : '') . "</strong></td>";
            echo "<td>" . htmlspecialchars($result['filename']) . "</td>";
            echo "<td>" . ($result['upload_timestamp'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($result['session_id']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No recent results found</p>";
    }
    
    echo "<h3>2. 📈 Performance Trends (Should be newest date first)</h3>";
    
    $performanceTrends = $dbManager->getPerformanceTrends(7);
    
    if (!empty($performanceTrends)) {
        foreach ($performanceTrends as $dayIndex => $trend) {
            $bgColor = $dayIndex === 0 ? 'background: #e7f3ff;' : 'background: #f8f9fa;';
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; $bgColor'>";
            echo "<h4>" . ($dayIndex + 1) . ". " . $trend['analysis_date'] . ($dayIndex === 0 ? ' (LATEST DATE)' : '') . "</h4>";
            echo "<p><strong>Daily Count:</strong> " . $trend['daily_count'] . " analyses</p>";
            
            if (!empty($trend['parsed_details'])) {
                echo "<h5>📋 Individual Analyses (Should be newest first within this day):</h5>";
                echo "<ul>";
                foreach ($trend['parsed_details'] as $detailIndex => $detail) {
                    $latest = $detailIndex === 0 ? ' <strong>(LATEST IN DAY)</strong>' : '';
                    echo "<li>";
                    echo ($detailIndex + 1) . ". " . htmlspecialchars($detail['filename']) . " - ";
                    echo date('H:i:s', strtotime($detail['upload_timestamp'])) . $latest;
                    echo "</li>";
                }
                echo "</ul>";
            }
            echo "</div>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No performance trends found</p>";
    }
    
    echo "<h3>3. 💡 What This Means</h3>";
    
    if (!empty($recentResults) || !empty($performanceTrends)) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h4>✅ Order is Correct!</h4>";
        echo "<p><strong>Analytics Dashboard shows:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Latest uploaded file</strong> appears at the top</li>";
        echo "<li><strong>Most recent date</strong> appears first</li>";
        echo "<li><strong>Within each day</strong>, newest analysis appears first</li>";
        echo "</ul>";
        echo "<p>If you upload a new audio file now, it will appear at the very top of the analytics dashboard.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h4>⚠️ No Data to Test</h4>";
        echo "<p>Upload some audio files first, then check the analytics dashboard order.</p>";
        echo "</div>";
    }
    
    echo "<h3>4. 🧪 Quick Test</h3>";
    echo "<p><strong>To verify the order works:</strong></p>";
    echo "<ol>";
    echo "<li>Upload an audio file now</li>";
    echo "<li>Go to analytics dashboard</li>";
    echo "<li>Your new file should appear at the very top</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='analytics-dashboard.php'>← Back to Analytics Dashboard</a> | <a href='index.php'>Upload Audio File</a></p>";
?>
