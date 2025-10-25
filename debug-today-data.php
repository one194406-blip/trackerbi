<?php
require_once 'DatabaseManager.php';
require_once 'FilenameParser.php';

echo "<h1>Debug Today's Data</h1>";

try {
    $dbManager = new DatabaseManager();
    $todayResults = $dbManager->getTodayResults();
    
    echo "<h2>Today's Results Count: " . count($todayResults) . "</h2>";
    
    if (empty($todayResults)) {
        echo "<p>No results found for today. Let's check recent results instead:</p>";
        $recentResults = $dbManager->getRecentResults(5);
        echo "<h2>Recent Results Count: " . count($recentResults) . "</h2>";
        $todayResults = $recentResults;
    }
    
    foreach ($todayResults as $index => $result) {
        echo "<div style='border: 2px solid #ccc; padding: 20px; margin: 10px; background: #f9f9f9;'>";
        echo "<h3>Result #" . ($index + 1) . "</h3>";
        
        echo "<strong>Raw Data:</strong><br>";
        echo "filename: " . htmlspecialchars($result['filename'] ?? 'NULL') . "<br>";
        echo "original_filename: " . htmlspecialchars($result['original_filename'] ?? 'NULL') . "<br>";
        echo "phone_number: " . htmlspecialchars($result['phone_number'] ?? 'NULL') . "<br>";
        echo "call_language: " . htmlspecialchars($result['call_language'] ?? 'NULL') . "<br>";
        echo "caller_name: " . htmlspecialchars($result['caller_name'] ?? 'NULL') . "<br>";
        echo "call_date: " . htmlspecialchars($result['call_date'] ?? 'NULL') . "<br>";
        echo "call_time: " . htmlspecialchars($result['call_time'] ?? 'NULL') . "<br>";
        echo "filename_parsed: " . ($result['filename_parsed'] ? 'TRUE' : 'FALSE') . "<br>";
        echo "upload_timestamp: " . htmlspecialchars($result['upload_timestamp'] ?? 'NULL') . "<br>";
        
        echo "<br><strong>Condition Check:</strong><br>";
        $condition1 = $result['filename_parsed'] ? 'TRUE' : 'FALSE';
        $condition2 = !empty($result['phone_number']) ? 'TRUE' : 'FALSE';
        echo "filename_parsed: $condition1<br>";
        echo "phone_number not empty: $condition2<br>";
        echo "Both conditions: " . (($result['filename_parsed'] && !empty($result['phone_number'])) ? 'TRUE' : 'FALSE') . "<br>";
        
        if ($result['filename_parsed'] && !empty($result['phone_number'])) {
            echo "<br><strong>Would Display:</strong><br>";
            echo "<div style='background: #e8f5e8; padding: 10px; border: 1px solid #4CAF50;'>";
            echo htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['call_language']) . ")<br>";
            echo "📞 " . htmlspecialchars($result['phone_number']) . "<br>";
            echo "🕐 " . date('g:i A', strtotime($result['call_time'])) . "<br>";
            echo "</div>";
        } else {
            echo "<br><strong>Would Display (Fallback):</strong><br>";
            echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffc107;'>";
            echo htmlspecialchars($result['filename']) . "<br>";
            echo "🕐 " . date('g:i A', strtotime($result['upload_timestamp'])) . "<br>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffebee; padding: 20px; border: 1px solid #f44336;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
