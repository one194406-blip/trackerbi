<?php
require_once 'config.php';
require_once 'ErrorHandler.php';
require_once 'AudioAnalyzer.php';

/**
 * Test Script for Audio Analysis System
 */

class SystemTester {
    private $analyzer;
    private $testResults = [];
    
    public function __construct() {
        $this->analyzer = new AudioAnalyzer();
    }
    
    public function runAllTests() {
        echo "<!DOCTYPE html><html><head><title>System Test - TrackerBI</title>";
        echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
        echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>";
        echo "</head><body class='bg-gray-100'>";
        
        // Navigation
        echo "<nav class='bg-gray-800 shadow-lg'>";
        echo "<div class='container mx-auto px-4'>";
        echo "<div class='flex justify-between items-center py-4'>";
        echo "<div class='flex items-center'>";
        echo "<i class='fas fa-microphone text-blue-400 text-2xl mr-3'></i>";
        echo "<span class='text-white text-xl font-bold'>TrackerBI</span>";
        echo "</div>";
        echo "<div class='flex space-x-6'>";
        echo "<a href='index.php' class='text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center'>";
        echo "<i class='fas fa-microphone mr-2'></i>Audio Analysis</a>";
        echo "<a href='external-dashboard.php' class='text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center'>";
        echo "<i class='fas fa-external-link-alt mr-2'></i>Dashboard</a>";
        echo "<a href='dashboard.php' class='text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center'>";
        echo "<i class='fas fa-chart-line mr-2'></i>Analytics</a>";
        echo "<a href='meta-dashboard.php' class='text-gray-300 hover:text-white px-4 py-2 rounded-lg flex items-center'>";
        echo "<i class='fas fa-analytics mr-2'></i>Meta Dashboard</a>";
        echo "</div></div></div></nav>";
        
        // Header
        echo "<div class='bg-gradient-to-r from-blue-600 to-purple-600 py-6'>";
        echo "<div class='container mx-auto px-4'>";
        echo "<h1 class='text-4xl font-bold text-white text-center'>";
        echo "<i class='fas fa-vial mr-3'></i>System Test Suite</h1>";
        echo "<p class='text-white text-center mt-2 opacity-90'>Comprehensive system diagnostics and validation</p>";
        echo "</div></div>";
        
        echo "<div class='container mx-auto px-4 py-8'>";
        echo "<div class='bg-white rounded-lg shadow-lg p-6'>";
        
        echo "<style>
            .test-pass { color: #10B981; font-weight: bold; }
            .test-fail { color: #EF4444; font-weight: bold; }
            .test-section { 
                margin: 20px 0; 
                padding: 20px; 
                border: 1px solid #E5E7EB; 
                border-radius: 12px; 
                background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s ease;
            }
            .test-section:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }
            .test-section h2 {
                color: #1F2937;
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 15px;
                border-bottom: 2px solid #E5E7EB;
                padding-bottom: 8px;
            }
            pre { 
                background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
                color: #E5E7EB;
                padding: 20px; 
                border-radius: 8px; 
                overflow-x: auto; 
                border: 1px solid #374151;
                font-family: 'Courier New', monospace;
                font-size: 0.875rem;
            }
            .summary-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin: 20px 0;
            }
            .stat-card {
                background: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
        </style>\n";
        
        $this->testConfiguration();
        $this->testDirectories();
        $this->testApiKeys();
        $this->testErrorHandling();
        $this->testFileValidation();
        
        $this->displaySummary();
    }
    
    private function testConfiguration() {
        echo "<div class='test-section'>\n";
        echo "<h2>Configuration Tests</h2>\n";
        
        // Test constants
        $this->runTest('GEMINI_API_KEYS defined', defined('GEMINI_API_KEYS'));
        $this->runTest('UPLOAD_DIR defined', defined('UPLOAD_DIR'));
        $this->runTest('MAX_FILE_SIZE defined', defined('MAX_FILE_SIZE'));
        $this->runTest('ALLOWED_AUDIO_TYPES defined', defined('ALLOWED_AUDIO_TYPES'));
        
        // Test API keys format
        $apiKeys = GEMINI_API_KEYS;
        $this->runTest('API keys is array', is_array($apiKeys));
        $this->runTest('API keys not empty', !empty($apiKeys));
        
        foreach ($apiKeys as $index => $key) {
            $this->runTest("API key $index format valid", 
                is_string($key) && strlen($key) > 30 && strpos($key, 'AIza') === 0);
        }
        
        echo "</div>\n";
    }
    
    private function testDirectories() {
        echo "<div class='test-section'>\n";
        echo "<h2>Directory Tests</h2>\n";
        
        $this->runTest('Upload directory exists', is_dir(UPLOAD_DIR));
        $this->runTest('Upload directory writable', is_writable(UPLOAD_DIR));
        $this->runTest('Log directory exists', is_dir(dirname(LOG_FILE)));
        $this->runTest('Log directory writable', is_writable(dirname(LOG_FILE)));
        
        // Test permissions
        $uploadPerms = substr(sprintf('%o', fileperms(UPLOAD_DIR)), -4);
        $this->runTest('Upload directory permissions OK', $uploadPerms >= '0755');
        
        echo "</div>\n";
    }
    
    private function testApiKeys() {
        echo "<div class='test-section'>\n";
        echo "<h2>API Connectivity Tests</h2>\n";
        
        // Test a simple API call (without audio)
        try {
            $testPrompt = "Please respond with exactly: 'API_TEST_SUCCESS'";
            
            $apiKey = GEMINI_API_KEYS[0];
            $url = GEMINI_API_URL . "?key=" . $apiKey;
            
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $testPrompt]
                        ]
                    ]
                ]
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $this->runTest('CURL executed without error', empty($error));
            $this->runTest('API returned HTTP 200', $httpCode === 200);
            
            if ($httpCode === 200 && !$error) {
                $decodedResponse = json_decode($response, true);
                $this->runTest('API response is valid JSON', $decodedResponse !== null);
                
                if ($decodedResponse) {
                    $hasContent = isset($decodedResponse['candidates'][0]['content']['parts'][0]['text']);
                    $this->runTest('API response has expected structure', $hasContent);
                    
                    if ($hasContent) {
                        $responseText = $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
                        echo "<p><strong>API Response:</strong> " . htmlspecialchars($responseText) . "</p>\n";
                    }
                }
            } else {
                echo "<p><strong>API Error:</strong> HTTP $httpCode - " . htmlspecialchars($response) . "</p>\n";
            }
            
        } catch (Exception $e) {
            $this->runTest('API test completed without exception', false);
            echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
        }
        
        echo "</div>\n";
    }
    
    private function testErrorHandling() {
        echo "<div class='test-section'>\n";
        echo "<h2>Error Handling Tests</h2>\n";
        
        // Test error handler initialization
        $errorHandler = ErrorHandler::getInstance();
        $this->runTest('Error handler initialized', $errorHandler !== null);
        
        // Test custom error logging
        try {
            $errorHandler->logCustomError('Test error message', ['test' => true]);
            $this->runTest('Custom error logging works', true);
        } catch (Exception $e) {
            $this->runTest('Custom error logging works', false);
        }
        
        // Test log file creation
        $this->runTest('Log file exists or created', file_exists(LOG_FILE));
        
        echo "</div>\n";
    }
    
    private function testFileValidation() {
        echo "<div class='test-section'>\n";
        echo "<h2>File Validation Tests</h2>\n";
        
        // Test allowed file types
        $allowedTypes = ALLOWED_AUDIO_TYPES;
        $this->runTest('Audio types array not empty', !empty($allowedTypes));
        $this->runTest('MP3 type allowed', in_array('audio/mpeg', $allowedTypes) || in_array('audio/mp3', $allowedTypes));
        $this->runTest('WAV type allowed', in_array('audio/wav', $allowedTypes));
        
        // Test max file size
        $maxSize = MAX_FILE_SIZE;
        $this->runTest('Max file size reasonable', $maxSize > 1024 * 1024 && $maxSize <= 100 * 1024 * 1024);
        
        echo "<p><strong>Max file size:</strong> " . number_format($maxSize / 1024 / 1024, 2) . " MB</p>\n";
        echo "<p><strong>Allowed types:</strong> " . implode(', ', $allowedTypes) . "</p>\n";
        
        echo "</div>\n";
    }
    
    private function runTest($testName, $condition) {
        $result = $condition ? 'PASS' : 'FAIL';
        $class = $condition ? 'test-pass' : 'test-fail';
        
        echo "<p class='$class'>[$result] $testName</p>\n";
        
        $this->testResults[] = [
            'name' => $testName,
            'result' => $condition
        ];
    }
    
    private function displaySummary() {
        echo "<div class='test-section'>\n";
        echo "<h2>Test Summary</h2>\n";
        
        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($test) {
            return $test['result'] === true;
        }));
        $failedTests = $totalTests - $passedTests;
        
        echo "<div class='summary-stats'>\n";
        echo "<div class='stat-card'><h3 class='text-lg font-semibold text-gray-700'>Total Tests</h3><p class='text-2xl font-bold text-blue-600'>$totalTests</p></div>\n";
        echo "<div class='stat-card'><h3 class='text-lg font-semibold text-gray-700'>Passed</h3><p class='text-2xl font-bold text-green-600'>$passedTests</p></div>\n";
        echo "<div class='stat-card'><h3 class='text-lg font-semibold text-gray-700'>Failed</h3><p class='text-2xl font-bold text-red-600'>$failedTests</p></div>\n";
        
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
        echo "<div class='stat-card'><h3 class='text-lg font-semibold text-gray-700'>Success Rate</h3><p class='text-2xl font-bold text-purple-600'>$successRate%</p></div>\n";
        echo "</div>\n";
        
        if ($failedTests > 0) {
            echo "<h3>Failed Tests:</h3>\n";
            echo "<ul>\n";
            foreach ($this->testResults as $test) {
                if (!$test['result']) {
                    echo "<li class='test-fail'>" . htmlspecialchars($test['name']) . "</li>\n";
                }
            }
            echo "</ul>\n";
        }
        
        // System information
        echo "<h3>System Information:</h3>\n";
        echo "<pre>\n";
        echo "PHP Version: " . PHP_VERSION . "\n";
        echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
        echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
        echo "Post Max Size: " . ini_get('post_max_size') . "\n";
        echo "Memory Limit: " . ini_get('memory_limit') . "\n";
        echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
        echo "CURL Available: " . (extension_loaded('curl') ? 'Yes' : 'No') . "\n";
        echo "JSON Available: " . (extension_loaded('json') ? 'Yes' : 'No') . "\n";
        echo "FileInfo Available: " . (extension_loaded('fileinfo') ? 'Yes' : 'No') . "\n";
        echo "</pre>\n";
        
        echo "</div>\n";
        
        // Close HTML tags
        echo "</div></div></div>";
        echo "<footer class='bg-gray-900 text-white py-8 mt-16'>";
        echo "<div class='container mx-auto px-4'>";
        echo "<div class='flex flex-col md:flex-row justify-between items-center'>";
        echo "<div class='flex items-center mb-4 md:mb-0'>";
        echo "<i class='fas fa-vial text-blue-400 text-xl mr-3'></i>";
        echo "<span class='text-lg font-semibold'>TrackerBI</span>";
        echo "</div>";
        echo "<div class='text-center md:text-right'>";
        echo "<p class='text-gray-300'>&copy; 2025 TrackerBI</p>";
        echo "<p class='text-sm text-gray-400 mt-1'>System Test Suite</p>";
        echo "</div>";
        echo "</div>";
        echo "<div class='border-t border-gray-700 mt-6 pt-6 text-center'>";
        echo "<div class='flex justify-center space-x-6 text-sm text-gray-400'>";
        echo "<span><i class='fas fa-check-circle mr-1'></i>Diagnostics</span>";
        echo "<span><i class='fas fa-cog mr-1'></i>Configuration</span>";
        echo "<span><i class='fas fa-shield-alt mr-1'></i>Validation</span>";
        echo "</div>";
        echo "</div>";
        echo "</div></footer>";
        echo "</body></html>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test.php') {
    $tester = new SystemTester();
    $tester->runAllTests();
}
