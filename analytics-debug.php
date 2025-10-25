<?php
/**
 * Analytics Dashboard Debug
 * Shows exactly what data the analytics dashboard is getting from the database
 */

require_once 'DatabaseManager.php';

$dbManager = new DatabaseManager();

echo "<h2>🔍 Analytics Dashboard Data Debug</h2>";

try {
    echo "<h3>1. 📊 Analytics Summary Data</h3>";
    
    $summary = $dbManager->getAnalyticsSummary();
    
    if ($summary) {
        echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Summary Statistics from Database:</h4>";
        echo "<ul>";
        echo "<li><strong>Total Analyses:</strong> " . ($summary['total_analyses'] ?? 0) . "</li>";
        echo "<li><strong>Avg Sentiment Score:</strong> " . round($summary['avg_sentiment_score'] ?? 0, 2) . "</li>";
        echo "<li><strong>Avg Clarity Score:</strong> " . round($summary['avg_clarity_score'] ?? 0, 2) . "</li>";
        echo "<li><strong>Positive Sentiment Count:</strong> " . ($summary['positive_count'] ?? 0) . "</li>";
        echo "<li><strong>Neutral Sentiment Count:</strong> " . ($summary['neutral_count'] ?? 0) . "</li>";
        echo "<li><strong>Negative Sentiment Count:</strong> " . ($summary['negative_count'] ?? 0) . "</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ No summary data available</p>";
    }
    
    echo "<h3>2. 📝 Recent Results Data</h3>";
    
    $recentResults = $dbManager->getRecentResults(5);
    
    if (!empty($recentResults)) {
        echo "<p>✅ Found " . count($recentResults) . " recent results</p>";
        
        foreach ($recentResults as $index => $result) {
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
            echo "<h4>Recent Result #" . ($index + 1) . "</h4>";
            echo "<p><strong>Filename:</strong> " . htmlspecialchars($result['filename']) . "</p>";
            echo "<p><strong>Upload Time:</strong> " . ($result['upload_timestamp'] ?? 'NULL') . "</p>";
            echo "<p><strong>Sentiment Score:</strong> " . ($result['sentiment_score'] ?? 'NULL') . "</p>";
            echo "<p><strong>Primary Sentiment:</strong> " . htmlspecialchars($result['primary_sentiment'] ?? 'NULL') . "</p>";
            echo "<p><strong>Overall Performance:</strong> " . htmlspecialchars($result['overall_performance'] ?? 'NULL') . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No recent results found - Dashboard will show demo data</p>";
    }
    
    echo "<h3>3. 📈 Performance Trends Data</h3>";
    
    $performanceTrends = $dbManager->getPerformanceTrends(7);
    
    if (!empty($performanceTrends)) {
        echo "<p>✅ Found " . count($performanceTrends) . " days of performance trends</p>";
        
        foreach ($performanceTrends as $index => $trend) {
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
            echo "<h4>📅 " . $trend['analysis_date'] . " (" . $trend['daily_count'] . " analyses)</h4>";
            
            echo "<p><strong>Average Scores:</strong></p>";
            echo "<ul>";
            echo "<li>Sentiment: " . round($trend['avg_sentiment'] ?? 0, 2) . "</li>";
            echo "<li>Clarity: " . round($trend['avg_clarity'] ?? 0, 2) . "</li>";
            echo "<li>Empathy: " . round($trend['avg_empathy'] ?? 0, 2) . "</li>";
            echo "</ul>";
            
            // Show detailed records for this day
            if (!empty($trend['parsed_details'])) {
                echo "<h5>📋 Detailed Records for this day:</h5>";
                foreach ($trend['parsed_details'] as $detailIndex => $detail) {
                    echo "<div style='background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 3px;'>";
                    echo "<p><strong>File:</strong> " . htmlspecialchars($detail['filename']) . "</p>";
                    
                    // Check transcription quality
                    if ($detail['original_transcription']) {
                        $transcription = $detail['original_transcription'];
                        $hasQuestionMarks = strpos($transcription, '???') !== false;
                        $length = strlen($transcription);
                        
                        echo "<p><strong>Transcription:</strong> ";
                        if ($hasQuestionMarks) {
                            echo "<span style='color: red;'>❌ Contains ??? (encoding issue)</span>";
                        } else {
                            echo "<span style='color: green;'>✅ Clean text</span>";
                        }
                        echo " ($length chars)</p>";
                        
                        echo "<div style='background: white; padding: 5px; border: 1px solid #ddd; border-radius: 3px; max-height: 100px; overflow-y: auto;'>";
                        echo "<pre style='font-size: 10px; margin: 0;'>" . htmlspecialchars(substr($transcription, 0, 200)) . ($length > 200 ? '...' : '') . "</pre>";
                        echo "</div>";
                    } else {
                        echo "<p><strong>Transcription:</strong> <span style='color: red;'>❌ NULL/Empty</span></p>";
                    }
                    
                    echo "</div>";
                }
            }
            
            echo "</div>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No performance trends found - Dashboard will show demo data</p>";
    }
    
    echo "<h3>4. 🎯 What This Means for Analytics Dashboard</h3>";
    
    $hasRealData = !empty($recentResults) || !empty($performanceTrends);
    
    if ($hasRealData) {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h4>⚠️ Dashboard is showing REAL database data</h4>";
        echo "<p><strong>This means:</strong></p>";
        echo "<ul>";
        echo "<li>If database has ??? characters, dashboard shows ??? characters</li>";
        echo "<li>If database has incomplete conversations, dashboard shows incomplete data</li>";
        echo "<li>If database has wrong dates, dashboard shows wrong dates</li>";
        echo "<li><strong>Fix the database = Fix the dashboard</strong></li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h4>ℹ️ Dashboard is showing DEMO data</h4>";
        echo "<p>Since no real data exists, the dashboard shows clean demo data with proper formatting.</p>";
        echo "<p>Once you upload audio files, the dashboard will show real database data.</p>";
        echo "</div>";
    }
    
    echo "<h3>5. 🛠️ Next Steps</h3>";
    echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
    echo "<h4>To Fix Analytics Dashboard Display:</h4>";
    echo "<ol>";
    echo "<li><strong>Fix Database Table:</strong> <a href='fix-table-encoding.php'>Run table encoding fix</a></li>";
    echo "<li><strong>Test Storage:</strong> <a href='test-full-pipeline.php'>Test full pipeline</a></li>";
    echo "<li><strong>Upload New Audio:</strong> Upload a new file to test the fixes</li>";
    echo "<li><strong>Check Results:</strong> Analytics dashboard will automatically show corrected data</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='analytics-dashboard.php'>← Back to Analytics Dashboard</a> | <a href='fix-table-encoding.php'>Fix Table Encoding</a></p>";
?>
