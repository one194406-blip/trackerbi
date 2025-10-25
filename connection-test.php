<?php
echo "<h2>🔍 Database Connection Test</h2>";

// Test 1: Basic PHP
echo "✅ PHP is working<br>";
echo "📅 Current time: " . date('Y-m-d H:i:s') . "<br><br>";

// Test 2: MySQL Extension
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension: LOADED<br>";
} else {
    echo "❌ PDO MySQL extension: NOT LOADED<br>";
}

// Test 3: Database Connection
echo "<h3>Database Connection Test:</h3>";

$host = 'localhost';
$dbname = 'trackerbi_audio';
$username = 'root';
$password = '';

try {
    // Test connection without database first
    echo "🔌 Testing MySQL server connection...<br>";
    $pdo_test = new PDO("mysql:host=$host", $username, $password);
    echo "✅ MySQL server: CONNECTED<br>";
    
    // Test specific database
    echo "🗄️ Testing database '$dbname'...<br>";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database '$dbname': CONNECTED<br>";
    
    // Test if database exists
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "📍 Current database: " . $result['db_name'] . "<br>";
    
    // Test tables
    echo "<h3>Tables in Database:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "📋 Found " . count($tables) . " tables:<br>";
        foreach ($tables as $table) {
            $table_name = array_values($table)[0];
            echo "- " . $table_name . "<br>";
            
            if ($table_name === 'users') {
                // Check users table
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                $count = $stmt->fetch();
                echo "  👥 Users count: " . $count['count'] . "<br>";
            }
        }
    } else {
        echo "❌ No tables found<br>";
        echo "<strong>🔧 You need to create the tables!</strong><br>";
        echo "<a href='#create-tables'>Click here to create tables</a><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Connection FAILED<br>";
    echo "🚨 Error: " . $e->getMessage() . "<br>";
    
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<br><strong>🔧 Database doesn't exist! Create it:</strong><br>";
        echo "<div style='background:#f0f0f0; padding:10px; margin:10px 0;'>";
        echo "1. Go to phpMyAdmin: <a href='http://localhost/phpmyadmin/'>http://localhost/phpmyadmin/</a><br>";
        echo "2. Click 'New' to create database<br>";
        echo "3. Name it: <strong>trackerbi_audio</strong><br>";
        echo "4. Click 'Create'<br>";
        echo "</div>";
    }
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<br><strong>🔧 MySQL not running! Start it:</strong><br>";
        echo "1. Open XAMPP Control Panel<br>";
        echo "2. Start MySQL service<br>";
    }
}

// Create tables section
echo "<div id='create-tables' style='margin-top:30px; padding:15px; border:1px solid #ccc;'>";
echo "<h3>🔧 Create Database & Tables</h3>";
echo "<p>Copy this SQL and run it in phpMyAdmin:</p>";
echo "<textarea rows='15' cols='80' style='width:100%;'>";
echo "CREATE DATABASE IF NOT EXISTS trackerbi_audio;\n";
echo "USE trackerbi_audio;\n\n";
echo "CREATE TABLE users (\n";
echo "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
echo "    name VARCHAR(100) NOT NULL,\n";
echo "    email VARCHAR(150) UNIQUE NOT NULL,\n";
echo "    password VARCHAR(255) NOT NULL,\n";
echo "    role ENUM('admin', 'user') DEFAULT 'user',\n";
echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n";
echo ");\n\n";
echo "INSERT INTO users (name, email, password, role) VALUES \n";
echo "('Admin User', 'admin@example.com', '\$2y\$10\$EIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');\n\n";
echo "SELECT * FROM users;";
echo "</textarea>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2, h3 { color: #333; }
textarea { font-family: monospace; }
</style>
