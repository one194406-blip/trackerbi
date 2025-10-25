<?php
echo "<h1>PHP Test</h1>";
echo "PHP is working: " . date('Y-m-d H:i:s') . "<br>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=trackerbi_audio", "root", "");
    echo "Database connection: SUCCESS<br>";
    
    // Check users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Users in database: " . $result['count'] . "<br>";
    
    // Check specific user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Admin user found: " . $user['name'] . "<br>";
        echo "Password hash: " . substr($user['password'], 0, 20) . "...<br>";
        
        // Test password
        if (password_verify('admin123', $user['password'])) {
            echo "<strong style='color:green;'>Password works!</strong><br>";
        } else {
            echo "<strong style='color:red;'>Password FAILED!</strong><br>";
            
            // Fix password
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@example.com'");
            $update->execute([$new_hash]);
            echo "Password updated with new hash<br>";
        }
    } else {
        echo "Admin user NOT FOUND<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
