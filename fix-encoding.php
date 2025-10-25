<?php
/**
 * Fix Character Encoding Issues in Database
 * This script will check and display the current encoding issues
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

echo "<h2>🔧 Character Encoding Fix Tool</h2>";

try {
    $dbManager = new DatabaseManager();
    
    echo "<h3>📊 Current Database Records</h3>";
    
    // Get recent results to check encoding
    $results = $dbManager->getAllResults(5, 0);
    
    if (empty($results)) {
        echo "<p>⚠️ No records found in database.</p>";
    } else {
        echo "<p>✅ Found " . count($results) . " records. Checking encoding...</p>";
        
        foreach ($results as $index => $record) {
            echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
            echo "<h4>Record #" . ($index + 1) . " - " . htmlspecialchars($record['filename']) . "</h4>";
            
            // Check transcription encoding
            if ($record['original_transcription']) {
                $transcription = $record['original_transcription'];
                $hasQuestionMarks = strpos($transcription, '???') !== false;
                
                echo "<p><strong>Original Transcription:</strong></p>";
                echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 3px; max-height: 200px; overflow-y: auto;'>";
                
                if ($hasQuestionMarks) {
                    echo "<p style='color: red;'>⚠️ <strong>Encoding Issue Detected!</strong> Contains question marks (???) indicating character encoding problems.</p>";
                    
                    // Try to detect the actual encoding
                    $detectedEncoding = mb_detect_encoding($transcription, ['UTF-8', 'UTF-16', 'ISO-8859-1', 'Windows-1252'], true);
                    echo "<p><strong>Detected Encoding:</strong> " . ($detectedEncoding ?: 'Unknown') . "</p>";
                    
                    // Show first 1000 characters
                    echo "<pre style='white-space: pre-wrap; font-size: 12px;'>" . htmlspecialchars(substr($transcription, 0, 1000)) . "...</pre>";
                    echo "<p><strong>Total Length:</strong> " . strlen($transcription) . " characters</p>";
                } else {
                    echo "<p style='color: green;'>✅ <strong>Encoding looks good!</strong></p>";
                    echo "<pre style='white-space: pre-wrap; font-size: 12px;'>" . htmlspecialchars(substr($transcription, 0, 300)) . "...</pre>";
                }
                echo "</div>";
            } else {
                echo "<p><em>No transcription data</em></p>";
            }
            
            // Check translation
            if ($record['english_translation']) {
                echo "<p><strong>English Translation:</strong></p>";
                echo "<div style='background: #f0f8ff; padding: 10px; border-radius: 3px; max-height: 200px; overflow-y: auto;'>";
                echo "<pre style='white-space: pre-wrap; font-size: 12px;'>" . htmlspecialchars(substr($record['english_translation'], 0, 800)) . "...</pre>";
                echo "<p><strong>Total Length:</strong> " . strlen($record['english_translation']) . " characters</p>";
                echo "</div>";
            } else {
                echo "<p><em>No translation data</em></p>";
            }
            
            // Show upload timestamp
            echo "<p><strong>Upload Date:</strong> " . ($record['upload_timestamp'] ? date('Y-m-d H:i:s', strtotime($record['upload_timestamp'])) : 'Not recorded') . "</p>";
            
            echo "</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🔧 Recommendations</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
    echo "<h4>To Fix Encoding Issues:</h4>";
    echo "<ol>";
    echo "<li><strong>For New Uploads:</strong> The DatabaseManager has been updated with proper UTF-8 handling</li>";
    echo "<li><strong>For Existing Data:</strong> The question marks (???) indicate the original characters were lost during storage</li>";
    echo "<li><strong>Audio Language:</strong> Your audio appears to contain Hindi/Devanagari text</li>";
    echo "<li><strong>Solution:</strong> Re-upload the audio files to get properly encoded transcriptions</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>✅ Database Connection Status</h3>";
    $connectionTest = $dbManager->testConnection();
    if ($connectionTest['success']) {
        echo "<p style='color: green;'>✅ Database connection is working with UTF-8 support</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection issue: " . htmlspecialchars($connectionTest['message']) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Main Dashboard</a> | <a href='test-db-connection.php'>Test DB Connection</a></p>";
?>
