<?php
/**
 * Product Landing Page with Login System
 * Main entry point for the authentication system
 */

session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: product-dashboard.php');
    exit();
}

// Handle login form submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'login_handler.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductAuth - Secure Business Management Platform</title>
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
            padding: 0 20px;
        }

        /* Header Section */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 80px 0;
            text-align: center;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 20px;
        }

        .tagline {
            font-size: 1.2rem;
            color: #6b7280;
            margin-bottom: 40px;
        }

        /* Product Features Section */
        .features-section {
            padding: 60px 0;
            background: rgba(255, 255, 255, 0.9);
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            color: #1f2937;
            margin-bottom: 50px;
            font-weight: 600;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #4f46e5;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .feature-description {
            color: #6b7280;
            line-height: 1.6;
        }

        /* Login Section */
        .login-section {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.95);
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            text-align: center;
            font-size: 1.8rem;
            color: #1f2937;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .admin-note {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 8px;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .admin-note strong {
            color: #4f46e5;
        }

        .trackerbi-link {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background: #e0f2fe;
            border-radius: 8px;
            border: 1px solid #0891b2;
        }

        .trackerbi-link a {
            color: #0891b2;
            text-decoration: none;
            font-weight: 600;
        }

        .trackerbi-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 60px 0;
            }

            .logo {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .login-container {
                margin: 0 20px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <section class="header">
        <div class="container">
            <div class="logo">ProductAuth</div>
            <div class="tagline">Secure Business Management Platform</div>
        </div>
    </section>

    <!-- Product Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose ProductAuth?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔐</div>
                    <h3 class="feature-title">Advanced Security</h3>
                    <p class="feature-description">
                        Enterprise-grade security with role-based access control, encrypted data storage, and secure authentication protocols.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Comprehensive Dashboard</h3>
                    <p class="feature-description">
                        Intuitive dashboards with real-time analytics, customizable widgets, and detailed reporting capabilities.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3 class="feature-title">User Management</h3>
                    <p class="feature-description">
                        Complete user lifecycle management with admin controls, permission settings, and activity monitoring.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Lightning Fast</h3>
                    <p class="feature-description">
                        Optimized performance with fast loading times, efficient database queries, and responsive design.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Mobile Responsive</h3>
                    <p class="feature-description">
                        Fully responsive design that works seamlessly across all devices - desktop, tablet, and mobile.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🛠️</div>
                    <h3 class="feature-title">Easy Integration</h3>
                    <p class="feature-description">
                        Simple API integration, extensive documentation, and developer-friendly tools for quick setup.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <h2 class="login-title">Welcome Back</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" required 
                               placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required 
                               placeholder="Enter your password">
                    </div>

                    <button type="submit" class="login-btn">Sign In</button>
                </form>

                <div class="admin-note">
                    <strong>Note:</strong> New users can only be added by admin.<br>
                    Contact your administrator for account creation.
                </div>

                <div class="trackerbi-link">
                    <strong>🎤 Need Audio Analysis?</strong><br>
                    <a href="trackerbi-audio.php">Access TrackerBI Audio Analysis System</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
