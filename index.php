<?php
/**
 * ProductAuth Landing Page with TrackerBI Integration
 * Secure Business Management Platform
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
    <title>TrackerBI - AI-Powered Audio Analysis Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section {
            padding: 80px 0;
        }

        .section:nth-child(even) {
            background: #f9fafb;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            color: white;
            padding: 140px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            pointer-events: none;
            animation: slide 20s linear infinite;
        }

        @keyframes slide {
            0% { transform: translateX(0); }
            100% { transform: translateX(20px); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 32px;
            line-height: 1.1;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-tagline {
            font-size: 1.4rem;
            margin-bottom: 48px;
            opacity: 0.95;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 400;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .cta-button {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 18px 40px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.6s both;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #1f2937;
            margin-bottom: 60px;
            font-weight: 700;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            margin-bottom: 60px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            padding: 48px 36px;
            border-radius: 24px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.08),
                0 1px 0px rgba(255, 255, 255, 0.5) inset;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
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

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(59, 130, 246, 0.1),
                0 1px 0px rgba(255, 255, 255, 0.6) inset;
            border-color: rgba(59, 130, 246, 0.2);
            background: rgba(255, 255, 255, 0.9);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            border: 1px solid rgba(59, 130, 246, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .feature-icon svg {
            width: 32px;
            height: 32px;
            color: #3b82f6;
            transition: all 0.3s ease;
        }

        .feature-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: translateY(-4px) scale(1.05);
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.2);
        }

        .feature-card:hover .feature-icon svg {
            color: white;
            transform: scale(1.1);
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
            transition: color 0.3s ease;
        }

        .feature-card:hover .feature-title {
            color: #3b82f6;
        }

        .feature-description {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Login Section */
        .login-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }

        .login-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23e2e8f0" stroke-width="0.5" opacity="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }

        .login-container {
            max-width: 520px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 56px;
            border-radius: 32px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.12),
                0 8px 32px rgba(0, 0, 0, 0.08),
                0 1px 0px rgba(255, 255, 255, 0.8) inset;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }

        .login-title {
            text-align: center;
            font-size: 2.25rem;
            color: #1e293b;
            margin-bottom: 40px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(10px);
            font-weight: 500;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 
                0 0 0 4px rgba(59, 130, 246, 0.1),
                0 8px 25px rgba(59, 130, 246, 0.15);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 20px;
            border: none;
            border-radius: 16px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 16px;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 500;
            border: 1px solid #fecaca;
        }

        .admin-note {
            text-align: center;
            margin-top: 32px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            color: #64748b;
            font-size: 0.9rem;
            border: 1px solid #e2e8f0;
        }

        .admin-note strong {
            color: #2563eb;
        }

        .trackerbi-link {
            text-align: center;
            margin-top: 24px;
            padding: 20px;
            background: #eff6ff;
            border-radius: 12px;
            border: 1px solid #dbeafe;
        }

        .trackerbi-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .trackerbi-link a:hover {
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            background: #ffffff;
            color: #64748b;
            padding: 40px 0;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
        }
        
        .footer-logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .footer-logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-links {
            display: flex;
            gap: 32px;
        }

        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #3b82f6;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 80px 0;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .section {
                padding: 60px 0;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .login-container {
                margin: 0 20px;
                padding: 32px;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero-tagline {
                font-size: 1.1rem;
            }

            .feature-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <!-- Logo Section in Hero -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 32px;">
                    <div style="width: 64px; height: 64px; background: rgba(255, 255, 255, 0.15); border-radius: 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(20px); border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor" style="color: white;">
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
                </div>
                <h1>TrackerBI: Transforming Audio into Actionable Intelligence</h1>
                <p class="hero-tagline">
                    Harness the power of AI to analyze conversations, track performance, and unlock valuable business insights from your audio data.
                </p>
                <a href="#access" class="cta-button">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Why Choose TrackerBI?</h2>
            <p class="section-subtitle">
                Discover how our AI-powered platform transforms your audio data into actionable business intelligence
            </p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                            <line x1="12" y1="19" x2="12" y2="23"/>
                            <line x1="8" y1="23" x2="16" y2="23"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">AI Audio Analysis</h3>
                    <p class="feature-description">
                        Advanced sentiment analysis and emotion detection for call quality improvement
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            <circle cx="15" cy="7" r="3"/>
                            <path d="M13 2l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Performance Tracking</h3>
                    <p class="feature-description">
                        Comprehensive agent scoring and quality metrics with detailed assessments
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                            <circle cx="18" cy="7" r="3"/>
                            <circle cx="6" cy="11" r="3"/>
                            <circle cx="12" cy="1" r="3"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Real-time Analytics</h3>
                    <p class="feature-description">
                        Interactive dashboards with performance trends and detailed reporting
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            <circle cx="12" cy="8" r="2"/>
                            <circle cx="8" cy="16" r="2"/>
                            <circle cx="16" cy="16" r="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Multi-language Support</h3>
                    <p class="feature-description">
                        Automatic transcription and translation with high accuracy processing
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <rect x="7" y="7" width="3" height="9"/>
                            <rect x="14" y="7" width="3" height="5"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Meta Dashboard</h3>
                    <p class="feature-description">
                        Executive-level business intelligence with cross-system analytics
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="7.5,4.21 12,6.81 16.5,4.21"/>
                            <polyline points="7.5,19.79 7.5,14.6 3,12"/>
                            <polyline points="21,12 16.5,14.6 16.5,19.79"/>
                            <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Deep Insights</h3>
                    <p class="feature-description">
                        Advanced conversation analysis with actionable recommendations
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="section login-section" id="access">
        <div class="container">
            <div class="login-container">
                <h2 class="login-title">Access TrackerBI</h2>
                
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

                    <button type="submit" class="login-btn">Launch Dashboard</button>
                </form>

                <div class="admin-note">
                    <strong>Secure Access:</strong> User accounts are managed by administrators.<br>
                    Contact your TrackerBI admin for account creation.
                </div>

                <!-- <div class="trackerbi-link">
                    <strong>Quick Audio Analysis</strong><br>
                    <a href="trackerbi-audio.php">Try TrackerBI without login</a>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <div class="footer-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
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
                    <span class="footer-logo-text">TrackerBI</span>
                </div>
                <div class="footer-links">
                    <a href="#access">Login</a>
                    <!-- <a href="trackerbi-audio.php">Audio Analysis</a> -->
                    <a href="product-dashboard.php">Dashboard</a>
                    <a href="register.php">Contact</a>
                </div>
            </div>
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; font-size: 0.9rem;">
                &copy; <?php echo date('Y'); ?> TrackerBI Platform. All rights reserved. | Transforming Audio into Actionable Intelligence
            </div>
        </div>
    </footer>
</body>
</html>
