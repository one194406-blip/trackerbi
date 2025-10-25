<?php
/**
 * Quick Database Data Check
 * Check what data exists in your database
 */

require_once 'DatabaseManager.php';

$dbManager = new DatabaseManager();

echo "<h2>🔍 Database Data Check</h2>";

try {
    echo "<h3>1. 📊 Total Records Count</h3>";
    
    // Count all records
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD
    );
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM audio_analysis_results");
    $count = $stmt->fetch();
    
    echo "<p><strong>Total Records:</strong> " . $count['total'] . "</p>";
    
    if ($count['total'] > 0) {
        echo "<h3>2. 📝 Recent Records (Last 5)</h3>";
        
        $stmt = $connection->query("
            SELECT session_id, filename, upload_timestamp, processing_status, 
                   LENGTH(original_transcription) as transcription_length,
                   LENGTH(english_translation) as translation_length
            FROM audio_analysis_results 
            ORDER BY upload_timestamp DESC 
            LIMIT 5
        ");
        
        $records = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'>";
        echo "<th>Session ID</th><th>Filename</th><th>Upload Time</th><th>Status</th><th>Transcription Length</th><th>Translation Length</th>";
        echo "</tr>";
        
        foreach ($records as $record) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($record['session_id']) . "</td>";
            echo "<td>" . htmlspecialchars($record['filename']) . "</td>";
            echo "<td>" . ($record['upload_timestamp'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($record['processing_status']) . "</td>";
            echo "<td>" . ($record['transcription_length'] ?? 0) . " chars</td>";
            echo "<td>" . ($record['translation_length'] ?? 0) . " chars</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>3. 🧪 Test Analytics Functions</h3>";
        
        // Test getRecentResults
        $recentResults = $dbManager->getRecentResults(3);
        echo "<p><strong>getRecentResults():</strong> " . count($recentResults) . " records</p>";
        
        // Test getPerformanceTrends
        $performanceTrends = $dbManager->getPerformanceTrends(7);
        echo "<p><strong>getPerformanceTrends():</strong> " . count($performanceTrends) . " days</p>";
        
        // Test getAnalyticsSummary
        $summary = $dbManager->getAnalyticsSummary();
        echo "<p><strong>getAnalyticsSummary():</strong> " . ($summary ? 'Working' : 'NULL') . "</p>";
        
        if (empty($recentResults) && empty($performanceTrends)) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
            echo "<h4>⚠️ Issue Found!</h4>";
            echo "<p>Records exist in database but analytics functions return empty results.</p>";
            echo "<p><strong>Possible causes:</strong></p>";
            echo "<ul>";
            echo "<li>Records have <code>processing_status != 'completed'</code></li>";
            echo "<li>Upload timestamps are NULL or invalid</li>";
            echo "<li>Database connection issues</li>";
            echo "</ul>";
            echo "</div>";
            
            // Check processing status
            $stmt = $connection->query("SELECT processing_status, COUNT(*) as count FROM audio_analysis_results GROUP BY processing_status");
            $statuses = $stmt->fetchAll();
            
            echo "<h4>📊 Processing Status Breakdown:</h4>";
            echo "<ul>";
            foreach ($statuses as $status) {
                echo "<li><strong>" . htmlspecialchars($status['processing_status']) . ":</strong> " . $status['count'] . " records</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
        echo "<h4>❌ No Records Found</h4>";
        echo "<p>Your database table is empty. You need to:</p>";
        echo "<ol>";
        echo "<li>Upload audio files through the main interface</li>";
        echo "<li>Or insert test data manually</li>";
        echo "</ol>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='analytics-dashboard.php'>← Back to Analytics</a> | <a href='index.php'>Upload Audio</a></p>";
?>
