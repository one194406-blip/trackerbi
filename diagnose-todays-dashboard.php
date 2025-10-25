<?php
require_once 'DatabaseManager.php';

echo "<h1>🔍 ROOT CAUSE ANALYSIS: Today's Dashboard</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .box{border:2px solid #ccc;padding:15px;margin:10px;background:#f9f9f9;} .error{border-color:#f44336;background:#ffebee;} .success{border-color:#4CAF50;background:#e8f5e8;} .warning{border-color:#ff9800;background:#fff3e0;}</style>";

try {
    echo "<div class='box'>";
    echo "<h2>Step 1: Database Connection</h2>";
    $dbManager = new DatabaseManager();
    echo "✅ DatabaseManager created successfully<br>";
    echo "</div>";

    echo "<div class='box'>";
    echo "<h2>Step 2: Get Recent Results</h2>";
    $recentResults = $dbManager->getRecentResults(20);
    echo "Recent results count: " . count($recentResults) . "<br>";
    
    if (empty($recentResults)) {
        echo "<div class='warning'>⚠️ NO RECENT RESULTS FROM DATABASE</div>";
        echo "This means the database has no data, so we should see demo data.<br>";
    } else {
        echo "✅ Found " . count($recentResults) . " recent results<br>";
        echo "<strong>First result sample:</strong><br>";
        $first = $recentResults[0];
        echo "- filename: " . htmlspecialchars($first['filename'] ?? 'NULL') . "<br>";
        echo "- phone_number: " . htmlspecialchars($first['phone_number'] ?? 'NULL') . "<br>";
        echo "- filename_parsed: " . ($first['filename_parsed'] ? 'TRUE' : 'FALSE') . "<br>";
    }
    echo "</div>";

    echo "<div class='box'>";
    echo "<h2>Step 3: Demo Data Logic</h2>";
    
    // Simulate the exact logic from dashboard.php
    if (empty($recentResults)) {
        echo "✅ DEMO DATA SHOULD BE ADDED (recentResults is empty)<br>";
        
        // Add the exact same demo data
        $recentResults = [
            [
                'id' => 'demo_today_1',
                'session_id' => 'demo_session_' . uniqid(),
                'filename' => 'demo_audio_1.mp3',
                'phone_number' => '8804439756',
                'call_language' => 'Hindi',
                'caller_name' => 'Harika',
                'call_date' => date('Y-m-d'),
                'call_time' => '16:30:00',
                'original_filename' => '8804439756_Hindi_Harika_' . date('Ymd') . '163000.mp3',
                'filename_parsed' => 1,
                'sentiment_score' => 85,
                'clarity_score' => 78,
                'empathy_score' => 82,
                'professionalism_score' => 90,
                'overall_performance' => 'good',
                'upload_timestamp' => date('Y-m-d H:i:s')
            ]
        ];
        
        echo "✅ Demo data added: " . count($recentResults) . " records<br>";
    } else {
        echo "❌ Demo data NOT added (recentResults has data)<br>";
    }
    echo "</div>";

    echo "<div class='box'>";
    echo "<h2>Step 4: Filter for Today's Data</h2>";
    
    $todayResults = array_filter($recentResults, function($result) {
        return date('Y-m-d', strtotime($result['upload_timestamp'])) === date('Y-m-d');
    });
    
    echo "Today's date: " . date('Y-m-d') . "<br>";
    echo "Today's results count: " . count($todayResults) . "<br>";
    
    if (empty($todayResults)) {
        echo "<div class='error'>❌ NO TODAY'S DATA AFTER FILTERING</div>";
        echo "This is the ROOT CAUSE! Let's check why:<br><br>";
        
        echo "<strong>Upload timestamps in recent results:</strong><br>";
        foreach ($recentResults as $i => $result) {
            $uploadDate = date('Y-m-d', strtotime($result['upload_timestamp']));
            $isToday = ($uploadDate === date('Y-m-d')) ? '✅ TODAY' : '❌ NOT TODAY';
            echo "Result $i: " . $result['upload_timestamp'] . " → $uploadDate $isToday<br>";
        }
    } else {
        echo "✅ Found " . count($todayResults) . " today's results<br>";
    }
    echo "</div>";

    echo "<div class='box'>";
    echo "<h2>Step 5: Test Display Condition</h2>";
    
    if (!empty($todayResults)) {
        foreach ($todayResults as $i => $result) {
            echo "<h3>Result #" . ($i + 1) . "</h3>";
            
            $condition = ($result['filename_parsed'] && $result['phone_number']);
            echo "filename_parsed: " . ($result['filename_parsed'] ? 'TRUE' : 'FALSE') . "<br>";
            echo "phone_number: '" . htmlspecialchars($result['phone_number'] ?? 'NULL') . "'<br>";
            echo "phone_number not empty: " . (!empty($result['phone_number']) ? 'TRUE' : 'FALSE') . "<br>";
            echo "<strong>Final condition (filename_parsed && phone_number): " . ($condition ? 'TRUE' : 'FALSE') . "</strong><br>";
            
            if ($condition) {
                echo "<div class='success'>";
                echo "<strong>✅ SHOULD SHOW STRUCTURED FORMAT:</strong><br>";
                echo htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['call_language']) . ")<br>";
                echo "📞 " . htmlspecialchars($result['phone_number']) . "<br>";
                echo "📅 " . date('M j', strtotime($result['call_date'])) . "<br>";
                echo "🕐 " . date('g:i A', strtotime($result['call_time'])) . "<br>";
                echo "</div>";
            } else {
                echo "<div class='warning'>";
                echo "<strong>⚠️ WILL SHOW FALLBACK FORMAT:</strong><br>";
                echo htmlspecialchars($result['filename']) . "<br>";
                echo "</div>";
            }
            echo "<hr>";
        }
    } else {
        echo "<div class='error'>❌ NO TODAY'S RESULTS TO TEST</div>";
    }
    echo "</div>";

    echo "<div class='box'>";
    echo "<h2>🎯 ROOT CAUSE SUMMARY</h2>";
    
    if (empty($recentResults)) {
        echo "<div class='warning'>Issue: No database data, but demo data should be added</div>";
    } elseif (empty($todayResults)) {
        echo "<div class='error'>Issue: Database has data but none from today</div>";
    } elseif (!empty($todayResults)) {
        $hasStructured = false;
        foreach ($todayResults as $result) {
            if ($result['filename_parsed'] && $result['phone_number']) {
                $hasStructured = true;
                break;
            }
        }
        if ($hasStructured) {
            echo "<div class='success'>✅ Everything should work! Structured display should show.</div>";
        } else {
            echo "<div class='error'>Issue: Today's data exists but no structured filename parsing</div>";
        }
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ ERROR</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "This might be the root cause of the issue.";
    echo "</div>";
}
?>
