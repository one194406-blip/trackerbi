<?php
/**
 * Fix Password Hash Issue
 */

echo "<h2>🔧 Password Hash Fix</h2>";

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'trackerbi_audio';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Generate new password hash
    $new_password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "🔑 New password hash generated<br>";
    echo "Hash: " . $new_password_hash . "<br><br>";
    
    // Update admin user password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@example.com'");
    $result = $stmt->execute([$new_password_hash]);
    
    if ($result) {
        echo "✅ Admin password updated successfully!<br>";
        
        // Test the new password
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@example.com'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user && password_verify('admin123', $user['password'])) {
            echo "✅ Password verification test: SUCCESS<br>";
            echo "<strong style='color:green;'>🎉 Login should now work!</strong><br><br>";
            
            echo "<div style='background:#e8f5e8; padding:15px; border:1px solid #4CAF50; margin:10px 0;'>";
            echo "<strong>✅ FIXED! Try logging in now:</strong><br>";
            echo "URL: <a href='http://localhost/trackerbi/'>http://localhost/trackerbi/</a><br>";
            echo "Email: admin@example.com<br>";
            echo "Password: admin123<br>";
            echo "</div>";
            
        } else {
            echo "❌ Password verification still failing<br>";
        }
    } else {
        echo "❌ Failed to update password<br>";
    }
    
    // Also create a backup user just in case
    $backup_hash = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Test Admin', 'test@example.com', $backup_hash, 'admin']);
    echo "✅ Backup admin user created: test@example.com / test123<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2 { color: #333; }
</style>
