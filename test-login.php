<?php
/**
 * Test Database Connection and User Login
 */

require_once 'db_connect.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test database connection
    $pdo = getDbConnection();
    echo "✅ Database connection: SUCCESS<br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table: EXISTS<br>";
        
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['admin@example.com']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Admin user: EXISTS<br>";
            echo "📧 Email: " . $user['email'] . "<br>";
            echo "👤 Name: " . $user['name'] . "<br>";
            echo "🔑 Role: " . $user['role'] . "<br>";
            
            // Test password verification
            if (password_verify('admin123', $user['password'])) {
                echo "✅ Password verification: SUCCESS<br>";
                echo "<strong>🎉 Login should work with admin@example.com / admin123</strong>";
            } else {
                echo "❌ Password verification: FAILED<br>";
                echo "The stored password hash doesn't match 'admin123'";
            }
        } else {
            echo "❌ Admin user: NOT FOUND<br>";
            echo "You need to run the SQL setup from database_setup.sql";
        }
    } else {
        echo "❌ Users table: NOT FOUND<br>";
        echo "You need to create the database and tables first.";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2 { color: #333; }
</style>
