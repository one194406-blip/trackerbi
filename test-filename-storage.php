<?php
require_once 'AudioAnalyzer.php';
require_once 'FilenameParser.php';

// Set page title for header
$page_title = 'Filename Storage Test';

// Include common header
include 'includes/header.php';

echo "<div class='gradient-bg py-16'>
        <div class='container mx-auto px-6'>
            <h1 class='text-5xl font-bold text-white text-center mb-4'>
                <i class='fas fa-vial mr-4 icon-accent'></i>
                Filename Storage Test
            </h1>
            <p class='text-white text-center text-xl opacity-90 max-w-2xl mx-auto'>
                Test filename parsing and database storage functionality
            </p>
        </div>
    </div>";

echo "<div class='container mx-auto px-6 py-12'>";

// Test filename parsing
$testFilename = "9080093260_English_Nisarga_20251022164012.mp3";

echo "<div class='glass-card rounded-2xl p-8 mb-8'>";
echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
echo "<i class='fas fa-cogs mr-3 icon-accent'></i>";
echo "Filename Parsing Test";
echo "</h2>";

echo "<div class='bg-blue-50 rounded-lg p-6 border border-blue-200 mb-6'>";
echo "<h3 class='text-lg font-semibold text-blue-800 mb-3'>Test Filename</h3>";
echo "<div class='font-mono text-lg bg-white p-3 rounded border'>";
echo htmlspecialchars($testFilename);
echo "</div>";
echo "</div>";

// Test parsing
$parsed = FilenameParser::parseFilename($testFilename);
$display = FilenameParser::formatForDisplay($parsed);

echo "<div class='bg-white rounded-lg p-6 border mb-6'>";
echo "<h3 class='text-lg font-semibold text-gray-800 mb-4'>Parsing Result</h3>";

if ($parsed['success']) {
    echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4'>";
    echo "<div class='bg-green-50 p-4 rounded border border-green-200'>";
    echo "<div class='text-sm text-green-600 mb-1'>Phone Number</div>";
    echo "<div class='font-semibold text-green-800'>" . htmlspecialchars($parsed['phone_number']) . "</div>";
    echo "</div>";
    
    echo "<div class='bg-blue-50 p-4 rounded border border-blue-200'>";
    echo "<div class='text-sm text-blue-600 mb-1'>Language</div>";
    echo "<div class='font-semibold text-blue-800'>" . htmlspecialchars($parsed['language']) . "</div>";
    echo "</div>";
    
    echo "<div class='bg-purple-50 p-4 rounded border border-purple-200'>";
    echo "<div class='text-sm text-purple-600 mb-1'>Caller Name</div>";
    echo "<div class='font-semibold text-purple-800'>" . htmlspecialchars($parsed['caller_name']) . "</div>";
    echo "</div>";
    
    echo "<div class='bg-orange-50 p-4 rounded border border-orange-200'>";
    echo "<div class='text-sm text-orange-600 mb-1'>Call Date</div>";
    echo "<div class='font-semibold text-orange-800'>" . htmlspecialchars($parsed['call_date']) . "</div>";
    echo "</div>";
    
    echo "<div class='bg-indigo-50 p-4 rounded border border-indigo-200'>";
    echo "<div class='text-sm text-indigo-600 mb-1'>Call Time</div>";
    echo "<div class='font-semibold text-indigo-800'>" . htmlspecialchars($parsed['call_time']) . "</div>";
    echo "</div>";
    
    echo "<div class='bg-gray-50 p-4 rounded border border-gray-200'>";
    echo "<div class='text-sm text-gray-600 mb-1'>Success</div>";
    echo "<div class='font-semibold text-green-800'><i class='fas fa-check-circle'></i> Parsed Successfully</div>";
    echo "</div>";
    
    echo "</div>";
} else {
    echo "<div class='bg-red-50 p-4 rounded border border-red-200'>";
    echo "<i class='fas fa-times-circle text-red-600 mr-2'></i>";
    echo "<span class='text-red-800 font-semibold'>Parsing Failed: " . htmlspecialchars($parsed['error_message']) . "</span>";
    echo "</div>";
}

echo "</div>";

echo "<div class='bg-gray-900 text-green-400 p-4 rounded-lg'>";
echo "<h4 class='text-white mb-2'>Raw Parsing Result (JSON):</h4>";
echo "<pre class='text-sm overflow-auto'>" . json_encode($parsed, JSON_PRETTY_PRINT) . "</pre>";
echo "</div>";

echo "</div>"; // Close glass-card

// Test database storage simulation
echo "<div class='glass-card rounded-2xl p-8 mb-8'>";
echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
echo "<i class='fas fa-database mr-3 icon-accent'></i>";
echo "Database Storage Simulation";
echo "</h2>";

