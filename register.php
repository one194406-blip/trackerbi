<?php
/**
 * User Registration (Admin Only)
 * Allows administrators to create new user accounts
 */

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once 'db_connect.php';

$success_message = '';
$error_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Get database connection
            $pdo = getDbConnection();
            
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                $error_message = 'Email address already exists.';
            } else {
                // Insert new user (plain text password as per your requirement)
                $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $result = $insertStmt->execute([$name, $email, $password, $role]);
                
                if ($result) {
                    $success_message = "User '$name' has been successfully registered with email '$email'.";
                    // Clear form
                    $name = $email = $password = '';
                    $role = 'user';
                } else {
                    $error_message = 'Registration failed. Please try again.';
                }
            }
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $error_message = 'Registration failed. Please try again later.';
        }
    }
}

// Get current users for display
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - TrackerBI Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4f46e5;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-link {
            background: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .nav-link:hover {
            background: #3730a3;
        }

        .nav-link.secondary {
            background: #6b7280;
        }

        .nav-link.secondary:hover {
            background: #4b5563;
        }

        /* Form Section */
        .form-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-badge {
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .form-button {
            background: #4f46e5;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        .form-button:hover {
            background: #3730a3;
        }

        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Users List */
        .users-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .users-table th,
        .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .users-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .role-user {
            background: #dbeafe;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">👥 User Registration</div>
            <div class="nav-links">
                <a href="product-dashboard.php" class="nav-link">← Dashboard</a>
                <a href="admin-panel.php" class="nav-link secondary">Admin Panel</a>
                <a href="logout.php" class="nav-link secondary">Logout</a>
            </div>
        </header>

        <!-- Registration Form -->
        <section class="form-section">
            <h1 class="section-title">
                ➕ Create New User
                <span class="admin-badge">Admin Only</span>
            </h1>

            <?php if ($success_message): ?>
                <div class="message success">✅ <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="message error">❌ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="form-grid">
                <div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" id="name" name="name" class="form-input" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-input" 
                                   minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">User Role</label>
                            <select id="role" name="role" class="form-select">
                                <option value="user" <?php echo (($role ?? 'user') === 'user') ? 'selected' : ''; ?>>Regular User</option>
                                <option value="admin" <?php echo (($role ?? 'user') === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>

                        <button type="submit" class="form-button">Create User Account</button>
                    </form>
                </div>

                <div>
                    <h3 style="color: #374151; margin-bottom: 15px;">📋 Registration Guidelines</h3>
                    <ul style="color: #6b7280; line-height: 1.6;">
                        <li>• All fields marked with * are required</li>
                        <li>• Email addresses must be unique</li>
                        <li>• Passwords must be at least 6 characters</li>
                        <li>• Admin users have full system access</li>
                        <li>• Regular users have limited access</li>
                        <li>• Users can be managed in the Admin Panel</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Current Users -->
        <section class="users-section">
            <h2 class="section-title">👥 Current Users (<?php echo count($users); ?>)</h2>
            
            <?php if (count($users) > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #6b7280; text-align: center; padding: 20px;">No users found.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
