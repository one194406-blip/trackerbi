<?php
require_once 'DatabaseManager.php';

echo "<h1>🔍 Debug: Both Dashboards Data Comparison</h1>";

try {
    $dbManager = new DatabaseManager();
    
    // Get the same data both dashboards use
    $recentResults = $dbManager->getRecentResults(20);
    
    echo "<h2>📊 Recent Results (What Analytics Dashboard Uses)</h2>";
    echo "<strong>Total Recent Results:</strong> " . count($recentResults) . "<br><br>";
    
    if (!empty($recentResults)) {
        foreach (array_slice($recentResults, 0, 3) as $index => $result) {
            echo "<div style='border: 2px solid #4CAF50; padding: 15px; margin: 10px; background: #f0f8f0;'>";
            echo "<h3>Recent Result #" . ($index + 1) . "</h3>";
            echo "<strong>filename:</strong> " . htmlspecialchars($result['filename'] ?? 'NULL') . "<br>";
            echo "<strong>phone_number:</strong> " . htmlspecialchars($result['phone_number'] ?? 'NULL') . "<br>";
            echo "<strong>call_language:</strong> " . htmlspecialchars($result['call_language'] ?? 'NULL') . "<br>";
            echo "<strong>caller_name:</strong> " . htmlspecialchars($result['caller_name'] ?? 'NULL') . "<br>";
            echo "<strong>filename_parsed:</strong> " . ($result['filename_parsed'] ? 'TRUE' : 'FALSE') . "<br>";
            echo "<strong>upload_timestamp:</strong> " . htmlspecialchars($result['upload_timestamp'] ?? 'NULL') . "<br>";
            echo "<strong>call_date:</strong> " . htmlspecialchars($result['call_date'] ?? 'NULL') . "<br>";
            echo "<strong>call_time:</strong> " . htmlspecialchars($result['call_time'] ?? 'NULL') . "<br>";
            
            // Test the exact condition from analytics dashboard
            $condition = ($result['filename_parsed'] && $result['phone_number']);
            echo "<br><strong>Analytics Dashboard Condition:</strong> " . ($condition ? 'TRUE - SHOWS STRUCTURED' : 'FALSE - SHOWS FILENAME') . "<br>";
            
            if ($condition) {
                echo "<div style='background: #e8f5e8; padding: 10px; margin: 5px; border: 1px solid #4CAF50;'>";
                echo "<strong>Analytics Dashboard Shows:</strong><br>";
                echo htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['call_language']) . ")<br>";
                echo "📞 " . htmlspecialchars($result['phone_number']) . " 📅 " . date('M j', strtotime($result['call_date'])) . " 🕐 " . date('g:i A', strtotime($result['call_time'])) . "<br>";
                echo "Session: " . htmlspecialchars($result['session_id']);
                echo "</div>";
            } else {
                echo "<div style='background: #fff3cd; padding: 10px; margin: 5px; border: 1px solid #ffc107;'>";
                echo "<strong>Analytics Dashboard Shows:</strong><br>";
                echo htmlspecialchars($result['filename']) . "<br>";
                echo "Session: " . htmlspecialchars($result['session_id']);
                echo "</div>";
            }
            echo "</div>";
        }
    }
    
    // Filter for today's data
    $todayResults = array_filter($recentResults, function($result) {
        return date('Y-m-d', strtotime($result['upload_timestamp'])) === date('Y-m-d');
    });
    
    echo "<hr><h2>📅 Today's Results (What Today's Dashboard Uses)</h2>";
    echo "<strong>Today's Date:</strong> " . date('Y-m-d') . "<br>";
    echo "<strong>Today's Results Count:</strong> " . count($todayResults) . "<br><br>";
    
    if (empty($todayResults)) {
        echo "<div style='color: red; background: #ffebee; padding: 20px; border: 1px solid #f44336;'>";
        echo "<strong>❌ NO TODAY'S DATA FOUND!</strong><br>";
        echo "This is why Today's Dashboard might not show anything.<br><br>";
        echo "<strong>Recent upload dates:</strong><br>";
        foreach (array_slice($recentResults, 0, 5) as $result) {
            $uploadDate = date('Y-m-d', strtotime($result['upload_timestamp']));
            $isToday = ($uploadDate === date('Y-m-d')) ? ' ✅ TODAY' : ' ❌ NOT TODAY';
            echo "- " . $uploadDate . " (" . $result['upload_timestamp'] . ")" . $isToday . "<br>";
        }
        echo "</div>";
    } else {
        echo "<div style='color: green; background: #e8f5e8; padding: 20px; border: 1px solid #4CAF50;'>";
        echo "<strong>✅ TODAY'S DATA FOUND!</strong><br>";
        echo "Today's Dashboard should show the same structured display as Analytics Dashboard.";
        echo "</div>";
        
        foreach ($todayResults as $index => $result) {
            echo "<div style='border: 2px solid #2196F3; padding: 15px; margin: 10px; background: #f0f4ff;'>";
            echo "<h3>Today's Result #" . ($index + 1) . "</h3>";
            echo "<strong>This should display exactly like Analytics Dashboard!</strong><br>";
            
            $condition = ($result['filename_parsed'] && $result['phone_number']);
            if ($condition) {
                echo "<div style='background: #e8f5e8; padding: 10px; margin: 5px; border: 1px solid #4CAF50;'>";
                echo "<strong>Should Show:</strong><br>";
                echo htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['call_language']) . ")<br>";
                echo "📞 " . htmlspecialchars($result['phone_number']) . " 📅 " . date('M j', strtotime($result['call_date'])) . " 🕐 " . date('g:i A', strtotime($result['call_time']));
                echo "</div>";
            } else {
                echo "<div style='background: #fff3cd; padding: 10px; margin: 5px; border: 1px solid #ffc107;'>";
                echo "<strong>Should Show:</strong><br>";
                echo htmlspecialchars($result['filename']);
                echo "</div>";
            }
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffebee; padding: 20px; border: 1px solid #f44336;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
</style>
