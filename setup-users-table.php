<?php
/**
 * Setup Users Table for Login System
 * Creates the users table in the trackerbi_audio database
 */

require_once 'DatabaseManager.php';

try {
    $dbManager = new DatabaseManager();
    $connectionTest = $dbManager->testConnection();
    
    if (!$connectionTest['success']) {
        die("Database connection failed: " . $connectionTest['message']);
    }
    
    echo "<h2>Setting up Users Table...</h2>";
    
    // Get the PDO connection
    $reflection = new ReflectionClass($dbManager);
    $connectionProperty = $reflection->getProperty('connection');
    $connectionProperty->setAccessible(true);
    $pdo = $connectionProperty->getValue($dbManager);
    
    // Create users table
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createTableSQL);
    echo "<p>✅ Users table created successfully!</p>";
    
    // Check if admin user exists
    $checkAdmin = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $checkAdmin->execute(['admin@trackerbi.com']);
    $adminExists = $checkAdmin->fetch()['count'] > 0;
    
    if (!$adminExists) {
        // Insert default admin user
        $insertAdmin = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $insertAdmin->execute([
            'Admin User',
            'admin@trackerbi.com',
            'admin123', // Plain text password for now
            'admin'
        ]);
        echo "<p>✅ Default admin user created!</p>";
        echo "<p><strong>Login Credentials:</strong><br>";
        echo "Email: admin@trackerbi.com<br>";
        echo "Password: admin123</p>";
    } else {
        echo "<p>ℹ️ Admin user already exists.</p>";
    }
    
    // Check if demo user exists
    $checkUser = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $checkUser->execute(['user@trackerbi.com']);
    $userExists = $checkUser->fetch()['count'] > 0;
    
    if (!$userExists) {
        // Insert default demo user
        $insertUser = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $insertUser->execute([
            'Demo User',
            'user@trackerbi.com',
            'user123', // Plain text password for now
            'user'
        ]);
        echo "<p>✅ Default demo user created!</p>";
        echo "<p><strong>Demo User Credentials:</strong><br>";
        echo "Email: user@trackerbi.com<br>";
        echo "Password: user123</p>";
    } else {
        echo "<p>ℹ️ Demo user already exists.</p>";
    }
    
    // Show current users
    $users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();
    
    echo "<h3>Current Users:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><p><strong>✅ Setup Complete!</strong></p>";
    echo "<p>You can now use the login system with the credentials above.</p>";
    echo "<p><a href='product-landing.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Setup Failed</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration and try again.</p>";
}
?>
