<?php
/**
 * Debug Storage Issues
 * This script helps identify why transcriptions show as ??? and dates aren't updating
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

echo "<h2>🔧 Debug Storage Issues</h2>";

try {
    $dbManager = new DatabaseManager();
    
    echo "<h3>1. 🔍 Database Connection Test</h3>";
    $connectionTest = $dbManager->testConnection();
    if ($connectionTest['success']) {
        echo "<p style='color: green;'>✅ Database connection working</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($connectionTest['error']) . "</p>";
        exit;
    }
    
    echo "<h3>2. 📊 Recent Database Records</h3>";
    $recentResults = $dbManager->getAllResults(3, 0);
    
    if (empty($recentResults)) {
        echo "<p>⚠️ No records found in database</p>";
    } else {
        echo "<p>✅ Found " . count($recentResults) . " recent records</p>";
        
        foreach ($recentResults as $index => $record) {
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
            echo "<h4>Record #" . ($index + 1) . "</h4>";
            
            echo "<p><strong>Filename:</strong> " . htmlspecialchars($record['filename']) . "</p>";
            echo "<p><strong>Upload Date:</strong> " . ($record['upload_timestamp'] ?? 'NULL') . "</p>";
            echo "<p><strong>Processing Status:</strong> " . htmlspecialchars($record['processing_status']) . "</p>";
            
            // Check transcription
            if ($record['original_transcription']) {
                $transcription = $record['original_transcription'];
                $hasQuestionMarks = strpos($transcription, '???') !== false;
                
                echo "<p><strong>Original Transcription:</strong></p>";
                if ($hasQuestionMarks) {
                    echo "<p style='color: red;'>❌ Contains ??? characters - encoding issue detected</p>";
                } else {
                    echo "<p style='color: green;'>✅ No encoding issues detected</p>";
                }
                
                echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 3px; max-height: 150px; overflow-y: auto;'>";
                echo "<pre style='white-space: pre-wrap; font-size: 11px;'>" . htmlspecialchars(substr($transcription, 0, 500)) . "...</pre>";
                echo "<p><strong>Length:</strong> " . strlen($transcription) . " characters</p>";
                echo "</div>";
            } else {
                echo "<p style='color: red;'>❌ No transcription data stored</p>";
            }
            
            // Check translation
            if ($record['english_translation']) {
                echo "<p><strong>English Translation:</strong></p>";
                echo "<div style='background: #f0f8ff; padding: 10px; border-radius: 3px; max-height: 100px; overflow-y: auto;'>";
                echo "<pre style='white-space: pre-wrap; font-size: 11px;'>" . htmlspecialchars(substr($record['english_translation'], 0, 300)) . "...</pre>";
                echo "<p><strong>Length:</strong> " . strlen($record['english_translation']) . " characters</p>";
                echo "</div>";
            } else {
                echo "<p style='color: red;'>❌ No translation data stored</p>";
            }
            
            echo "</div>";
        }
    }
    
    echo "<h3>3. 🛠️ Test Sample Data Storage</h3>";
    echo "<p>Testing if the DatabaseManager can store UTF-8 data correctly...</p>";
    
    // Create test data with Hindi text
    $testResults = [
        'transcription' => [
            'success' => true,
            'transcription' => "नमस्कार, आप कैसे हैं? यह एक परीक्षण है।\n[00:01] Speaker: नमस्कार\n[00:02] Speaker: आप कैसे हैं"
        ],
        'translation' => [
            'success' => true,
            'translation' => "Hello, how are you? This is a test.\n[00:01] Speaker: Hello\n[00:02] Speaker: How are you"
        ],
        'conversation_summary' => [
            'success' => true,
            'summary' => "A simple greeting conversation in Hindi with English translation."
        ],
        'sentiment_analysis' => [
            'success' => true,
            'analysis' => [
                'sentiment_score' => ['numerical_score' => 75, 'confidence' => 0.85],
                'overall_sentiment' => ['primary_sentiment' => 'positive'],
                'agent_performance' => ['overall_performance' => 'good']
            ]
        ],
        'errors' => []
    ];
    
    $testResult = $dbManager->storeAnalysisResults($testResults, 'test_hindi_audio.mp3', 1024);
    
    if ($testResult['success']) {
        echo "<p style='color: green;'>✅ Test data stored successfully! Session ID: " . $testResult['session_id'] . "</p>";
        
        // Retrieve the test data to verify
        $retrievedData = $dbManager->getAllResults(1, 0);
        if (!empty($retrievedData)) {
            $testRecord = $retrievedData[0];
            echo "<p><strong>Retrieved Test Data:</strong></p>";
            echo "<div style='background: #e7f3ff; padding: 10px; border-radius: 3px;'>";
            echo "<p><strong>Transcription:</strong> " . htmlspecialchars(substr($testRecord['original_transcription'], 0, 100)) . "...</p>";
            echo "<p><strong>Translation:</strong> " . htmlspecialchars(substr($testRecord['english_translation'], 0, 100)) . "...</p>";
            echo "<p><strong>Upload Time:</strong> " . $testRecord['upload_timestamp'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ Test data storage failed: " . htmlspecialchars($testResult['error']) . "</p>";
    }
    
    echo "<h3>4. 💡 Recommendations</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
    echo "<h4>If you're still seeing ??? characters:</h4>";
    echo "<ol>";
    echo "<li><strong>Database Table:</strong> Check if your table fields are UTF8MB4</li>";
    echo "<li><strong>AudioAnalyzer:</strong> Verify it's calling DatabaseManager correctly</li>";
    echo "<li><strong>Gemini API:</strong> Check if the API response format has changed</li>";
    echo "<li><strong>Re-upload:</strong> Try uploading a new audio file to test the fixes</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Main Dashboard</a> | <a href='check-table-structure.php'>Check Table Structure</a></p>";
?>
