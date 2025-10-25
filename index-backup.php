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
                
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <div class="upload-area p-8 text-center">
                        <div class="mb-6">
                            <i class="fas fa-cloud-upload-alt text-6xl icon-accent mb-4"></i>
                            <h3 class="text-xl font-semibold text-primary mb-2">Drop your audio file here</h3>
                            <p class="text-secondary">or click to browse</p>
                        </div>
                        
                        <input type="file" 
                               id="audio_file" 
                               name="audio_file" 
                               accept="audio/*" 
                               required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition file:duration-300">
                        
                        <div class="mt-6 flex flex-wrap justify-center gap-2 text-xs text-secondary">
                            <span class="px-3 py-1 bg-gray-100 rounded-full">MP3</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">WAV</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">M4A</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">AAC</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">OGG</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">WebM</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">FLAC</span>
                        </div>
                        <p class="text-xs text-secondary mt-3">Maximum file size: 50MB</p>
                    </div>
                    
                    <button type="submit" 
                            class="btn-primary w-full text-white font-semibold py-4 px-8 rounded-xl text-lg"
                            <?php echo $processing ? 'disabled' : ''; ?>>
                        <?php if ($processing): ?>
                            <div class="flex items-center justify-center">
                                <div class="loading-spinner mr-3"></div>
                                Processing Audio...
                            </div>
                        <?php else: ?>
                            <i class="fas fa-magic mr-3"></i>
                            Analyze Audio
                        <?php endif; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Always Show Debug Info -->
        <!-- <div class="max-w-7xl mx-auto mb-4">
            <div class="glass-card rounded-2xl p-4 bg-yellow-50 border border-yellow-200">
                <h3 class="text-yellow-800 font-bold mb-2">🔍 Debug Information</h3>
                <p class="text-yellow-700 text-sm">Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
                <p class="text-yellow-700 text-sm">Form Submitted: <?php echo $debug_info['form_submitted'] ? 'YES' : 'NO'; ?></p>
                
                <?php if ($debug_info['form_submitted']): ?>
                    <p class="text-yellow-700 text-sm">Files Exist: <?php echo $debug_info['files_exist'] ? 'YES' : 'NO'; ?></p>
                    
                    <?php if (isset($debug_info['file_info'])): ?>
                        <p class="text-yellow-700 text-sm">File Name: <?php echo $debug_info['file_info']['name']; ?></p>
                        <p class="text-yellow-700 text-sm">File Size: <?php echo number_format($debug_info['file_info']['size']); ?> bytes</p>
                        <p class="text-yellow-700 text-sm">File Error Code: <?php echo $debug_info['file_info']['error']; ?></p>
                        <p class="text-yellow-700 text-sm">Results Generated: <?php echo isset($debug_info['results_generated']) ? 'YES' : 'NO'; ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($debug_info['exception'])): ?>
                        <p class="text-red-600 text-sm font-bold">Exception: <?php echo $debug_info['exception']; ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($debug_info['no_file'])): ?>
                        <p class="text-red-600 text-sm">No file was uploaded with the form!</p>
                    <?php endif; ?>
                <?php endif; ?>
                
                <p class="text-yellow-700 text-sm">Results Available: <?php echo $results ? 'YES' : 'NO'; ?></p>
                
                <?php if ($results): ?>
                    <p class="text-green-600 text-sm">✅ Results object exists</p>
                    <?php if (isset($results['errors']) && count($results['errors']) > 0): ?>
                        <p class="text-red-600 text-sm">❌ Errors: <?php echo implode(', ', $results['errors']); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Test Form Submission -->
                <!-- <div class="mt-4 pt-4 border-t border-yellow-300">
                    <p class="text-yellow-800 text-sm font-bold mb-2">🧪 Test Form Submission:</p>
                    <form method="POST" class="inline">
                        <input type="hidden" name="test_submit" value="1">
                        <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                            Test Submit (No File)
                        </button>
                    </form>
                    <?php if (isset($_POST['test_submit'])): ?>
                        <span class="text-green-600 text-sm ml-2">✅ Test form works!</span>
                    <?php endif; ?>
                </div> -->
            </div>
        </div>

        <!-- Results Section -->
        <?php if ($results): ?>
        <div class="max-w-7xl mx-auto result-card space-y-8">
            <!-- Error Messages -->
            <?php if (!empty($results['errors'])): ?>
            <div class="glass-card rounded-2xl p-6 border-l-4 border-red-400">
                <h3 class="font-semibold mb-4 text-red-700 text-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Processing Errors
                </h3>
                <ul class="space-y-2">
                    <?php foreach ($results['errors'] as $error): ?>
                    <li class="text-red-600 flex items-start">
                        <i class="fas fa-times-circle mr-2 mt-1 text-sm"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Upload Status -->
            <?php if ($results['upload']): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-file-audio mr-2 icon-accent"></i>
                    Upload Status
                </h3>
                <?php if ($results['upload']['success']): ?>
                    <div class="flex items-center p-4 bg-green-50 rounded-xl border border-green-200">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-700 font-medium">File uploaded successfully</p>
                            <p class="text-green-600 text-sm">
                                <?php echo htmlspecialchars($results['upload']['filename']); ?>
                                (<?php echo number_format($results['upload']['size'] / 1024, 2); ?> KB)
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center p-4 bg-red-50 rounded-xl border border-red-200">
                        <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-700 font-medium">Upload failed</p>
                            <p class="text-red-600 text-sm"><?php echo htmlspecialchars($results['upload']['error']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Transcription Results -->
            <?php if ($results['transcription'] && $results['transcription']['success']): ?>
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">
                    <i class="fas fa-file-alt mr-3 icon-accent"></i>
                    Original Transcription
                </h3>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border">
                    <pre class="whitespace-pre-wrap text-sm text-primary leading-relaxed font-mono"><?php echo htmlspecialchars($results['transcription']['transcription']); ?></pre>
                </div>
            </div>
            <?php endif; ?>

            <!-- Translation Results -->
            <?php if ($results['translation'] && $results['translation']['success']): ?>
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">
                    <i class="fas fa-language mr-3 icon-accent"></i>
                    English Translation
                </h3>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-100">
                    <pre class="whitespace-pre-wrap text-sm text-primary leading-relaxed font-mono"><?php echo htmlspecialchars($results['translation']['translation']); ?></pre>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sentiment Analysis Results -->
            <?php if ($results['sentiment_analysis'] && $results['sentiment_analysis']['success']): ?>
            <?php $analysis = $results['sentiment_analysis']['analysis']; ?>
            
            <!-- Overall Sentiment Score -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary text-center">
                    <i class="fas fa-chart-line mr-3 icon-accent"></i>
                    Sentiment Analysis Score
                </h3>
                <div class="text-center">
                    <div class="relative inline-block mb-6">
                        <div class="w-32 h-32 rounded-full border-8 border-gray-200 flex items-center justify-center mx-auto
                            <?php 
                            $score = $analysis['sentiment_score']['numerical_score'];
                            if ($score >= 70) echo 'border-green-400 bg-green-50';
                            elseif ($score >= 40) echo 'border-yellow-400 bg-yellow-50';
                            else echo 'border-red-400 bg-red-50';
                            ?>">
                            <div class="text-center">
                                <div class="text-3xl font-bold 
                                    <?php 
                                    if ($score >= 70) echo 'text-green-600';
                                    elseif ($score >= 40) echo 'text-yellow-600';
                                    else echo 'text-red-600';
                                    ?>">
                                    <?php echo $score; ?>
                                </div>
                                <div class="text-xs text-secondary">out of 100</div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-secondary">
                            Scale: <?php echo htmlspecialchars($analysis['sentiment_score']['scale']); ?>
                        </p>
                        <p class="text-sm text-secondary">
                            Confidence: <?php echo ($analysis['sentiment_score']['confidence'] * 100); ?>%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Speaker Analysis -->
            <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">
                    Speaker Sentiment Breakdown
                </h3>
                <div class="space-y-4">
                    <?php foreach ($analysis['speaker_analysis'] as $speaker): ?>
                    <div class="border rounded-lg p-4 sentiment-<?php echo $speaker['sentiment']; ?>">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold"><?php echo htmlspecialchars($speaker['speaker']); ?></h4>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-50">
                                <?php echo ucfirst($speaker['sentiment']); ?> (<?php echo ($speaker['confidence'] * 100); ?>%)
                            </span>
                        </div>
                        <p class="text-sm mb-2"><?php echo htmlspecialchars($speaker['reasoning']); ?></p>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="text-xs font-medium">Emotions:</span>
                            <?php foreach ($speaker['key_emotions'] as $emotion): ?>
                            <span class="px-2 py-1 bg-white bg-opacity-50 rounded text-xs"><?php echo htmlspecialchars($emotion); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs font-medium">Tone:</span>
                            <?php foreach ($speaker['tone_indicators'] as $indicator): ?>
                            <span class="px-2 py-1 bg-white bg-opacity-50 rounded text-xs"><?php echo htmlspecialchars($indicator); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Overall Sentiment Summary -->
            <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">
                    Overall Sentiment Summary
                </h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold mb-2">Primary Sentiment</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium sentiment-<?php echo $analysis['overall_sentiment']['primary_sentiment']; ?>">
                            <?php echo ucfirst($analysis['overall_sentiment']['primary_sentiment']); ?>
                        </span>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Emotional Tone</h4>
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($analysis['overall_sentiment']['emotional_tone']); ?></p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Empathy Level</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?php echo ucfirst($analysis['overall_sentiment']['empathy_level']); ?>
                        </span>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Politeness Level</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <?php echo ucfirst($analysis['overall_sentiment']['politeness_level']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 grid md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold mb-2 text-red-600">Frustration Indicators</h4>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            <?php foreach ($analysis['overall_sentiment']['frustration_indicators'] as $indicator): ?>
                            <li><?php echo htmlspecialchars($indicator); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2 text-green-600">Positive Indicators</h4>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            <?php foreach ($analysis['overall_sentiment']['positive_indicators'] as $indicator): ?>
                            <li><?php echo htmlspecialchars($indicator); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Agent Performance Insights -->
            <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">
                    Agent Performance Insights
                </h3>
                
                <!-- Performance Scores -->
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 mb-1">
                            <?php echo $analysis['agent_performance']['clarity_score']; ?>/100
                        </div>
                        <div class="text-sm text-gray-600">Clarity</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 mb-1">
                            <?php echo $analysis['agent_performance']['empathy_score']; ?>/100
                        </div>
                        <div class="text-sm text-gray-600">Empathy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 mb-1">
                            <?php echo $analysis['agent_performance']['professionalism_score']; ?>/100
                        </div>
                        <div class="text-sm text-gray-600">Professionalism</div>
                    </div>
                </div>

                <!-- Call Structure Scores -->
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="text-center bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-700 mb-1">
                            <?php echo isset($analysis['agent_performance']['call_opening_score']) ? $analysis['agent_performance']['call_opening_score'] : 'N/A'; ?>/100
                        </div>
                        <div class="text-sm text-blue-600 font-medium">Call Opening</div>
                        <div class="text-xs text-gray-600 mt-1">Greeting & Introduction</div>
                    </div>
                    <div class="text-center bg-orange-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-orange-700 mb-1">
                            <?php echo isset($analysis['agent_performance']['call_quality_score']) ? $analysis['agent_performance']['call_quality_score'] : 'N/A'; ?>/100
                        </div>
                        <div class="text-sm text-orange-600 font-medium">Call Quality</div>
                        <div class="text-xs text-gray-600 mt-1">Communication & Problem Solving</div>
                    </div>
                    <div class="text-center bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-700 mb-1">
                            <?php echo isset($analysis['agent_performance']['call_closing_score']) ? $analysis['agent_performance']['call_closing_score'] : 'N/A'; ?>/100
                        </div>
                        <div class="text-sm text-green-600 font-medium">Call Closing</div>
                        <div class="text-xs text-gray-600 mt-1">Resolution & Follow-up</div>
                    </div>
                </div>

                <!-- Call Structure Analysis -->
                <?php if (isset($analysis['agent_performance']['call_structure_analysis'])): ?>
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold mb-3 text-gray-800">Call Structure Analysis</h4>
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-blue-600">Opening Assessment:</span>
                            <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($analysis['agent_performance']['call_structure_analysis']['opening_assessment']); ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-orange-600">Quality Assessment:</span>
                            <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($analysis['agent_performance']['call_structure_analysis']['quality_assessment']); ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-green-600">Closing Assessment:</span>
                            <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($analysis['agent_performance']['call_structure_analysis']['closing_assessment']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Overall Performance -->
                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Overall Performance</h4>
                    <span class="px-4 py-2 rounded-full text-sm font-medium 
                        <?php 
                        $performance = $analysis['agent_performance']['overall_performance'];
                        if ($performance === 'excellent') echo 'bg-green-100 text-green-800';
                        elseif ($performance === 'good') echo 'bg-blue-100 text-blue-800';
                        else echo 'bg-yellow-100 text-yellow-800';
                        ?>">
                        <?php echo ucfirst($performance); ?>
                    </span>
                </div>

                <!-- Strengths and Improvements -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold mb-2 text-green-600">Strengths</h4>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <?php foreach ($analysis['agent_performance']['strengths'] as $strength): ?>
                            <li><?php echo htmlspecialchars($strength); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2 text-orange-600">Areas for Improvement</h4>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <?php foreach ($analysis['agent_performance']['areas_for_improvement'] as $area): ?>
                            <li><?php echo htmlspecialchars($area); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="mt-4">
                    <h4 class="font-semibold mb-2 text-indigo-600">Recommendations</h4>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        <?php foreach ($analysis['agent_performance']['recommendations'] as $recommendation): ?>
                        <li><?php echo htmlspecialchars($recommendation); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Conversation Summary -->
            <?php if ($results['conversation_summary'] && $results['conversation_summary']['success']): ?>
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">
                    Overall Conversation Summary
                </h3>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-xl border border-gray-200">
                    <div class="conversation-summary">
                        <?php 
                        $summary = $results['conversation_summary']['summary'];
                        // Split summary into sections for better formatting
                        $sections = explode("\n\n", $summary);
                        foreach ($sections as $section) {
                            $section = trim($section);
                            if (!empty($section)) {
                                // Check if it's a heading (contains colon at end of line)
                                if (preg_match('/^([^:]+):?\s*$/', $section, $matches)) {
                                    echo '<h4 class="text-lg font-semibold text-gray-800 mb-3 mt-6 first:mt-0">' . htmlspecialchars($matches[1]) . '</h4>';
                                } else {
                                    // Regular content
                                    $formatted = nl2br(htmlspecialchars($section));
                                    // Convert numbered lists
                                    $formatted = preg_replace('/^(\d+\.\s)/m', '<span class="font-medium text-blue-600">$1</span>', $formatted);
                                    // Convert bullet points
                                    $formatted = preg_replace('/^(•\s)/m', '<span class="text-blue-500">$1</span>', $formatted);
                                    echo '<div class="text-gray-700 leading-relaxed mb-4">' . $formatted . '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script>
    // Page-specific JavaScript for audio analysis
    document.addEventListener("DOMContentLoaded", function() {
        const fileInput = document.getElementById("audio_file");
        const form = document.querySelector("form");
        
        if (fileInput) {
            fileInput.setAttribute("data-max-size", "50");
        }
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
