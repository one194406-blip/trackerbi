<?php
require_once 'config.php';

// Set page title for header
$page_title = 'Run Filename Migration';

// Include common header
include 'includes/header.php';

echo "<div class='gradient-bg py-16'>
        <div class='container mx-auto px-6'>
            <h1 class='text-5xl font-bold text-white text-center mb-4'>
                <i class='fas fa-database mr-4 icon-accent'></i>
                Run Filename Migration
            </h1>
            <p class='text-white text-center text-xl opacity-90 max-w-2xl mx-auto'>
                Add filename parsing columns to the database
            </p>
        </div>
    </div>";

echo "<div class='container mx-auto px-6 py-12'>";

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    echo "<div class='glass-card rounded-2xl p-8 mb-8'>";
    echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
    echo "<i class='fas fa-cogs mr-3 icon-accent'></i>";
    echo "Database Migration Progress";
    echo "</h2>";
    
    // Check if columns already exist
    echo "<div class='bg-blue-50 rounded-lg p-4 border border-blue-200 mb-6'>";
    echo "<h3 class='text-lg font-semibold text-blue-800 mb-3'>Step 1: Checking existing columns</h3>";
    
    $stmt = $pdo->query("DESCRIBE audio_analysis_results");
    $columns = $stmt->fetchAll();
    
    $existingColumns = [];
    foreach ($columns as $column) {
        $existingColumns[] = $column['Field'];
    }
    
    $requiredColumns = [
        'phone_number',
        'call_language',
        'caller_name', 
        'call_date',
        'call_time',
        'original_filename',
        'filename_parsed'
    ];
    
    $missingColumns = [];
    foreach ($requiredColumns as $col) {
        if (!in_array($col, $existingColumns)) {
            $missingColumns[] = $col;
        }
    }
    
    if (empty($missingColumns)) {
        echo "<i class='fas fa-check-circle text-green-600 mr-2'></i>";
        echo "<span class='text-green-800'>All filename parsing columns already exist!</span>";
    } else {
        echo "<i class='fas fa-exclamation-triangle text-yellow-600 mr-2'></i>";
        echo "<span class='text-yellow-800'>Missing columns: " . implode(', ', $missingColumns) . "</span>";
    }
    echo "</div>";
    
    // Add missing columns
    if (!empty($missingColumns)) {
        echo "<div class='bg-yellow-50 rounded-lg p-4 border border-yellow-200 mb-6'>";
        echo "<h3 class='text-lg font-semibold text-yellow-800 mb-3'>Step 2: Adding missing columns</h3>";
        
        try {
            $sql = "ALTER TABLE audio_analysis_results 
                    ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) NULL COMMENT 'Extracted phone number from filename',
                    ADD COLUMN IF NOT EXISTS call_language VARCHAR(50) NULL COMMENT 'Extracted language from filename', 
                    ADD COLUMN IF NOT EXISTS caller_name VARCHAR(100) NULL COMMENT 'Extracted caller/agent name from filename',
                    ADD COLUMN IF NOT EXISTS call_date DATE NULL COMMENT 'Extracted call date from filename timestamp',
                    ADD COLUMN IF NOT EXISTS call_time TIME NULL COMMENT 'Extracted call time from filename timestamp',
                    ADD COLUMN IF NOT EXISTS original_filename VARCHAR(500) NULL COMMENT 'Original uploaded filename before processing',
                    ADD COLUMN IF NOT EXISTS filename_parsed BOOLEAN DEFAULT FALSE COMMENT 'Whether filename has been successfully parsed'";
            
            $pdo->exec($sql);
            
            echo "<i class='fas fa-check-circle text-green-600 mr-2'></i>";
            echo "<span class='text-green-800'>Columns added successfully!</span>";
            
        } catch (Exception $e) {
            echo "<i class='fas fa-times-circle text-red-600 mr-2'></i>";
            echo "<span class='text-red-800'>Error adding columns: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
        echo "</div>";
    }
    
    // Add indexes
    echo "<div class='bg-green-50 rounded-lg p-4 border border-green-200 mb-6'>";
    echo "<h3 class='text-lg font-semibold text-green-800 mb-3'>Step 3: Adding indexes</h3>";
    
    try {
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_phone_number ON audio_analysis_results(phone_number)",
            "CREATE INDEX IF NOT EXISTS idx_call_language ON audio_analysis_results(call_language)",
            "CREATE INDEX IF NOT EXISTS idx_caller_name ON audio_analysis_results(caller_name)",
            "CREATE INDEX IF NOT EXISTS idx_call_date ON audio_analysis_results(call_date)",
            "CREATE INDEX IF NOT EXISTS idx_call_time ON audio_analysis_results(call_time)",
            "CREATE INDEX IF NOT EXISTS idx_filename_parsed ON audio_analysis_results(filename_parsed)"
        ];
        
        foreach ($indexes as $indexSql) {
            $pdo->exec($indexSql);
        }
        
        echo "<i class='fas fa-check-circle text-green-600 mr-2'></i>";
        echo "<span class='text-green-800'>Indexes added successfully!</span>";
        
    } catch (Exception $e) {
        echo "<i class='fas fa-times-circle text-red-600 mr-2'></i>";
        echo "<span class='text-red-800'>Error adding indexes: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
    echo "</div>";
    
    // Final verification
    echo "<div class='bg-blue-50 rounded-lg p-4 border border-blue-200'>";
    echo "<h3 class='text-lg font-semibold text-blue-800 mb-3'>Step 4: Final verification</h3>";
    
    $stmt = $pdo->query("DESCRIBE audio_analysis_results");
    $finalColumns = $stmt->fetchAll();
    
    $finalColumnNames = [];
    foreach ($finalColumns as $column) {
        $finalColumnNames[] = $column['Field'];
    }
    
    $allPresent = true;
    foreach ($requiredColumns as $col) {
        if (!in_array($col, $finalColumnNames)) {
            $allPresent = false;
            break;
        }
    }
    
    if ($allPresent) {
        echo "<i class='fas fa-check-circle text-green-600 mr-2'></i>";
        echo "<span class='text-green-800 font-semibold'>Migration completed successfully! All filename parsing columns are now available.</span>";
    } else {
        echo "<i class='fas fa-times-circle text-red-600 mr-2'></i>";
        echo "<span class='text-red-800 font-semibold'>Migration incomplete. Some columns are still missing.</span>";
    }
    echo "</div>";
    
    echo "</div>"; // Close glass-card
    
    // Show next steps
    echo "<div class='glass-card rounded-2xl p-8'>";
    echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
    echo "<i class='fas fa-arrow-right mr-3 icon-accent'></i>";
    echo "Next Steps";
    echo "</h2>";
    
    echo "<div class='space-y-4'>";
    echo "<div class='bg-green-50 rounded-lg p-4 border border-green-200'>";
    echo "<h3 class='font-semibold text-green-800 mb-2'>1. Test Filename Parsing</h3>";
    echo "<p class='text-green-700 text-sm'>Visit <a href='test-filename-storage.php' class='underline font-semibold'>test-filename-storage.php</a> to test the filename parsing functionality.</p>";
    echo "</div>";
    
    echo "<div class='bg-blue-50 rounded-lg p-4 border border-blue-200'>";
    echo "<h3 class='font-semibold text-blue-800 mb-2'>2. Check Database Records</h3>";
    echo "<p class='text-blue-700 text-sm'>Visit <a href='check-database-columns.php' class='underline font-semibold'>check-database-columns.php</a> to see current database records.</p>";
    echo "</div>";
    
    echo "<div class='bg-purple-50 rounded-lg p-4 border border-purple-200'>";
    echo "<h3 class='font-semibold text-purple-800 mb-2'>3. Upload Test File</h3>";
    echo "<p class='text-purple-700 text-sm'>Go to <a href='trackerbi-audio.php' class='underline font-semibold'>trackerbi-audio.php</a> and upload a file with pattern: <code>9080093260_English_Nisarga_20251022164012.mp3</code></p>";
    echo "</div>";
    echo "</div>";
    
    echo "</div>"; // Close glass-card
    
} catch (Exception $e) {
    echo "<div class='glass-card rounded-2xl p-8'>";
    echo "<div class='bg-red-50 rounded-lg p-6 border border-red-200'>";
    echo "<i class='fas fa-times-circle text-red-600 mr-3'></i>";
    echo "<span class='text-red-800 font-semibold'>Database Connection Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    echo "</div>";
    echo "</div>";
}

echo "</div>"; // Close container

include 'includes/footer.php';
?>
