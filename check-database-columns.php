<?php
require_once 'config.php';

// Set page title for header
$page_title = 'Database Column Check';

// Include common header
include 'includes/header.php';

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
    
    echo "<div class='gradient-bg py-16'>
            <div class='container mx-auto px-6'>
                <h1 class='text-5xl font-bold text-white text-center mb-4'>
                    <i class='fas fa-database mr-4 icon-accent'></i>
                    Database Column Check
                </h1>
                <p class='text-white text-center text-xl opacity-90 max-w-2xl mx-auto'>
                    Checking if filename parsing columns exist in the database
                </p>
            </div>
        </div>";
    
    echo "<div class='container mx-auto px-6 py-12'>";
    
    // Check table structure
    echo "<div class='glass-card rounded-2xl p-8 mb-8'>";
    echo "<h2 class='text-2xl font-semibold mb-6 text-primary'>";
    echo "<i class='fas fa-table mr-3 icon-accent'></i>";
    echo "Table Structure Check";
    echo "</h2>";
    
    $stmt = $pdo->query("DESCRIBE audio_analysis_results");
    $columns = $stmt->fetchAll();
    
    $filenameColumns = [
        'phone_number',
        'call_language', 
        'caller_name',
        'call_date',
        'call_time',
        'original_filename',
        'filename_parsed'
    ];
    
    echo "<div class='bg-blue-50 rounded-lg p-6 border border-blue-200 mb-6'>";
    echo "<h3 class='text-lg font-semibold text-blue-800 mb-4'>Filename Parsing Columns Status</h3>";
    
    foreach ($filenameColumns as $columnName) {
        $exists = false;
        $columnInfo = null;
        
        foreach ($columns as $column) {
            if ($column['Field'] === $columnName) {
                $exists = true;
                $columnInfo = $column;
                break;
            }
        }
        
        if ($exists) {
            echo "<div class='flex items-center mb-2'>";
            echo "<i class='fas fa-check-circle text-green-600 mr-3'></i>";
            echo "<span class='font-semibold text-green-700'>$columnName</span>";
            echo "<span class='ml-2 text-sm text-gray-600'>({$columnInfo['Type']})</span>";
            echo "</div>";
        } else {
            echo "<div class='flex items-center mb-2'>";
            echo "<i class='fas fa-times-circle text-red-600 mr-3'></i>";
            echo "<span class='font-semibold text-red-700'>$columnName</span>";
            echo "<span class='ml-2 text-sm text-red-600'>(MISSING)</span>";
            echo "</div>";
        }
    }
    echo "</div>";
    
    // Check recent records
    echo "<h3 class='text-lg font-semibold text-gray-800 mb-4'>Recent Records Check</h3>";
    
    $stmt = $pdo->query("SELECT id, session_id, filename, original_filename, phone_number, call_language, caller_name, call_date, call_time, filename_parsed, upload_timestamp FROM audio_analysis_results ORDER BY upload_timestamp DESC LIMIT 5");
    $records = $stmt->fetchAll();
    
    if (empty($records)) {
        echo "<div class='bg-yellow-50 rounded-lg p-4 border border-yellow-200'>";
        echo "<i class='fas fa-exclamation-triangle text-yellow-600 mr-2'></i>";
        echo "<span class='text-yellow-800'>No records found in database</span>";
        echo "</div>";
    } else {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='min-w-full bg-white border border-gray-200 rounded-lg'>";
        echo "<thead class='bg-gray-50'>";
        echo "<tr>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>ID</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Session ID</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Filename</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Original Filename</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Phone</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Language</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Name</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Date</th>";
        echo "<th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Parsed</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($records as $record) {
            echo "<tr class='border-t'>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['id']) . "</td>";
            echo "<td class='px-4 py-2 text-sm font-mono'>" . htmlspecialchars(substr($record['session_id'], 0, 15)) . "...</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['filename'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['original_filename'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['phone_number'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['call_language'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['caller_name'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>" . htmlspecialchars($record['call_date'] ?? 'NULL') . "</td>";
            echo "<td class='px-4 py-2 text-sm'>";
            if ($record['filename_parsed']) {
                echo "<i class='fas fa-check-circle text-green-600'></i>";
            } else {
                echo "<i class='fas fa-times-circle text-red-600'></i>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
    
    echo "</div>"; // Close glass-card
    
    // Show SQL to add missing columns
    $missingColumns = [];
    foreach ($filenameColumns as $columnName) {
        $exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $columnName) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $missingColumns[] = $columnName;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "<div class='glass-card rounded-2xl p-8'>";
        echo "<h2 class='text-2xl font-semibold mb-6 text-red-700'>";
        echo "<i class='fas fa-exclamation-triangle mr-3'></i>";
        echo "Missing Columns Detected";
        echo "</h2>";
        
        echo "<div class='bg-red-50 rounded-lg p-6 border border-red-200 mb-6'>";
        echo "<p class='text-red-800 mb-4'>The following columns are missing and need to be added:</p>";
        echo "<ul class='list-disc list-inside text-red-700'>";
        foreach ($missingColumns as $column) {
            echo "<li>$column</li>";
        }
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='bg-gray-900 text-green-400 p-4 rounded-lg'>";
        echo "<p class='text-white mb-2'>Run this SQL to add missing columns:</p>";
        echo "<pre class='text-sm overflow-auto'>";
        echo "ALTER TABLE audio_analysis_results \n";
        echo "ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) NULL COMMENT 'Extracted phone number from filename',\n";
        echo "ADD COLUMN IF NOT EXISTS call_language VARCHAR(50) NULL COMMENT 'Extracted language from filename',\n";
        echo "ADD COLUMN IF NOT EXISTS caller_name VARCHAR(100) NULL COMMENT 'Extracted caller/agent name from filename',\n";
        echo "ADD COLUMN IF NOT EXISTS call_date DATE NULL COMMENT 'Extracted call date from filename timestamp',\n";
        echo "ADD COLUMN IF NOT EXISTS call_time TIME NULL COMMENT 'Extracted call time from filename timestamp',\n";
        echo "ADD COLUMN IF NOT EXISTS original_filename VARCHAR(500) NULL COMMENT 'Original uploaded filename before processing',\n";
        echo "ADD COLUMN IF NOT EXISTS filename_parsed BOOLEAN DEFAULT FALSE COMMENT 'Whether filename has been successfully parsed';";
        echo "</pre>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='glass-card rounded-2xl p-8'>";
        echo "<div class='bg-green-50 rounded-lg p-6 border border-green-200'>";
        echo "<i class='fas fa-check-circle text-green-600 mr-3'></i>";
        echo "<span class='text-green-800 font-semibold'>All filename parsing columns are present!</span>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "</div>"; // Close container
    
} catch (Exception $e) {
    echo "<div class='container mx-auto px-6 py-12'>";
    echo "<div class='glass-card rounded-2xl p-8'>";
    echo "<div class='bg-red-50 rounded-lg p-6 border border-red-200'>";
    echo "<i class='fas fa-times-circle text-red-600 mr-3'></i>";
    echo "<span class='text-red-800 font-semibold'>Database Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

include 'includes/footer.php';
?>
