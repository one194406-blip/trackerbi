<?php
/**
 * TrackerBI Enhanced Dashboard
 * Modern navigation hub with advanced features
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user information from session
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
$login_time = $_SESSION['login_time'];

// Calculate session duration
$session_duration = time() - $login_time;
$hours = floor($session_duration / 3600);
$minutes = floor(($session_duration % 3600) / 60);

// Mock data for enhanced features
$recent_activities = [
    ['action' => 'Audio Analysis Completed', 'file' => 'customer_call_001.mp3', 'time' => '2 minutes ago', 'icon' => 'fas fa-microphone', 'color' => 'text-green-600'],
    ['action' => 'Dashboard Viewed', 'file' => 'Analytics Dashboard', 'time' => '15 minutes ago', 'icon' => 'fas fa-chart-bar', 'color' => 'text-blue-600'],
    ['action' => 'Profile Updated', 'file' => 'User Settings', 'time' => '1 hour ago', 'icon' => 'fas fa-user-cog', 'color' => 'text-purple-600'],
    ['action' => 'Report Generated', 'file' => 'Monthly Report', 'time' => '2 hours ago', 'icon' => 'fas fa-file-alt', 'color' => 'text-orange-600']
];

$notifications = [
    ['title' => 'New Analysis Complete', 'message' => 'Your audio analysis for customer_call_001.mp3 is ready', 'time' => '5 min ago', 'type' => 'success'],
    ['title' => 'System Update', 'message' => 'TrackerBI has been updated to version 2.1.0', 'time' => '1 hour ago', 'type' => 'info'],
    ['title' => 'Storage Warning', 'message' => 'You are using 85% of your storage quota', 'time' => '3 hours ago', 'type' => 'warning']
];

$system_health = [
    'api_status' => 'online',
    'database_status' => 'online', 
    'storage_usage' => 75,
    'active_users' => 12,
    'cpu_usage' => 45,
    'memory_usage' => 62
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TrackerBI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            color: #1f2937;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Modern Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 24px 32px;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .action-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #64748b;
            position: relative;
        }
        
        .action-btn:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            transform: translateY(-2px);
        }
        
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: 600;
        }
        
        .search-container {
            position: relative;
            width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .user-role {
            font-size: 0.9rem;
            color: #6b7280;
            text-transform: capitalize;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        /* Modern Navigation */
        .nav-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 2px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .nav-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 32px 24px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .nav-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border-color: rgba(59, 130, 246, 0.2);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .nav-card:hover::before {
            opacity: 1;
        }

        .nav-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            border: 1px solid rgba(59, 130, 246, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 28px;
            color: #3b82f6;
        }
        
        .nav-card:hover .nav-icon {
            transform: translateY(-4px) scale(1.05);
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.2);
            color: white;
        }

        .nav-card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }
        
        .nav-card:hover .nav-card-title {
            color: #3b82f6;
        }

        .nav-card-desc {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .admin-only {
            border: 2px solid #f59e0b;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            position: relative;
        }
        
        .admin-only::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 22px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .admin-only:hover::after {
            opacity: 0.1;
        }

        .admin-badge {
            background: #f59e0b;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: inline-block;
        }

        /* Welcome Section */
        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .welcome-text {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .session-info {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            border-radius: 16px;
            font-size: 0.95rem;
            color: #64748b;
            border: 1px solid rgba(226, 232, 240, 0.5);
            backdrop-filter: blur(10px);
        }

        /* Stats Section */
        .stats-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            .nav-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Modern Header -->
        <header class="header">
            <div class="logo-section">
                <div class="logo-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                        <!-- Audio waveform bars -->
                        <rect x="2" y="8" width="2" height="8" rx="1"/>
                        <rect x="6" y="4" width="2" height="16" rx="1"/>
                        <rect x="10" y="6" width="2" height="12" rx="1"/>
                        <rect x="14" y="2" width="2" height="20" rx="1"/>
                        <rect x="18" y="7" width="2" height="10" rx="1"/>
                        <!-- Analytics trend line -->
                        <path d="M2 15 L6 12 L10 14 L14 8 L18 10 L22 6" stroke="currentColor" stroke-width="2" fill="none" opacity="0.7"/>
                    </svg>
                </div>
                <span class="logo-text">TrackerBI Dashboard</span>
            </div>
            <div class="user-info">
                <!-- Search Bar -->
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search features, files, or help..." id="globalSearch">
                    <i class="fas fa-search search-icon"></i>
                </div>
                
                
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($user_role); ?> Account</div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </header>

        <!-- Welcome Section -->
        <section class="welcome-section">
            <h1 class="welcome-title">Welcome to TrackerBI, <?php echo htmlspecialchars($user_name); ?>! 🎯</h1>
            <p class="welcome-text">
                Your comprehensive audio analysis and business intelligence platform. 
                <?php if ($user_role === 'admin'): ?>
                    As an administrator, you have full access to all features including user registration and system management.
                <?php else: ?>
                    Access all TrackerBI features for audio analysis, dashboards, and analytics.
                <?php endif; ?>
            </p>
            <div class="session-info">
                <strong>Session Info:</strong> Logged in as <?php echo htmlspecialchars($user_email); ?> • 
                Session duration: <?php echo $hours; ?>h <?php echo $minutes; ?>m
            </div>
        </section>

      

        <!-- Navigation Section -->
        <section class="nav-section">
            <h2 class="nav-title">TrackerBI Features</h2>
            <div class="nav-grid">
                <!-- Audio Analysis -->
                <a href="trackerbi-audio.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <div class="nav-card-title">Audio Analysis</div>
                    <div class="nav-card-desc">Upload and analyze audio files with AI-powered sentiment analysis</div>
                </a>

                <!-- Call Dashboard -->
                <a href="call-dashboard.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="nav-card-title">Call Dashboard</div>
                    <div class="nav-card-desc">View detailed call analysis results and API response data</div>
                </a>

                <!-- Enhanced Analytics -->
                <a href="enhanced-call-dashboard.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="nav-card-title">Enhanced Analytics</div>
                    <div class="nav-card-desc">Advanced analytics with performance trends and insights</div>
                </a>

                <!-- Analytics Dashboard -->
                <a href="analytics-dashboard.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Analytics Dashboard</h3>
                    <p>Comprehensive performance analytics and insights</p>
                </a>

                <!-- Today's Analytics -->
                <a href="today-analytics.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="nav-card-title">Today's Analytics</div>
                    <div class="nav-card-desc">Real-time analysis results and performance for today</div>
                </a>

                <!-- Meta Dashboard -->
                <a href="meta-dashboard.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fab fa-meta"></i>
                    </div>
                    <div class="nav-card-title">Meta Dashboard</div>
                    <div class="nav-card-desc">High-level overview and meta analytics across all systems</div>
                </a>

                <?php if ($user_role === 'admin'): ?>
                <!-- User Registration (Admin Only) -->
                <a href="register.php" class="nav-card admin-only">
                    <div class="admin-badge">Admin Only</div>
                    <div class="nav-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="nav-card-title">Register Users</div>
                    <div class="nav-card-desc">Create new user accounts and manage registrations</div>
                </a>

                <!-- Admin Panel (Admin Only) -->
                <a href="admin-panel.php" class="nav-card admin-only">
                    <div class="admin-badge">Admin Only</div>
                    <div class="nav-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="nav-card-title">Admin Panel</div>
                    <div class="nav-card-desc">Complete user management and system administration</div>
                </a>
                <?php endif; ?>

                <!-- Profile Settings -->
                <a href="profile.php" class="nav-card">
                    <div class="nav-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="nav-card-title">Profile Settings</div>
                    <div class="nav-card-desc">Update your personal information and account preferences</div>
                </a>
            </div>
        </section>






          <!-- Quick Stats -->
        <section class="stats-section">
            <h2 class="nav-title">Quick Stats</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Active Session</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo ucfirst($user_role); ?></div>
                    <div class="stat-label">Account Type</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo date('M j', $login_time); ?></div>
                    <div class="stat-label">Login Date</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo date('H:i', $login_time); ?></div>
                    <div class="stat-label">Login Time</div>
                </div>
            </div>
        </section>



    </div>
</body>
</html>
