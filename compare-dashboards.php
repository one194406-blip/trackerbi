<?php
require_once 'DatabaseManager.php';
require_once 'FilenameParser.php';

echo "<h1>Dashboard Data Comparison</h1>";

try {
    $dbManager = new DatabaseManager();
    
    echo "<h2>1. Analytics Dashboard Data (getRecentResults)</h2>";
    $analyticsData = $dbManager->getRecentResults(5);
    echo "<strong>Count:</strong> " . count($analyticsData) . "<br><br>";
    
    foreach ($analyticsData as $index => $result) {
        echo "<div style='border: 2px solid #4CAF50; padding: 15px; margin: 10px; background: #f0f8f0;'>";
        echo "<h3>Analytics Result #" . ($index + 1) . "</h3>";
        echo "<strong>filename:</strong> " . htmlspecialchars($result['filename'] ?? 'NULL') . "<br>";
        echo "<strong>phone_number:</strong> " . htmlspecialchars($result['phone_number'] ?? 'NULL') . "<br>";
        echo "<strong>call_language:</strong> " . htmlspecialchars($result['call_language'] ?? 'NULL') . "<br>";
        echo "<strong>caller_name:</strong> " . htmlspecialchars($result['caller_name'] ?? 'NULL') . "<br>";
        echo "<strong>filename_parsed:</strong> " . ($result['filename_parsed'] ? 'TRUE' : 'FALSE') . "<br>";
        echo "<strong>upload_timestamp:</strong> " . htmlspecialchars($result['upload_timestamp'] ?? 'NULL') . "<br>";
        
        // Test the condition
        $condition = ($result['filename_parsed'] && $result['phone_number']);
        echo "<strong>Display Condition (filename_parsed && phone_number):</strong> " . ($condition ? 'TRUE' : 'FALSE') . "<br>";
        
        if ($condition) {
            echo "<div style='background: #e8f5e8; padding: 10px; margin: 5px; border: 1px solid #4CAF50;'>";
            echo "<strong>WOULD SHOW:</strong><br>";
            echo htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['call_language']) . ")<br>";
            echo "📞 " . htmlspecialchars($result['phone_number']) . "<br>";
            if ($result['call_time']) {
                echo "🕐 " . date('g:i A', strtotime($result['call_time'])) . "<br>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; padding: 10px; margin: 5px; border: 1px solid #ffc107;'>";
            echo "<strong>WOULD SHOW (fallback):</strong><br>";
            echo htmlspecialchars($result['filename']) . "<br>";
            echo "</div>";
        }
        echo "</div>";
    }
    
    echo "<hr><h2>2. Today's Dashboard Data (filtered from recent)</h2>";
    $todayFiltered = array_filter($analyticsData, function($result) {
        return date('Y-m-d', strtotime($result['upload_timestamp'])) === date('Y-m-d');
    });
    echo "<strong>Today's Count:</strong> " . count($todayFiltered) . "<br><br>";
    
    if (empty($todayFiltered)) {
        echo "<div style='color: red; background: #ffebee; padding: 20px; border: 1px solid #f44336;'>";
        echo "<strong>PROBLEM FOUND:</strong> No results from today!<br>";
        echo "Current date: " . date('Y-m-d') . "<br>";
        echo "Recent results dates:<br>";
        foreach ($analyticsData as $result) {
            echo "- " . date('Y-m-d', strtotime($result['upload_timestamp'])) . " (" . $result['upload_timestamp'] . ")<br>";
        }
        echo "</div>";
    } else {
        foreach ($todayFiltered as $index => $result) {
            echo "<div style='border: 2px solid #2196F3; padding: 15px; margin: 10px; background: #f0f4ff;'>";
            echo "<h3>Today's Result #" . ($index + 1) . "</h3>";
            echo "<strong>Same data as analytics, should work the same way!</strong><br>";
            echo "</div>";
        }
    }
    
    echo "<hr><h2>3. Test with Sample Data</h2>";
    echo "<div style='background: #f9f9f9; padding: 20px; border: 1px solid #ddd;'>";
    echo "<strong>If no today's data exists, let's create sample data:</strong><br>";
    
    $sampleData = [
        [
            'filename' => 'processed_audio.mp3',
            'phone_number' => '8804439756',
            'call_language' => 'Hindi',
            'caller_name' => 'Harika',
            'call_date' => date('Y-m-d'),
            'call_time' => '16:30:00',
            'filename_parsed' => 1,
            'upload_timestamp' => date('Y-m-d H:i:s'),
            'sentiment_score' => 85,
            'clarity_score' => 78,
            'empathy_score' => 82,
            'professionalism_score' => 90,
            'overall_performance' => 'good',
            'session_id' => 'sample_123'
        ]
    ];
    
    foreach ($sampleData as $sample) {
        echo "<h4>Sample Display Test:</h4>";
        $condition = ($sample['filename_parsed'] && $sample['phone_number']);
        echo "Condition: " . ($condition ? 'TRUE' : 'FALSE') . "<br>";
        
        if ($condition) {
            echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px; border: 1px solid #4CAF50;'>";
            echo "<strong>" . htmlspecialchars($sample['caller_name']) . " (" . htmlspecialchars($sample['call_language']) . ")</strong><br>";
            echo "📞 " . htmlspecialchars($sample['phone_number']) . "<br>";
            echo "🕐 " . date('g:i A', strtotime($sample['call_time'])) . "<br>";
            echo "<br>Metrics: Sentiment: " . $sample['sentiment_score'] . ", Clarity: " . $sample['clarity_score'];
            echo "<br>Performance: " . ucfirst($sample['overall_performance']);
            echo "</div>";
        }
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffebee; padding: 20px; border: 1px solid #f44336;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
