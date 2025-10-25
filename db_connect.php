<?php
/**
 * Database Connection Configuration
 * Simple and secure database connection using PDO
 */

// Database configuration
$host = 'localhost';
$dbname = 'trackerbi_audio';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

/**
 * Function to get database connection
 * @return PDO Database connection object
 */
function getDbConnection() {
    global $pdo;
    return $pdo;
}
?>
