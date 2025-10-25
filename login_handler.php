<?php
/**
 * Login Handler
 * Processes login form submissions and validates user credentials
 */

require_once 'DatabaseManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = 'Please enter both email and password.';
        return;
    }
    
    try {
        // Get database connection using the same robust manager as audio analysis
        $dbManager = new DatabaseManager();
        $connectionTest = $dbManager->testConnection();
        
        if (!$connectionTest['success']) {
            throw new Exception('Database connection failed: ' . $connectionTest['message']);
        }
        
        // Get the PDO connection from DatabaseManager
        $reflection = new ReflectionClass($dbManager);
        $connectionProperty = $reflection->getProperty('connection');
        $connectionProperty->setAccessible(true);
        $pdo = $connectionProperty->getValue($dbManager);
        
        // Prepare and execute query to find user
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Check if user exists and password is correct (plain text comparison)
        if ($user && $password === $user['password']) {
            // Password is correct, start session
            session_regenerate_id(true); // Prevent session fixation
            
            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Update last login time (optional)
            $updateStmt = $pdo->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Redirect to dashboard
            header('Location: product-dashboard.php');
            exit();
            
        } else {
            // Invalid credentials
            $error_message = 'Invalid login credentials.';
        }
        
    } catch (PDOException $e) {
        // Log error and show generic message
        error_log("Login PDO error: " . $e->getMessage());
        $error_message = 'Database connection failed. Please try again later.';
    } catch (Exception $e) {
        // Log error and show generic message
        error_log("Login error: " . $e->getMessage());
        $error_message = 'Database connection failed. Please try again later.';
    }
}
?>
