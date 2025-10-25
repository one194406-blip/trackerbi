<?php
/**
 * Test Full Audio Processing Pipeline
 * This script tests the complete flow from audio upload to database storage
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

echo "<h2>🔧 Full Pipeline Test</h2>";

try {
    $dbManager = new DatabaseManager();
    
    echo "<h3>1. 📊 Current Database Status</h3>";
    
    // Check recent records
    $recentResults = $dbManager->getAllResults(2, 0);
    
    if (empty($recentResults)) {
        echo "<p>⚠️ No records found in database</p>";
    } else {
        echo "<p>✅ Found " . count($recentResults) . " recent records</p>";
        
        foreach ($recentResults as $index => $record) {
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
            echo "<h4>📝 Record #" . ($index + 1) . " - " . htmlspecialchars($record['filename']) . "</h4>";
            
            // Show key fields
            echo "<p><strong>🕒 Upload Date:</strong> " . ($record['upload_timestamp'] ?? '<span style="color:red;">NULL</span>') . "</p>";
            echo "<p><strong>📊 Status:</strong> " . htmlspecialchars($record['processing_status']) . "</p>";
            
            // Check transcription encoding
            if ($record['original_transcription']) {
                $transcription = $record['original_transcription'];
                $hasQuestionMarks = strpos($transcription, '???') !== false;
                $length = strlen($transcription);
                
                echo "<p><strong>📝 Original Transcription:</strong></p>";
                echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 3px;'>";
                
                if ($hasQuestionMarks) {
                    echo "<p style='color: red;'>❌ <strong>Encoding Issue:</strong> Contains ??? characters</p>";
                } else {
                    echo "<p style='color: green;'>✅ <strong>Encoding OK:</strong> No ??? characters detected</p>";
                }
                
                echo "<p><strong>Length:</strong> $length characters</p>";
                echo "<div style='max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;'>";
                echo "<pre style='white-space: pre-wrap; font-size: 11px; margin: 0;'>" . htmlspecialchars(substr($transcription, 0, 800)) . ($length > 800 ? '...' : '') . "</pre>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<p style='color: red;'>❌ <strong>No transcription data</strong></p>";
            }
            
            // Check translation
            if ($record['english_translation']) {
                $translation = $record['english_translation'];
                $length = strlen($translation);
                
                echo "<p><strong>🌐 English Translation:</strong></p>";
                echo "<div style='background: #f0f8ff; padding: 10px; border-radius: 3px;'>";
                echo "<p><strong>Length:</strong> $length characters</p>";
                echo "<div style='max-height: 100px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;'>";
                echo "<pre style='white-space: pre-wrap; font-size: 11px; margin: 0;'>" . htmlspecialchars(substr($translation, 0, 400)) . ($length > 400 ? '...' : '') . "</pre>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "<p style='color: red;'>❌ <strong>No translation data</strong></p>";
            }
            
            echo "</div>";
        }
    }
    
    echo "<h3>2. 🧪 Test UTF-8 Storage Capability</h3>";
    echo "<p>Testing if the database can store multilingual text correctly...</p>";
    
    // Test data with multiple languages
    $testData = [
        'transcription' => [
            'success' => true,
            'transcription' => "[00:00:01] Speaker: नमस्कार, आप कैसे हैं?\n[00:00:05] Speaker: నమస్కారం, మీరు ఎలా ఉన్నారు?\n[00:00:10] Speaker: வணக்கம், நீங்கள் எப்படி இருக்கிறீர்கள்?\n[00:00:15] Speaker: നമസ്കാരം, നിങ്ങൾ എങ്ങനെയുണ്ട്?\n[00:00:20] Speaker: Hello, how are you doing today?"
        ],
        'translation' => [
            'success' => true,
            'translation' => "[00:00:01] Speaker: Hello, how are you?\n[00:00:05] Speaker: Hello, how are you?\n[00:00:10] Speaker: Hello, how are you?\n[00:00:15] Speaker: Hello, how are you?\n[00:00:20] Speaker: Hello, how are you doing today?"
        ],
        'conversation_summary' => [
            'success' => true,
            'summary' => "A multilingual greeting conversation featuring Hindi, Telugu, Tamil, Malayalam, and English. The conversation consists of simple greetings asking 'how are you' in different Indian languages."
        ],
        'sentiment_analysis' => [
            'success' => true,
            'analysis' => [
                'sentiment_score' => ['numerical_score' => 80, 'confidence' => 0.9],
                'overall_sentiment' => [
                    'primary_sentiment' => 'positive',
                    'emotional_tone' => 'friendly',
                    'empathy_level' => 'high',
                    'politeness_level' => 'high'
                ],
                'agent_performance' => [
                    'clarity_score' => 85,
                    'empathy_score' => 90,
                    'professionalism_score' => 88,
                    'call_opening_score' => 92,
                    'call_quality_score' => 87,
                    'call_closing_score' => 85,
                    'overall_performance' => 'excellent'
                ]
            ]
        ],
        'errors' => []
    ];
    
    $testResult = $dbManager->storeAnalysisResults($testData, 'multilingual_test.mp3', 2048);
    
    if ($testResult['success']) {
        echo "<p style='color: green;'>✅ <strong>Test storage successful!</strong></p>";
        echo "<p><strong>Session ID:</strong> " . $testResult['session_id'] . "</p>";
        
        // Retrieve and verify the test data
        echo "<h4>🔍 Verifying stored test data...</h4>";
        $verifyData = $dbManager->getAllResults(1, 0);
        
        if (!empty($verifyData)) {
            $testRecord = $verifyData[0];
            echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
            echo "<h5>📝 Retrieved Test Record:</h5>";
            echo "<p><strong>Filename:</strong> " . htmlspecialchars($testRecord['filename']) . "</p>";
            echo "<p><strong>Upload Time:</strong> " . $testRecord['upload_timestamp'] . "</p>";
            
            $storedTranscription = $testRecord['original_transcription'];
            $hasQuestionMarks = strpos($storedTranscription, '???') !== false;
            
            if ($hasQuestionMarks) {
                echo "<p style='color: red;'>❌ <strong>Storage Issue:</strong> Test data also shows ??? characters</p>";
                echo "<p><strong>This indicates a database table encoding problem</strong></p>";
            } else {
                echo "<p style='color: green;'>✅ <strong>Storage Success:</strong> Multilingual text stored correctly</p>";
            }
            
            echo "<p><strong>Stored Transcription Preview:</strong></p>";
            echo "<div style='background: white; padding: 10px; border: 1px solid #ddd; border-radius: 3px;'>";
            echo "<pre style='white-space: pre-wrap; font-size: 11px;'>" . htmlspecialchars(substr($storedTranscription, 0, 300)) . "...</pre>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ <strong>Test storage failed:</strong> " . htmlspecialchars($testResult['error']) . "</p>";
    }
    
    echo "<h3>3. 💡 Diagnosis & Solutions</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
    echo "<h4>🔍 If you're still seeing issues:</h4>";
    echo "<ol>";
    echo "<li><strong>Database Table Encoding:</strong> Your table columns may not be UTF8MB4</li>";
    echo "<li><strong>MySQL Configuration:</strong> Server may not support UTF8MB4</li>";
    echo "<li><strong>Old Data:</strong> Existing records with ??? cannot be recovered</li>";
    echo "<li><strong>New Uploads:</strong> Should work correctly with the enhanced DatabaseManager</li>";
    echo "</ol>";
    
    echo "<h4>🛠️ Quick Fixes:</h4>";
    echo "<ul>";
    echo "<li><strong>Check Table:</strong> <a href='check-table-structure.php'>View table structure</a></li>";
    echo "<li><strong>Test Upload:</strong> Upload a new audio file to test the fixes</li>";
    echo "<li><strong>Database Logs:</strong> Check your error logs for storage details</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Dashboard</a> | <a href='debug-storage.php'>Debug Storage</a> | <a href='check-table-structure.php'>Check Table</a></p>";
?>
