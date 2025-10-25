<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get current page name for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_role = $is_logged_in ? $_SESSION['user_role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - TrackerBI' : 'TrackerBI'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="includes/mobile-styles.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            /* Modern Color Palette */
            --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-200: #bae6fd;
            --primary-300: #7dd3fc;
            --primary-400: #38bdf8;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --primary-800: #075985;
            --primary-900: #0c4a6e;
            
            --accent-50: #f0fdf4;
            --accent-100: #dcfce7;
            --accent-200: #bbf7d0;
            --accent-300: #86efac;
            --accent-400: #4ade80;
            --accent-500: #22c55e;
            --accent-600: #16a34a;
            --accent-700: #15803d;
            --accent-800: #166534;
            --accent-900: #14532d;
            
            --neutral-50: #fafafa;
            --neutral-100: #f5f5f5;
            --neutral-200: #e5e5e5;
            --neutral-300: #d4d4d4;
            --neutral-400: #a3a3a3;
            --neutral-500: #737373;
            --neutral-600: #525252;
            --neutral-700: #404040;
            --neutral-800: #262626;
            --neutral-900: #171717;
            
            /* Glass Morphism Variables */
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.12);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            --glass-blur: 16px;
            
            /* Modern Gradients */
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-dark: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --gradient-glass: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }
        
        /* Professional Body Styling */
        body {
            background: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
            padding-top: 40px; /* Reduced padding for tighter spacing */
        }
        
        /* Professional Navigation */
        .modern-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 70px;
            gap: 2rem;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-item {
            position: relative;
            padding: 12px 20px;
            border-radius: 16px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-item:hover {
            color: #1e293b;
            background: rgba(102, 126, 234, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }
        
        .nav-item.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        
        .nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            border-radius: 16px;
            opacity: 0.1;
            z-index: -1;
        }
        
        .nav-icon {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Back Button Styling */
        .back-btn {
            background: rgba(34, 197, 94, 0.1) !important;
            color: #16a34a !important;
            border: 1px solid rgba(34, 197, 94, 0.2);
            margin-right: 12px;
        }
        
        .back-btn:hover {
            background: rgba(34, 197, 94, 0.15) !important;
            color: #15803d !important;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        /* User Controls */
        .user-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto; /* Push user controls to the right */
        }
        
        .user-info-dropdown {
            position: relative;
        }
        
        .user-info-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #64748b;
        }
        
        .user-info-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(102, 126, 234, 0.3);
            transform: translateY(-1px);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }
        
        .user-name-text {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }
        
        .user-role-text {
            font-size: 12px;
            color: #64748b;
            text-transform: capitalize;
        }
        
        .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.3s ease;
        }
        
        .user-info-btn.active .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        /* User Dropdown */
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.08);
            color: #1e293b;
        }
        
        .dropdown-item i {
            width: 16px;
            font-size: 14px;
            opacity: 0.7;
        }
        
        .dropdown-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.08);
            margin: 8px 0;
        }
        
        .logout-item {
            color: #dc2626 !important;
        }
        
        .logout-item:hover {
            background: rgba(220, 38, 38, 0.08) !important;
            color: #b91c1c !important;
        }
        
        /* Professional Glass Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border-color: rgba(102, 126, 234, 0.2);
        }
        
        /* Modern Buttons */
        .btn-modern {
            background: var(--gradient-primary);
            border: none;
            border-radius: 16px;
            padding: 14px 28px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        /* Content Spacing */
        .main-content {
            margin-top: 30px;
            padding: 1rem;
        }
        
        /* Modern Animations */
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
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .animate-slide-in {
            animation: slideInRight 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Loading Spinner */
        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Legacy Support */
        .gradient-bg {
            background: var(--gradient-primary);
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 16px;
            padding: 14px 28px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .icon-accent {
            color: var(--accent-500);
        }
        
        .text-primary {
            color: var(--neutral-800) !important;
        }
        
        .text-secondary {
            color: var(--neutral-600) !important;
        }
        
        /* Fix text visibility on white background */
        .glass-card .text-primary {
            color: #1e293b !important;
        }
        
        .glass-card .text-secondary {
            color: #64748b !important;
        }
        
        .glass-card h3 {
            color: #1e293b !important;
        }
        
        .glass-card p {
            color: #374151 !important;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #1e293b;
        }
        
        /* Enhanced Mobile Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                padding: 0 1rem;
                flex-wrap: nowrap;
                position: relative;
                min-height: 70px;
            }
            
            .mobile-menu-toggle {
                display: block;
                order: 2;
                margin-left: auto;
                margin-right: 0;
                z-index: 1001;
                position: absolute;
                right: 2rem;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-menu-toggle:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-1px);
            }
            
            .logo-section {
                order: 1;
                flex: 1;
            }
            
            .user-controls {
                order: 3;
                z-index: 1002;
                position: relative;
            }
            
            .nav-menu {
                display: none;
                width: 100%;
                order: 4;
                flex-direction: column;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border-radius: 16px;
                margin-top: 1rem;
                padding: 1.5rem;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
                gap: 12px;
                animation: slideDown 0.3s ease-out;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                z-index: 1000;
                max-height: calc(100vh - 70px);
                overflow-y: auto;
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .nav-menu.show {
                display: flex;
            }
            
            .nav-item {
                padding: 16px 20px;
                font-size: 16px;
                width: 100%;
                text-align: left;
                border-radius: 12px;
                transition: all 0.3s ease;
                min-height: 44px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 500;
            }
            
            .nav-item:hover {
                background: rgba(102, 126, 234, 0.1);
                transform: translateX(8px);
            }
            
            .nav-item.active {
                background: var(--gradient-primary);
                color: white;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            }
            
            .nav-item span {
                display: inline;
                font-size: 16px;
            }
            
            .nav-icon {
                font-size: 18px;
                width: 20px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            .logo-text {
                font-size: 20px;
                font-weight: 800;
            }
            
            .main-content {
                padding: 0.5rem;
                margin-top: 5px;
            }
            
            /* Enhanced User Controls for Mobile */
            .user-details {
                display: none;
            }
            
            .user-info-btn {
                padding: 10px;
                min-height: 44px;
                min-width: 44px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .user-info-btn:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }
            
            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            .user-dropdown {
                right: 0;
                min-width: 220px;
                top: calc(100% + 12px);
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(0, 0, 0, 0.08);
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                padding: 8px;
                animation: slideDown 0.3s ease-out;
                position: fixed;
            }
            
            .dropdown-item {
                padding: 14px 16px;
                font-size: 15px;
                border-radius: 12px;
                min-height: 44px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .dropdown-item:hover {
                background: rgba(102, 126, 234, 0.08);
                transform: translateX(4px);
            }
            
            .dropdown-item i {
                width: 18px;
                font-size: 16px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            /* Mobile Menu Overlay */
            .nav-menu.show::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: -1;
                animation: fadeIn 0.3s ease-out;
                backdrop-filter: blur(2px);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            /* Enhanced mobile menu animations */
            .nav-menu {
                transform: translateY(-20px);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .nav-menu.show {
                transform: translateY(0);
                opacity: 1;
            }
            
            /* Staggered animation for menu items */
            .nav-menu.show .nav-item {
                animation: slideInLeft 0.3s ease-out forwards;
                opacity: 0;
            }
            
            .nav-menu.show .nav-item:nth-child(1) { animation-delay: 0.1s; }
            .nav-menu.show .nav-item:nth-child(2) { animation-delay: 0.15s; }
            .nav-menu.show .nav-item:nth-child(3) { animation-delay: 0.2s; }
            .nav-menu.show .nav-item:nth-child(4) { animation-delay: 0.25s; }
            .nav-menu.show .nav-item:nth-child(5) { animation-delay: 0.3s; }
            .nav-menu.show .nav-item:nth-child(6) { animation-delay: 0.35s; }
            
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            /* Mobile toggle button animation */
            .mobile-menu-toggle i {
                transition: transform 0.3s ease;
            }
            
            .mobile-menu-toggle:active {
                transform: scale(0.95);
            }
            
            /* User dropdown animation enhancement */
            .user-dropdown {
                transform: translateY(-10px) scale(0.95);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .user-dropdown.show {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            
            /* Active states for touch devices */
            .nav-item:active {
                transform: translateX(12px) scale(0.98);
                background: rgba(102, 126, 234, 0.15);
            }
            
            .dropdown-item:active {
                transform: translateX(6px) scale(0.98);
                background: rgba(102, 126, 234, 0.12);
            }
            
            /* Focus states for accessibility */
            .mobile-menu-toggle:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }
            
            .nav-item:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
                background: rgba(102, 126, 234, 0.1);
            }
            
            .user-info-btn:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }
            
            .dropdown-item:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
                background: rgba(102, 126, 234, 0.08);
            }
        }
        
        @media (max-width: 480px) {
            .nav-container {
                padding: 0.75rem;
                min-height: 60px;
            }
            
            .logo-text {
                font-size: 16px;
                font-weight: 700;
            }
            
            .mobile-menu-toggle {
                font-size: 1.25rem;
                padding: 8px;
                width: 40px;
                height: 40px;
                margin-right: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-menu-toggle i {
                line-height: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .nav-menu {
                padding: 1rem;
                margin-top: 0.75rem;
                border-radius: 12px;
            }
            
            .nav-item {
                padding: 14px 16px;
                font-size: 15px;
                min-height: 44px;
                border-radius: 10px;
            }
            
            .nav-item:hover {
                transform: translateX(6px);
            }
            
            .nav-icon {
                font-size: 16px;
                width: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            .user-info-btn {
                padding: 8px;
                min-height: 40px;
                min-width: 40px;
                border-radius: 10px;
            }
            
            .user-avatar {
                width: 24px;
                height: 24px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            .user-dropdown {
                right: 0;
                min-width: 200px;
                top: calc(100% + 8px);
                border-radius: 12px;
                padding: 6px;
            }
            
            .dropdown-item {
                padding: 12px 14px;
                font-size: 14px;
                min-height: 44px;
                border-radius: 10px;
            }
            
            .dropdown-item:hover {
                transform: translateX(3px);
            }
            
            .dropdown-item i {
                width: 16px;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            
            .main-content {
                padding: 0.25rem;
                margin-top: 2px;
            }
            
            /* Ensure proper spacing on very small screens */
            .logo-section {
                min-width: 0;
                flex: 1;
            }
            
            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
        }
        
        /* Utility Classes */
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--glass-border);
        }
        
        .shadow-glass {
            box-shadow: var(--glass-shadow);
        }
    </style>
    <?php if (isset($additional_styles)) echo $additional_styles; ?>
</head>
<body>
    <!-- Modern Glass Navigation -->
    <nav class="modern-nav">
        <div class="nav-container">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
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
                <span class="logo-text">TrackerBI</span>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navigation Menu -->
            <div class="nav-menu">
                <?php if ($is_logged_in && $current_page != 'product-dashboard.php'): ?>
                <!-- Back to Home Button -->
                <a href="product-dashboard.php" class="nav-item back-btn">
                    <i class="fas fa-arrow-left nav-icon"></i>
                    <span>Backto Home</span>
                </a>
                <?php endif; ?>
                
                <a href="trackerbi-audio.php" class="nav-item <?php echo ($current_page == 'trackerbi-audio.php') ? 'active' : ''; ?>">
                    <i class="fas fa-microphone nav-icon"></i>
                    <span>Audio Analysis</span>
                </a>
                <a href="call-dashboard.php" class="nav-item <?php echo ($current_page == 'call-dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-phone nav-icon"></i>
                    <span>Call Dashboard</span>
                </a>
                <a href="enhanced-call-dashboard.php" class="nav-item <?php echo ($current_page == 'enhanced-call-dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span>Enhanced CallDashboard</span>
                </a>
                <a href="today-analytics.php" class="nav-item <?php echo ($current_page == 'today-analytics.php') ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-day nav-icon"></i>
                    <span>Today's Analytics</span>
                </a>
                <a href="analytics-dashboard.php" class="nav-item <?php echo ($current_page == 'analytics-dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span>Analytics Dashboard</span>
                </a>
                <a href="meta-dashboard.php" class="nav-item <?php echo ($current_page == 'meta-dashboard.php') ? 'active' : ''; ?>">
                    <i class="fab fa-meta nav-icon"></i>
                    <span>Meta Dashboard</span>
                </a>
            </div>
            
            <!-- User Controls -->
            <?php if ($is_logged_in): ?>
            <div class="user-controls">
                <div class="user-info-dropdown">
                    <button class="user-info-btn" onclick="toggleUserDropdown()">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <span class="user-name-text"><?php echo htmlspecialchars($user_name); ?></span>
                            <span class="user-role-text"><?php echo htmlspecialchars($user_role); ?></span>
                        </div>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <a href="product-dashboard.php" class="dropdown-item">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                        <a href="profile.php" class="dropdown-item">
                            <i class="fas fa-user-cog"></i>
                            <span>Profile Settings</span>
                        </a>
                        <?php if ($user_role === 'admin'): ?>
                        <a href="admin-panel.php" class="dropdown-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Admin Panel</span>
                        </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Main Content Area -->
    <div class="main-content">
    
    <!-- JavaScript for User Dropdown -->
    <script>
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('.user-info-btn');
            
            dropdown.classList.toggle('show');
            button.classList.toggle('active');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('.user-info-btn');
            
            if (dropdown && !event.target.closest('.user-info-dropdown')) {
                dropdown.classList.remove('show');
                if (button) button.classList.remove('active');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('userDropdown');
                const button = document.querySelector('.user-info-btn');
                
                if (dropdown) dropdown.classList.remove('show');
                if (button) button.classList.remove('active');
                
                // Also close mobile menu
                const mobileMenu = document.querySelector('.nav-menu');
                const mobileToggle = document.querySelector('.mobile-menu-toggle');
                if (mobileMenu) mobileMenu.classList.remove('show');
                if (mobileToggle) mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
        
        // Enhanced Mobile Menu Toggle Function
        function toggleMobileMenu() {
            const menu = document.querySelector('.nav-menu');
            const toggle = document.querySelector('.mobile-menu-toggle');
            const body = document.body;
            
            if (!menu || !toggle) return;
            
            const isOpen = menu.classList.contains('show');
            
            if (isOpen) {
                // Close menu
                menu.classList.remove('show');
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
                body.style.overflow = ''; // Re-enable scrolling
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open navigation menu');
            } else {
                // Open menu
                menu.classList.add('show');
                toggle.innerHTML = '<i class="fas fa-times"></i>';
                body.style.overflow = 'hidden'; // Prevent background scrolling
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Close navigation menu');
                
                // Focus first menu item for accessibility
                const firstMenuItem = menu.querySelector('.nav-item');
                if (firstMenuItem) {
                    setTimeout(() => firstMenuItem.focus(), 100);
                }
            }
        }
        
        // Enhanced User Dropdown Toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('.user-info-btn');
            
            if (!dropdown || !button) return;
            
            const isOpen = dropdown.classList.contains('show');
            
            // Close mobile menu if open
            const mobileMenu = document.querySelector('.nav-menu');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            if (mobileMenu && mobileMenu.classList.contains('show')) {
                mobileMenu.classList.remove('show');
                if (mobileToggle) mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
                document.body.style.overflow = '';
            }
            
            if (isOpen) {
                dropdown.classList.remove('show');
                button.classList.remove('active');
                button.setAttribute('aria-expanded', 'false');
            } else {
                dropdown.classList.add('show');
                button.classList.add('active');
                button.setAttribute('aria-expanded', 'true');
                
                // Focus first dropdown item
                const firstItem = dropdown.querySelector('.dropdown-item');
                if (firstItem) {
                    setTimeout(() => firstItem.focus(), 100);
                }
            }
        }
        
        // Close mobile menu when clicking outside or on menu items
        document.addEventListener('click', function(event) {
            const menu = document.querySelector('.nav-menu');
            const toggle = document.querySelector('.mobile-menu-toggle');
            const dropdown = document.getElementById('userDropdown');
            const userButton = document.querySelector('.user-info-btn');
            
            // Close mobile menu
            if (menu && menu.classList.contains('show')) {
                if (!event.target.closest('.nav-menu') && 
                    !event.target.closest('.mobile-menu-toggle')) {
                    menu.classList.remove('show');
                    if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
                    document.body.style.overflow = '';
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                        toggle.setAttribute('aria-label', 'Open navigation menu');
                    }
                }
                
                // Close menu when clicking on nav items (for mobile)
                if (event.target.closest('.nav-item') && window.innerWidth <= 768) {
                    setTimeout(() => {
                        menu.classList.remove('show');
                        if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
                        document.body.style.overflow = '';
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                            toggle.setAttribute('aria-label', 'Open navigation menu');
                        }
                    }, 150); // Small delay for better UX
                }
            }
            
            // Close user dropdown
            if (dropdown && dropdown.classList.contains('show') && 
                !event.target.closest('.user-info-dropdown')) {
                dropdown.classList.remove('show');
                if (userButton) {
                    userButton.classList.remove('active');
                    userButton.setAttribute('aria-expanded', 'false');
                }
            }
        });
        
        // Enhanced touch support for mobile
        let touchStartY = 0;
        let touchStartX = 0;
        
        document.addEventListener('touchstart', function(event) {
            touchStartY = event.touches[0].clientY;
            touchStartX = event.touches[0].clientX;
        });
        
        document.addEventListener('touchmove', function(event) {
            const menu = document.querySelector('.nav-menu');
            if (menu && menu.classList.contains('show')) {
                const touchY = event.touches[0].clientY;
                const touchX = event.touches[0].clientX;
                const deltaY = touchY - touchStartY;
                const deltaX = touchX - touchStartX;
                
                // Close menu on swipe up (significant upward swipe)
                if (deltaY < -100 && Math.abs(deltaX) < 50) {
                    event.preventDefault();
                    const toggle = document.querySelector('.mobile-menu-toggle');
                    menu.classList.remove('show');
                    if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
                    document.body.style.overflow = '';
                }
            }
        });
        
        // Close menus when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const menu = document.querySelector('.nav-menu');
                const toggle = document.querySelector('.mobile-menu-toggle');
                const dropdown = document.getElementById('userDropdown');
                const userButton = document.querySelector('.user-info-btn');
                
                if (menu) menu.classList.remove('show');
                if (toggle) {
                    toggle.innerHTML = '<i class="fas fa-bars"></i>';
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.setAttribute('aria-label', 'Open navigation menu');
                }
                if (dropdown) dropdown.classList.remove('show');
                if (userButton) {
                    userButton.classList.remove('active');
                    userButton.setAttribute('aria-expanded', 'false');
                }
                document.body.style.overflow = '';
            }
        });
        
        // Keyboard navigation support
        document.addEventListener('keydown', function(event) {
            const menu = document.querySelector('.nav-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (event.key === 'Escape') {
                // Close mobile menu
                if (menu && menu.classList.contains('show')) {
                    const toggle = document.querySelector('.mobile-menu-toggle');
                    menu.classList.remove('show');
                    if (toggle) {
                        toggle.innerHTML = '<i class="fas fa-bars"></i>';
                        toggle.focus();
                        toggle.setAttribute('aria-expanded', 'false');
                        toggle.setAttribute('aria-label', 'Open navigation menu');
                    }
                    document.body.style.overflow = '';
                }
                
                // Close user dropdown
                if (dropdown && dropdown.classList.contains('show')) {
                    const button = document.querySelector('.user-info-btn');
                    dropdown.classList.remove('show');
                    if (button) {
                        button.classList.remove('active');
                        button.focus();
                        button.setAttribute('aria-expanded', 'false');
                    }
                }
            }
        });
        
        // Initialize ARIA attributes
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const userButton = document.querySelector('.user-info-btn');
            
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open navigation menu');
                toggle.setAttribute('aria-controls', 'nav-menu');
            }
            
            if (userButton) {
                userButton.setAttribute('aria-expanded', 'false');
                userButton.setAttribute('aria-label', 'Open user menu');
                userButton.setAttribute('aria-controls', 'userDropdown');
            }
        });
    </script>
