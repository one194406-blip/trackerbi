<?php
/**
 * Fix Database Table Encoding
 * This script fixes the table structure to support UTF8MB4 properly
 */

require_once 'config.php';

echo "<h2>🔧 Fix Database Table Encoding</h2>";

try {
    // Connect directly to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h3>1. 🔍 Current Table Status</h3>";
    
    // Check current table structure
    $stmt = $pdo->query("SHOW CREATE TABLE audio_analysis_results");
    $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 3px; margin: 10px 0;'>";
    echo "<h4>Current Table Definition:</h4>";
    echo "<pre style='font-size: 11px; white-space: pre-wrap;'>" . htmlspecialchars($createTable['Create Table']) . "</pre>";
    echo "</div>";
    
    // Check if table uses UTF8MB4
    $hasUtf8mb4 = strpos($createTable['Create Table'], 'utf8mb4') !== false;
    
    if ($hasUtf8mb4) {
        echo "<p style='color: green;'>✅ <strong>Table already uses UTF8MB4 encoding</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>Table does not use UTF8MB4 encoding</strong></p>";
    }
    
    echo "<h3>2. 🔍 Column Analysis</h3>";
    
    // Check specific columns
    $stmt = $pdo->query("DESCRIBE audio_analysis_results");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $textColumns = ['original_transcription', 'english_translation', 'conversation_summary'];
    $needsFix = false;
    
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f5f5f5;'><th>Column</th><th>Type</th><th>Status</th><th>Recommendation</th></tr>";
    
    foreach ($columns as $column) {
        $fieldName = $column['Field'];
        $fieldType = $column['Type'];
        
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($fieldName) . "</strong></td>";
        echo "<td>" . htmlspecialchars($fieldType) . "</td>";
        
        if (in_array($fieldName, $textColumns)) {
            if (strpos($fieldType, 'TEXT') !== false || strpos($fieldType, 'LONGTEXT') !== false) {
                echo "<td style='color: green;'>✅ Good for long text</td>";
                echo "<td>-</td>";
            } elseif (strpos($fieldType, 'VARCHAR') !== false) {
                echo "<td style='color: orange;'>⚠️ Limited length</td>";
                echo "<td>Consider changing to LONGTEXT</td>";
                $needsFix = true;
            } else {
                echo "<td style='color: red;'>❌ May truncate</td>";
                echo "<td>Change to LONGTEXT</td>";
                $needsFix = true;
            }
        } elseif ($fieldName === 'upload_timestamp') {
            if (strpos($fieldType, 'timestamp') !== false || strpos($fieldType, 'datetime') !== false) {
                echo "<td style='color: green;'>✅ Good</td>";
                echo "<td>-</td>";
            } else {
                echo "<td style='color: red;'>❌ Wrong type</td>";
                echo "<td>Should be TIMESTAMP</td>";
                $needsFix = true;
            }
        } else {
            echo "<td>-</td>";
            echo "<td>-</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>3. 🛠️ Fix Options</h3>";
    
    if (!$hasUtf8mb4 || $needsFix) {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h4>⚠️ Table needs updates for optimal performance</h4>";
        
        echo "<p><strong>Recommended SQL commands to run in phpMyAdmin:</strong></p>";
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 3px; margin: 10px 0;'>";
        echo "<pre style='font-size: 12px;'>";
        
        if (!$hasUtf8mb4) {
            echo "-- Convert table to UTF8MB4\n";
            echo "ALTER TABLE audio_analysis_results CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n\n";
        }
        
        echo "-- Ensure text columns can store long content\n";
        echo "ALTER TABLE audio_analysis_results \n";
        echo "MODIFY COLUMN original_transcription LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,\n";
        echo "MODIFY COLUMN english_translation LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,\n";
        echo "MODIFY COLUMN conversation_summary TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n\n";
        
        echo "-- Ensure upload_timestamp has proper default\n";
        echo "ALTER TABLE audio_analysis_results \n";
        echo "MODIFY COLUMN upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP;\n";
        
        echo "</pre>";
        echo "</div>";
        
        echo "<p><strong>Steps to apply:</strong></p>";
        echo "<ol>";
        echo "<li>Copy the SQL commands above</li>";
        echo "<li>Go to your InfinityFree phpMyAdmin</li>";
        echo "<li>Select your database</li>";
        echo "<li>Go to SQL tab</li>";
        echo "<li>Paste and execute the commands</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h4>✅ Table structure looks good!</h4>";
        echo "<p>Your table already supports UTF8MB4 and has appropriate column types.</p>";
        echo "<p>If you're still seeing encoding issues, try uploading a new audio file to test the enhanced DatabaseManager.</p>";
        echo "</div>";
    }
    
    echo "<h3>4. 🧪 Test Current Setup</h3>";
    echo "<p><a href='test-full-pipeline.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🧪 Test Full Pipeline</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h4>Connection Failed</h4>";
    echo "<p>Could not connect to database. Please check:</p>";
    echo "<ul>";
    echo "<li>Database credentials in config.php</li>";
    echo "<li>Database server is running</li>";
    echo "<li>Database exists and is accessible</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Dashboard</a> | <a href='test-full-pipeline.php'>Test Pipeline</a> | <a href='check-table-structure.php'>Check Structure</a></p>";
?>
