<?php
// Simple test without database connection
require_once 'FilenameParser.php';

echo "<h1>Filename Parser Test</h1>";

// Test the filename you mentioned
$testFilename = "8804439756_Hindi_Harika_20251022163000.mp3";

echo "<h2>Testing: " . htmlspecialchars($testFilename) . "</h2>";

$parsed = FilenameParser::parseFilename($testFilename);

echo "<h3>Parsing Result:</h3>";
echo "<pre>" . json_encode($parsed, JSON_PRETTY_PRINT) . "</pre>";

if ($parsed['success']) {
    $display = FilenameParser::formatForDisplay($parsed);
    echo "<h3>Display Format:</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 10px; background: #f9f9f9;'>";
    echo "<strong>" . htmlspecialchars($display['display_name']) . "</strong><br>";
    echo "📞 " . htmlspecialchars($parsed['phone_number']) . "<br>";
    echo "📅 " . htmlspecialchars($parsed['call_date']) . "<br>";
    echo "🕐 " . htmlspecialchars($parsed['call_time']) . "<br>";
    echo "<small>Details: " . htmlspecialchars($display['details']) . "</small>";
    echo "</div>";
} else {
    echo "<div style='color: red;'>Parsing failed: " . htmlspecialchars($parsed['error_message']) . "</div>";
}

// Test other variations
$testFiles = [
    "9080093260_English_Nisarga_20251022164012.mp3",
    "8804439756_Hindi_Harika_20251022163000.mp3",
    "1234567890_Tamil_Priya_20251023120000.wav",
    "invalid_filename.mp3"
];

echo "<h2>Multiple Test Cases:</h2>";
foreach ($testFiles as $file) {
    $result = FilenameParser::parseFilename($file);
    echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px;'>";
    echo "<strong>" . htmlspecialchars($file) . "</strong><br>";
    if ($result['success']) {
        echo "✅ Success: " . htmlspecialchars($result['caller_name']) . " (" . htmlspecialchars($result['language']) . ") - " . htmlspecialchars($result['phone_number']);
    } else {
        echo "❌ Failed: " . htmlspecialchars($result['error_message']);
    }
    echo "</div>";
}
?>