try {
    $dbManager = new DatabaseManager();
    
    // Create mock results for testing
    $mockResults = [
        'upload' => ['success' => true, 'filename' => 'audio_test123.mp3', 'size' => 1024000],
        'transcription' => ['success' => true, 'transcription' => 'Sample transcription text'],
        'translation' => ['success' => true, 'translation' => 'Sample English translation'],
        'sentiment_analysis' => [
            'success' => true,
            'analysis' => [
                'sentiment_score' => ['numerical_score' => 75, 'confidence' => 0.85],
                'overall_sentiment' => ['primary_sentiment' => 'positive', 'emotional_tone' => 'Professional'],
                'agent_performance' => [
                    'clarity_score' => 80,
                    'empathy_score' => 75,
                    'professionalism_score' => 90,
                    'call_opening_score' => 85,
                    'call_quality_score' => 78,
                    'call_closing_score' => 88,
                    'overall_performance' => 'good',
                    'strengths' => ['Clear communication'],
                    'areas_for_improvement' => ['Response time'],
                    'recommendations' => ['Continue professional approach'],
                    'call_structure_analysis' => [
                        'opening_assessment' => 'Professional greeting',
                        'quality_assessment' => 'Clear communication',
                        'closing_assessment' => 'Appropriate closure'
                    ]
                ]
            ]
        ],
        'conversation_summary' => ['success' => true, 'summary' => 'Sample conversation summary'],
        'errors' => []
    ];
    
    echo "<div class='bg-blue-50 rounded-lg p-6 border border-blue-200 mb-6'>";
    echo "<h3 class='text-lg font-semibold text-blue-800 mb-3'>Storage Test Parameters</h3>";
    echo "<div class='grid grid-cols-1 md:grid-cols-3 gap-4 text-sm'>";
    echo "<div><strong>Generated Filename:</strong> audio_test123.mp3</div>";
    echo "<div><strong>Original Filename:</strong> " . htmlspecialchars($testFilename) . "</div>";
    echo "<div><strong>File Size:</strong> 1,024,000 bytes</div>";
    echo "</div>";
    echo "</div>";
    
    // Test the storage (but don't actually store to avoid cluttering database)
    echo "<div class='bg-yellow-50 rounded-lg p-4 border border-yellow-200'>";
    echo "<i class='fas fa-info-circle text-yellow-600 mr-2'></i>";
    echo "<span class='text-yellow-800'>Database storage test would call:</span><br>";
    echo "<code class='text-sm bg-white p-2 rounded mt-2 block'>";
    echo "\$dbManager->storeAnalysisResults(\$results, 'audio_test123.mp3', 1024000, '" . htmlspecialchars($testFilename) . "');";
    echo "</code>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='bg-red-50 rounded-lg p-4 border border-red-200'>";
    echo "<i class='fas fa-times-circle text-red-600 mr-2'></i>";
    echo "<span class='text-red-800 font-semibold'>Database Manager Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    echo "</div>";
}

echo "</div>"; // Close glass-card

// Instructions
echo "<div class='glass-card rounded-2xl p-8'>";
echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
echo "<i class='fas fa-lightbulb mr-3 icon-accent'></i>";
echo "Next Steps";
echo "</h2>";

echo "<div class='space-y-4'>";
echo "<div class='bg-green-50 rounded-lg p-4 border border-green-200'>";
echo "<h3 class='font-semibold text-green-800 mb-2'>1. Check Database Columns</h3>";
echo "<p class='text-green-700 text-sm'>Visit <a href='check-database-columns.php' class='underline font-semibold'>check-database-columns.php</a> to verify if filename parsing columns exist in your database.</p>";
echo "</div>";

echo "<div class='bg-blue-50 rounded-lg p-4 border border-blue-200'>";
echo "<h3 class='font-semibold text-blue-800 mb-2'>2. Run Database Migration</h3>";
echo "<p class='text-blue-700 text-sm'>If columns are missing, run the <code>add-filename-parsing-columns.sql</code> script in your MySQL database.</p>";
echo "</div>";

echo "<div class='bg-purple-50 rounded-lg p-4 border border-purple-200'>";
echo "<h3 class='font-semibold text-purple-800 mb-2'>3. Test File Upload</h3>";
echo "<p class='text-purple-700 text-sm'>Upload a file with the pattern <code>9080093260_English_Nisarga_20251022164012.mp3</code> to test the complete flow.</p>";
echo "</div>";
echo "</div>";

echo "</div>"; // Close glass-card

echo "</div>"; // Close container

include 'includes/footer.php';
?>
