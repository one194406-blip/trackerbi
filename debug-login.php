<?php
/**
 * Debug Login Issues
 */

echo "<h2>🔍 Login Debug Test</h2>";

// Test 1: Database Connection
echo "<h3>1. Database Connection Test</h3>";
try {
    $host = 'localhost';
    $dbname = 'trackerbi_audio';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection: SUCCESS<br>";
    
    // Test 2: Check if database exists
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    echo "📍 Current database: " . $result['current_db'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database connection FAILED: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Check if users table exists
echo "<h3>2. Users Table Test</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table: EXISTS<br>";
        
        // Test 4: Check users in table
        $stmt = $pdo->query("SELECT id, name, email, role FROM users");
        $users = $stmt->fetchAll();
        
        if (count($users) > 0) {
            echo "✅ Users found: " . count($users) . "<br>";
            echo "<table border='1' style='margin:10px 0;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['name'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "❌ No users found in table<br>";
        }
        
    } else {
        echo "❌ Users table: NOT FOUND<br>";
        echo "<strong>🔧 Run this SQL in phpMyAdmin:</strong><br>";
        echo "<textarea rows='10' cols='80' style='margin:10px 0;'>";
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
        echo "('Admin User', 'admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');\n";
        echo "</textarea>";
        exit;
    }
    
} catch (Exception $e) {
    echo "❌ Table check failed: " . $e->getMessage() . "<br>";
}

// Test 5: Password verification test
echo "<h3>3. Password Verification Test</h3>";
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ Admin user found<br>";
        echo "📧 Email: " . $user['email'] . "<br>";
        echo "👤 Name: " . $user['name'] . "<br>";
        
        // Test password
        if (password_verify('admin123', $user['password'])) {
            echo "✅ Password verification: SUCCESS<br>";
            echo "<strong style='color:green;'>🎉 Login should work!</strong><br>";
        } else {
            echo "❌ Password verification: FAILED<br>";
            echo "🔧 Password hash issue detected<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ User check failed: " . $e->getMessage() . "<br>";
}

// Test 6: Login form test
echo "<h3>4. Quick Login Test</h3>";
echo "<form method='POST' style='border:1px solid #ccc; padding:15px; margin:10px 0;'>";
echo "<strong>Test Login:</strong><br>";
echo "Email: <input type='email' name='email' value='admin@example.com' style='margin:5px;'><br>";
echo "Password: <input type='password' name='password' value='admin123' style='margin:5px;'><br>";
echo "<input type='submit' name='test_login' value='Test Login' style='margin:5px; padding:5px 10px;'>";
echo "</form>";

if (isset($_POST['test_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    echo "<h4>Login Test Result:</h4>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            echo "<div style='color:green; font-weight:bold;'>✅ LOGIN SUCCESS!</div>";
            echo "User: " . $user['name'] . " (" . $user['role'] . ")<br>";
        } else {
            echo "<div style='color:red; font-weight:bold;'>❌ LOGIN FAILED!</div>";
            if (!$user) {
                echo "User not found with email: " . $email . "<br>";
            } else {
                echo "Password verification failed<br>";
            }
        }
    } catch (Exception $e) {
        echo "<div style='color:red;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2, h3 { color: #333; }
table { border-collapse: collapse; }
th, td { padding: 8px; text-align: left; }
</style>
