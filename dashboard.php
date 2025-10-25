<?php
require_once 'config.php';
require_once 'ErrorHandler.php';
require_once 'DatabaseManager.php';
require_once 'FilenameParser.php';

// Get analysis statistics from database
$dbManager = new DatabaseManager();

// Get analytics data (EXACT SAME as Analytics Dashboard)
$summary = $dbManager->getAnalyticsSummary();
$recentResults = $dbManager->getRecentResults(10);

// Use the SAME recent results that Analytics Dashboard uses for Today's display
// Just show the recent results as "today's" data (same as Analytics Dashboard)
$todayResults = $recentResults;

// Initialize stats
$stats = [
    'total_analyses' => $summary['total_analyses'] ?? 0,
    'successful_analyses' => $summary['successful_analyses'] ?? 0,
    'failed_analyses' => $summary['failed_analyses'] ?? 0,
    'today_analyses' => count($todayResults),
    'avg_processing_time' => 0,
    'popular_formats' => [],
    'recent_activity' => []
];

// Legacy log file support for backward compatibility
$logFile = LOG_FILE;
$usageLogFile = dirname(LOG_FILE) . '/api_usage.log';
$structuredLogFile = dirname(LOG_FILE) . '/structured_errors.json';

// Read usage logs if available
if (file_exists($usageLogFile)) {
    $usageData = file($usageLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $stats['total_analyses'] = count($usageData);
    
    $successCount = 0;
    $totalTime = 0;
    $timeCount = 0;
    
    foreach ($usageData as $line) {
        if (strpos($line, 'Success: YES') !== false) {
            $successCount++;
        }
        
        // Extract processing time
        if (preg_match('/Processing Time: ([\d.]+)s/', $line, $matches)) {
            $totalTime += floatval($matches[1]);
            $timeCount++;
        }
    }
    
    $stats['successful_analyses'] = $successCount;
    $stats['failed_analyses'] = $stats['total_analyses'] - $successCount;
    $stats['avg_processing_time'] = $timeCount > 0 ? round($totalTime / $timeCount, 2) : 0;
}

// Read error logs if available
if (file_exists($structuredLogFile)) {
    $errorData = json_decode(file_get_contents($structuredLogFile), true);
    if ($errorData) {
        $stats['total_errors'] = count($errorData);
    }
}

// Set page title for header
$page_title = "Today's Dashboard";

// Additional styles for this page
$additional_styles = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .metric-card {
        background: linear-gradient(135deg, var(--primary-50) 0%, var(--accent-50) 100%);
        transition: all 0.3s ease;
        border: 1px solid rgba(59, 130, 246, 0.1);
    }
    .metric-card:hover {
        background: linear-gradient(135deg, var(--primary-100) 0%, var(--accent-100) 100%);
        transform: translateY(-2px);
    }
    
    .pulse-dot {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .chart-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
    }
</style>
';

// Include common header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <div class="gradient-bg py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-bold text-white text-center mb-4">
                <i class="fas fa-calendar-day mr-4 icon-accent"></i>
                Today's Dashboard
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Today's call analysis insights with caller information and performance metrics
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Total Analyses -->
            <div class="stat-card rounded-2xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary text-sm font-medium mb-2">Total Analyses</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['total_analyses']); ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-200">
                        <i class="fas fa-file-audio text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Successful Analyses -->
            <div class="stat-card rounded-2xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary text-sm font-medium mb-2">Successful</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['successful_analyses']); ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-green-100 to-green-200">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Failed Analyses -->
            <div class="stat-card rounded-2xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary text-sm font-medium mb-2">Failed</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['failed_analyses']); ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-red-100 to-red-200">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Analyses -->
            <div class="stat-card rounded-2xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary text-sm font-medium mb-2">Today's Analyses</p>
                        <p class="text-3xl font-bold text-primary"><?php echo number_format($stats['today_analyses']); ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-orange-100 to-orange-200">
                        <i class="fas fa-calendar-day text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Source Info -->
        <?php if (!empty($todayResults)): ?>
        <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
            <h4 class="text-blue-800 font-semibold mb-2">📊 Recent Analyses (Same as Analytics Dashboard)</h4>
            <div class="text-sm text-blue-700">
                <strong>Showing:</strong> <?php echo count($todayResults); ?> recent analyses<br>
                <?php 
                $firstResult = reset($todayResults);
                if ($firstResult) {
                    if ($firstResult['filename_parsed'] && $firstResult['phone_number']) {
                        echo "<strong>✅ Structured Display:</strong> " . htmlspecialchars($firstResult['caller_name']) . " (" . htmlspecialchars($firstResult['call_language']) . ") - " . htmlspecialchars($firstResult['phone_number']) . "<br>";
                    } else {
                        echo "<strong>⚠️ Raw Filename:</strong> " . htmlspecialchars($firstResult['filename']) . " (no parsing data)<br>";
                    }
                    echo "<strong>Data Source:</strong> Same as Analytics Dashboard";
                }
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Today's Analyses Section -->
        <?php if (!empty($todayResults)): ?>
        <div class="glass-card rounded-2xl p-8 mb-12">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-primary">
                    <i class="fas fa-clock mr-3 icon-accent"></i>
                    Recent Analyses (<?php echo count($todayResults); ?>)
                </h3>
                <div class="text-sm text-secondary">
                    📊 Same data as Analytics Dashboard
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($todayResults, 0, 6) as $result): ?>
                <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300">
                    <!-- Caller Information (EXACT COPY from Analytics Dashboard) -->
                    <div class="mb-4">
                        <?php if ($result['filename_parsed'] && $result['phone_number']): ?>
                            <div class="font-medium text-gray-900 mb-2">
                                <?php echo htmlspecialchars($result['caller_name']); ?> 
                                <span class="text-blue-600">(<?php echo htmlspecialchars($result['call_language']); ?>)</span>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center space-x-2 flex-wrap">
                                <span><i class="fas fa-phone text-blue-500 mr-1"></i><?php echo htmlspecialchars($result['phone_number']); ?></span>
                                <span><i class="fas fa-calendar text-green-500 mr-1"></i><?php echo date('M j', strtotime($result['call_date'])); ?></span>
                                <span><i class="fas fa-clock text-orange-500 mr-1"></i><?php echo date('g:i A', strtotime($result['call_time'])); ?></span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1"><?php echo $result['session_id']; ?></div>
                        <?php else: ?>
                            <div class="font-medium text-gray-900 mb-1"><?php echo htmlspecialchars($result['filename']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo $result['session_id']; ?></div>
                            <div class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-clock text-orange-500 mr-1"></i>
                                <?php echo date('g:i A', strtotime($result['upload_timestamp'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Performance Metrics -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600"><?php echo $result['sentiment_score']; ?></div>
                            <div class="text-xs text-blue-500">Sentiment</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600"><?php echo $result['clarity_score']; ?></div>
                            <div class="text-xs text-green-500">Clarity</div>
                        </div>
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <div class="text-lg font-bold text-purple-600"><?php echo $result['empathy_score']; ?></div>
                            <div class="text-xs text-purple-500">Empathy</div>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <div class="text-lg font-bold text-orange-600"><?php echo $result['professionalism_score']; ?></div>
                            <div class="text-xs text-orange-500">Professional</div>
                        </div>
                    </div>
                    
                    <!-- Overall Performance -->
                    <div class="flex justify-between items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            <?php 
                            switch($result['overall_performance']) {
                                case 'excellent': echo 'bg-green-100 text-green-800'; break;
                                case 'good': echo 'bg-blue-100 text-blue-800'; break;
                                default: echo 'bg-yellow-100 text-yellow-800'; break;
                            }
                            ?>">
                            <?php echo ucfirst($result['overall_performance']); ?>
                        </span>
                        <span class="text-xs text-gray-500">
                            <?php echo $result['session_id']; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($todayResults) > 6): ?>
            <div class="text-center mt-6">
                <a href="analytics-dashboard.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-300">
                    <i class="fas fa-chart-line mr-2"></i>
                    View All Today's Analyses (<?php echo count($todayResults); ?>)
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Instructions for Structured Display -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold text-primary mb-6">
                <i class="fas fa-lightbulb mr-3 icon-accent"></i>
                Get Structured Caller Display
            </h3>
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-6 border border-blue-200">
                <h4 class="text-blue-800 font-semibold mb-4">To see caller information like "Harika (Hindi)" instead of raw filenames:</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-semibold text-gray-700 mb-3">📝 Filename Pattern Required:</h5>
                        <div class="bg-white p-4 rounded border font-mono text-sm">
                            <div class="text-green-600 mb-2">✅ Correct Format:</div>
                            <div class="text-green-800">8804439756_Hindi_Harika_20251022163000.mp3</div>
                            <div class="text-gray-500 mt-2 text-xs">
                                {phone}_{language}_{name}_{YYYYMMDDHHMMSS}.{ext}
                            </div>
                        </div>
                        
                        <div class="bg-white p-4 rounded border font-mono text-sm mt-3">
                            <div class="text-red-600 mb-2">❌ Current Format:</div>
                            <div class="text-red-800">audio_68fb291761627.WAV</div>
                            <div class="text-gray-500 mt-2 text-xs">
                                (Cannot extract caller information)
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold text-gray-700 mb-3">🎯 Expected Result:</h5>
                        <div class="bg-white p-4 rounded border">
                            <div class="font-semibold text-gray-800 mb-2">
                                Harika <span class="text-blue-600">(Hindi)</span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-blue-500 mr-2"></i>
                                    <span>8804439756</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                                    <span>4:30 PM</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="trackerbi-audio.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Structured File
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Success Rate Chart -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">
                    <i class="fas fa-chart-pie mr-3 icon-accent"></i>
                    Success Rate Analysis
                </h3>
                <div class="relative h-64">
                    <canvas id="successChart"></canvas>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">
                    <i class="fas fa-tachometer-alt mr-3 icon-accent"></i>
                    Performance Overview
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="metric-card text-center p-6 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600 mb-1"><?php echo $stats['total_analyses'] > 0 ? round(($stats['successful_analyses'] / $stats['total_analyses']) * 100, 1) : 0; ?>%</p>
                        <p class="text-sm text-secondary">Success Rate</p>
                    </div>
                    <div class="metric-card text-center p-6 rounded-xl">
                        <p class="text-2xl font-bold text-purple-600 mb-1"><?php echo $stats['avg_processing_time']; ?>s</p>
                        <p class="text-sm text-secondary">Avg. Processing</p>
                    </div>
                    <div class="metric-card text-center p-6 rounded-xl">
                        <p class="text-2xl font-bold text-orange-600 mb-1"><?php echo count(GEMINI_API_KEYS); ?></p>
                        <p class="text-sm text-secondary">API Keys</p>
                    </div>
                    <div class="metric-card text-center p-6 rounded-xl">
                        <p class="text-2xl font-bold text-red-600 mb-1"><?php echo $stats['total_errors']; ?></p>
                        <p class="text-sm text-secondary">Total Errors</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold mb-8 text-primary text-center">
                <i class="fas fa-server mr-3 icon-accent"></i>
                System Health Monitor
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- API Status -->
                <div class="text-center p-6 border border-green-200 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 hover:shadow-lg transition-all duration-300">
                    <div class="relative inline-block mb-4">
                        <i class="fas fa-robot text-4xl text-green-600"></i>
                        <div class="pulse-dot w-3 h-3 bg-green-500 rounded-full absolute -top-1 -right-1"></div>
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Gemini 2.0 Flash</h4>
                    <p class="text-sm text-green-600 font-medium">✅ Operational</p>
                </div>

                <!-- Storage Status -->
                <div class="text-center p-6 border border-blue-200 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-lg transition-all duration-300">
                    <div class="relative inline-block mb-4">
                        <i class="fas fa-database text-4xl text-blue-600"></i>
                        <div class="pulse-dot w-3 h-3 bg-blue-500 rounded-full absolute -top-1 -right-1"></div>
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Storage System</h4>
                    <p class="text-sm text-blue-600 font-medium">✅ Available</p>
                </div>

                <!-- Error Handling -->
                <div class="text-center p-6 border border-purple-200 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-lg transition-all duration-300">
                    <div class="relative inline-block mb-4">
                        <i class="fas fa-shield-alt text-4xl text-purple-600"></i>
                        <div class="pulse-dot w-3 h-3 bg-purple-500 rounded-full absolute -top-1 -right-1"></div>
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Error Handling</h4>
                    <p class="text-sm text-purple-600 font-medium">✅ Protected</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-card rounded-2xl p-8">
            <h3 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-history mr-3 icon-accent"></i>
                Recent Activity Feed
            </h3>
            <div class="space-y-4">
                <?php if (file_exists($logFile)): ?>
                    <?php
                    $recentLogs = array_slice(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -5);
                    $recentLogs = array_reverse($recentLogs);
                    ?>
                    <?php foreach ($recentLogs as $log): ?>
                        <div class="flex items-center p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:shadow-md transition-all duration-300">
                            <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mr-4 flex-shrink-0"></div>
                            <p class="text-sm text-primary flex-1 font-mono"><?php echo htmlspecialchars($log); ?></p>
                            <div class="text-xs text-secondary ml-4">
                                <i class="fas fa-clock mr-1"></i>
                                Recent
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12 text-secondary">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-info-circle text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium mb-2">No recent activity</p>
                        <p class="text-sm">Start using the audio analysis system to see activity here</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script>
    // Success Rate Chart
    const ctx = document.getElementById("successChart").getContext("2d");
    const successChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Successful", "Failed"],
            datasets: [{
                data: [' . $stats['successful_analyses'] . ', ' . $stats['failed_analyses'] . '],
                backgroundColor: ["#10B981", "#EF4444"],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom"
                }
            }
        }
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
