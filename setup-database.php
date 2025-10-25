<?php
/**
 * Database Setup Tool for TrackerBI
 * This script creates the required database and tables
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

echo "<!DOCTYPE html>";
echo "<html><head><title>TrackerBI Database Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:15px;border-radius:5px;}</style>";
echo "</head><body>";

echo "<h1>🗄️ TrackerBI Database Setup</h1>";

try {
    // Connect to MySQL server (without specifying database)
    echo "<div class='info'>📡 Connecting to MySQL server...</div>";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Connected to MySQL server successfully!</div><br>";
    
    // Read the SQL setup file
    echo "<div class='info'>📖 Reading database setup script...</div>";
    $sqlFile = __DIR__ . '/database_setup.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Database setup file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    echo "<div class='success'>✅ Database setup script loaded successfully!</div><br>";
    
    // Split SQL into individual statements
    echo "<div class='info'>⚙️ Executing database setup...</div>";
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $totalStatements = count($statements);
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            // Some statements might fail if they already exist, which is okay
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "<div class='error'>⚠️ Warning: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
    
    echo "<div class='success'>✅ Database setup completed! ($successCount statements executed)</div><br>";
    
    // Test the connection to the new database
    echo "<div class='info'>🔍 Testing connection to trackerbi_audio database...</div>";
    $testPdo = new PDO("mysql:host=$host;dbname=trackerbi_audio", $username, $password);
    $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check tables
    $stmt = $testPdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='success'>✅ Successfully connected to trackerbi_audio database!</div>";
    echo "<div class='info'>📋 Tables created:</div>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul><br>";
    
    // Check sample data
    echo "<div class='info'>📊 Checking sample data...</div>";
    
    $stmt = $testPdo->query("SELECT COUNT(*) FROM audio_analysis_results");
    $audioCount = $stmt->fetchColumn();
    
    $stmt = $testPdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    echo "<div class='success'>✅ Sample data loaded:</div>";
    echo "<ul>";
    echo "<li>Audio Analysis Records: $audioCount</li>";
    echo "<li>User Accounts: $userCount</li>";
    echo "</ul><br>";
    
    // Show login credentials
    echo "<div class='info'>🔑 <strong>Login Credentials for Testing:</strong></div>";
    echo "<pre>";
    echo "Admin Users:\n";
    echo "  • admin@example.com / admin123\n";
    echo "  • sarah@example.com / admin123\n\n";
    echo "Regular Users:\n";
    echo "  • user@example.com / user123\n";
    echo "  • jane@example.com / user123\n";
    echo "  • mike@example.com / user123\n";
    echo "</pre>";
    
    echo "<div class='success'><h2>🎉 Database Setup Complete!</h2></div>";
    echo "<div class='info'>You can now use the TrackerBI audio analysis system.</div>";
    echo "<br><a href='index.php' style='background:#007cba;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to TrackerBI Login</a>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>Please make sure:</div>";
    echo "<ul>";
    echo "<li>XAMPP MySQL is running</li>";
    echo "<li>Database credentials are correct</li>";
    echo "<li>You have permission to create databases</li>";
    echo "</ul>";
}

echo "</body></html>";
?>
