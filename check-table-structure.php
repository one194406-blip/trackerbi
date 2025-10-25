<?php
/**
 * Check Database Table Structure
 * This script checks the audio_analysis_results table structure
 */

require_once 'config.php';
require_once 'DatabaseManager.php';

echo "<h2>🔍 Database Table Structure Check</h2>";

try {
    $dbManager = new DatabaseManager();
    
    echo "<h3>📊 Table Structure: audio_analysis_results</h3>";
    
    // Get table structure
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD
    );
    
    $stmt = $connection->query("DESCRIBE audio_analysis_results");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f5f5f5;'>";
    echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
    echo "</tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>🔍 Key Field Analysis</h3>";
    
    $textFields = ['original_transcription', 'english_translation', 'conversation_summary'];
    foreach ($textFields as $field) {
        $found = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $field) {
                $found = true;
                echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
                echo "<h4>📝 " . ucwords(str_replace('_', ' ', $field)) . "</h4>";
                echo "<p><strong>Data Type:</strong> " . $column['Type'] . "</p>";
                
                if (strpos($column['Type'], 'TEXT') !== false || strpos($column['Type'], 'LONGTEXT') !== false) {
                    echo "<p style='color: green;'>✅ <strong>Good:</strong> Can store large amounts of text</p>";
                } elseif (strpos($column['Type'], 'VARCHAR') !== false) {
                    preg_match('/VARCHAR\((\d+)\)/', $column['Type'], $matches);
                    $length = $matches[1] ?? 'unknown';
                    echo "<p style='color: orange;'>⚠️ <strong>Limited:</strong> VARCHAR($length) - may truncate long text</p>";
                } else {
                    echo "<p style='color: red;'>❌ <strong>Issue:</strong> May not support long text</p>";
                }
                echo "</div>";
                break;
            }
        }
        
        if (!$found) {
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid red; border-radius: 5px;'>";
            echo "<h4>❌ " . ucwords(str_replace('_', ' ', $field)) . "</h4>";
            echo "<p style='color: red;'><strong>Field not found!</strong> This may cause storage issues.</p>";
            echo "</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>💡 Recommendations</h3>";
    echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
    echo "<h4>For Optimal Storage:</h4>";
    echo "<ul>";
    echo "<li><strong>original_transcription:</strong> Should be LONGTEXT for long conversations</li>";
    echo "<li><strong>english_translation:</strong> Should be LONGTEXT for complete translations</li>";
    echo "<li><strong>conversation_summary:</strong> Can be TEXT (shorter content)</li>";
    echo "<li><strong>upload_timestamp:</strong> Should be TIMESTAMP or DATETIME with default CURRENT_TIMESTAMP</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Main Dashboard</a> | <a href='fix-encoding.php'>Check Encoding</a></p>";
?>
