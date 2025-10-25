<?php
require_once 'config.php';

/**
 * Demo page with sample audio files and usage examples
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - TrackerBI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link { transition: all 0.3s ease; }
        .nav-link:hover { transform: translateY(-2px); }
        .nav-link.active { background-color: rgba(255,255,255,0.2); }
        .demo-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .demo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .step-number {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .code-block {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        
        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            .container { padding-left: 1rem !important; padding-right: 1rem !important; }
            .flex.space-x-6 { flex-direction: column; space-x: 0; gap: 0.5rem; }
            .nav-link { padding: 0.5rem 1rem; text-align: center; }
            h1 { font-size: 1.5rem !important; }
            .text-4xl { font-size: 1.5rem !important; }
            .py-6 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
            .flex-wrap { flex-direction: column; }
            .gap-4 { gap: 0.5rem !important; }
        }
        
        @media (max-width: 480px) {
            .container { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
            h1 { font-size: 1.25rem !important; }
            .px-4 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
            .py-2 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-gray-800 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <i class="fas fa-microphone text-blue-400 text-2xl mr-3"></i>
                    <span class="text-white text-xl font-bold">TrackerBI</span>
                </div>
                
                <!-- Navigation Menu -->
                <div class="flex space-x-6">
                    <a href="index.php" class="nav-link text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-microphone mr-2"></i>
                        Audio Analysis
                    </a>
                    <a href="external-dashboard.php" class="nav-link text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Dashboard
                    </a>
                    <a href="dashboard.php" class="nav-link text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Analytics
                    </a>
                    <a href="meta-dashboard.php" class="nav-link text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-analytics mr-2"></i>
                        Meta Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="gradient-bg py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-white text-center">
                <i class="fas fa-play-circle mr-3"></i>
                Demo & Documentation
            </h1>
            <p class="text-white text-center mt-2 opacity-90">
                API examples, usage guides, and system documentation
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">

            <!-- Navigation -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-8">
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-upload mr-2"></i>Upload Audio
                    </a>
                    <a href="api.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-code mr-2"></i>API Endpoint
                    </a>
                    <a href="test.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-cog mr-2"></i>System Test
                    </a>
                </div>
            </div>

            <!-- System Overview -->
            <div class="demo-card bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    System Overview
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">Supported Features</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Audio transcription with timestamps</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Translation to English</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Speaker sentiment analysis</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Overall sentiment scoring</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Agent performance insights</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Comprehensive error handling</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">Supported Formats</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>MP3 (audio/mpeg, audio/mp3)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>WAV (audio/wav, audio/x-wav, audio/wave)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>M4A (audio/m4a, audio/x-m4a, audio/mp4)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>AAC (audio/aac)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>OGG (audio/ogg, audio/x-ogg)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>WebM (audio/webm)</li>
                            <li><i class="fas fa-file-audio text-blue-500 mr-2"></i>FLAC (audio/flac, audio/x-flac)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- API Usage Examples -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-code mr-2 text-green-500"></i>
                    API Usage Examples
                </h2>
                
                <div class="space-y-6">
                    <!-- cURL Example -->
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">cURL Command</h3>
                        <pre class="code-block text-green-400 p-4 rounded-lg text-sm overflow-x-auto"><code>curl -X POST \
  -F "audio_file=@your-audio.mp3" \
  <?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>/api.php</code></pre>
                    </div>

                    <!-- JavaScript Example -->
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">JavaScript (Fetch API)</h3>
                        <pre class="code-block text-green-400 p-4 rounded-lg text-sm overflow-x-auto"><code>const formData = new FormData();
formData.append('audio_file', audioFile);

fetch('<?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>/api.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    console.log('Analysis result:', data);
})
.catch(error => {
    console.error('Error:', error);
});</code></pre>
                    </div>

                    <!-- Python Example -->
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">Python (requests)</h3>
                        <pre class="code-block text-green-400 p-4 rounded-lg text-sm overflow-x-auto"><code>import requests

url = '<?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>/api.php'
files = {'audio_file': open('your-audio.mp3', 'rb')}

response = requests.post(url, files=files)
result = response.json()

print('Analysis result:', result)</code></pre>
                    </div>
                </div>
            </div>

            <!-- Sample Response -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-file-code mr-2 text-purple-500"></i>
                    Sample API Response
                </h2>
                <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>{
  "success": true,
  "timestamp": "2024-10-12T14:19:00+00:00",
  "data": {
    "upload_info": {
      "filename": "audio_67890.mp3",
      "size_bytes": 1048576,
      "mime_type": "audio/mpeg"
    },
    "transcription": "[00:00:05] Speaker 1: Hello, how can I help you today?\n[00:00:08] Speaker 2: Hi, I'm having trouble with my account.",
    "translation": "[00:00:05] Speaker 1: Hello, how can I help you today?\n[00:00:08] Speaker 2: Hi, I'm having trouble with my account.",
    "sentiment_analysis": {
      "speaker_analysis": [
        {
          "speaker": "Speaker 1",
          "sentiment": "positive",
          "confidence": 0.85,
          "reasoning": "Friendly greeting with helpful tone",
          "key_emotions": ["welcoming", "professional"],
          "tone_indicators": ["polite", "engaging"]
        }
      ],
      "overall_sentiment": {
        "primary_sentiment": "neutral",
        "emotional_tone": "Professional customer service interaction",
        "empathy_level": "medium",
        "politeness_level": "high"
      },
      "sentiment_score": {
        "numerical_score": 75,
        "scale": "0-100 (0=very negative, 50=neutral, 100=very positive)",
        "confidence": 0.88
      },
      "agent_performance": {
        "clarity_score": 85,
        "empathy_score": 78,
        "professionalism_score": 92,
        "overall_performance": "good",
        "strengths": ["Clear communication", "Professional demeanor"],
        "areas_for_improvement": ["Could show more empathy"],
        "recommendations": ["Use more empathetic language"]
      }
    }
  }
}</code></pre>
            </div>

            <!-- Configuration Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-cogs mr-2 text-orange-500"></i>
                    Current Configuration
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">File Limits</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>Max File Size:</strong> <?php echo number_format(MAX_FILE_SIZE / 1024 / 1024, 2); ?> MB</li>
                            <li><strong>Upload Directory:</strong> <?php echo is_writable(UPLOAD_DIR) ? '✅ Writable' : '❌ Not Writable'; ?></li>
                            <li><strong>Log Directory:</strong> <?php echo is_writable(dirname(LOG_FILE)) ? '✅ Writable' : '❌ Not Writable'; ?></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2 text-gray-700">API Configuration</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>API Keys:</strong> <?php echo count(GEMINI_API_KEYS); ?> configured</li>
                            <li><strong>Error Handling:</strong> ✅ Active</li>
                            <li><strong>Logging:</strong> ✅ Enabled</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Start Guide -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">
                    <i class="fas fa-rocket mr-2 text-red-500"></i>
                    Quick Start Guide
                </h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="step-number text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">1</div>
                        <div>
                            <h3 class="font-semibold text-gray-700">Prepare Your Audio</h3>
                            <p class="text-sm text-gray-600">Ensure your audio file is in a supported format (MP3, WAV, M4A, OGG, WebM) and under 50MB.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="step-number text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">2</div>
                        <div>
                            <h3 class="font-semibold text-gray-700">Upload via Web Interface</h3>
                            <p class="text-sm text-gray-600">Go to <a href="index.php" class="text-blue-500 hover:underline">index.php</a> and use the upload form for a user-friendly experience.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="step-number text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">3</div>
                        <div>
                            <h3 class="font-semibold text-gray-700">Or Use the API</h3>
                            <p class="text-sm text-gray-600">Send POST requests to <code class="bg-gray-100 px-2 py-1 rounded">api.php</code> for programmatic access.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="step-number text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">4</div>
                        <div>
                            <h3 class="font-semibold text-gray-700">Review Results</h3>
                            <p class="text-sm text-gray-600">Get comprehensive analysis including transcription, translation, sentiment analysis, and performance insights.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <i class="fas fa-play-circle text-blue-400 text-xl mr-3"></i>
                    <span class="text-lg font-semibold">TrackerBI</span>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-300">&copy; 2025 TrackerBI</p>
                    <p class="text-sm text-gray-400 mt-1">Demo & Documentation</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-6 pt-6 text-center">
                <div class="flex justify-center space-x-6 text-sm text-gray-400">
                    <span><i class="fas fa-book mr-1"></i>Documentation</span>
                    <span><i class="fas fa-code mr-1"></i>API Examples</span>
                    <span><i class="fas fa-rocket mr-1"></i>Quick Start</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
