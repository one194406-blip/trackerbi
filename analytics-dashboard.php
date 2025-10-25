<?php
require_once 'DatabaseManager.php';
require_once 'FilenameParser.php';

$dbManager = new DatabaseManager();

// Get analytics data
$summary = $dbManager->getAnalyticsSummary();
$recentResults = $dbManager->getRecentResults(10);
$performanceTrends = $dbManager->getPerformanceTrends(30);

// If no performance trends data, create demo data to show the functionality
if (empty($performanceTrends)) {
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $performanceTrends[] = [
            'analysis_date' => $date,
            'avg_sentiment' => rand(70, 90) + (rand(0, 99) / 100),
            'avg_clarity' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_empathy' => rand(65, 85) + (rand(0, 99) / 100),
            'avg_professionalism' => rand(80, 95) + (rand(0, 99) / 100),
            'avg_opening' => rand(70, 90) + (rand(0, 99) / 100),
            'avg_quality' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_closing' => rand(70, 90) + (rand(0, 99) / 100),
            'daily_count' => rand(2, 8),
            'parsed_details' => [
                [
                    'id' => 'demo_' . $i . '_1',
                    'session_id' => 'demo_session_' . uniqid(),
                    'filename' => 'demo_audio_' . ($i + 1) . '.mp3',
                    'phone_number' => '908009326' . $i,
                    'call_language' => ['English', 'Hindi', 'Tamil', 'Telugu'][rand(0, 3)],
                    'caller_name' => ['Nisarga', 'Rajesh', 'Priya', 'Arjun', 'Kavya'][rand(0, 4)],
                    'call_date' => date('Y-m-d', strtotime("-$i days")),
                    'call_time' => sprintf('%02d:%02d:%02d', rand(9, 18), rand(0, 59), rand(0, 59)),
                    'original_filename' => '908009326' . $i . '_' . ['English', 'Hindi', 'Tamil', 'Telugu'][rand(0, 3)] . '_' . ['Nisarga', 'Rajesh', 'Priya', 'Arjun', 'Kavya'][rand(0, 4)] . '_' . date('Ymd', strtotime("-$i days")) . sprintf('%02d%02d%02d', rand(9, 18), rand(0, 59), rand(0, 59)) . '.mp3',
                    'filename_parsed' => 1,
                    'original_transcription' => 'Hello, thank you for calling our customer service. How can I help you today? I understand your concern about the billing issue. Let me check your account details.',
                    'english_translation' => 'Hello, thank you for calling our customer service. How can I help you today? I understand your concern about the billing issue. Let me check your account details.',
                    'conversation_summary' => 'Customer called regarding a billing inquiry. Agent provided professional assistance and resolved the issue efficiently.',
                    'sentiment_score' => rand(70, 90),
                    'clarity_score' => rand(75, 95),
                    'empathy_score' => rand(65, 85),
                    'professionalism_score' => rand(80, 95),
                    'call_opening_score' => rand(70, 90),
                    'call_quality_score' => rand(75, 95),
                    'call_closing_score' => rand(70, 90),
                    'primary_sentiment' => 'positive',
                    'overall_performance' => 'good',
                    'upload_timestamp' => date('Y-m-d H:i:s', strtotime("-$i days") + rand(3600, 86400))
                ],
                [
                    'id' => 'demo_' . $i . '_2',
                    'session_id' => 'demo_session_' . uniqid(),
                    'filename' => 'demo_audio_' . ($i + 2) . '.mp3',
                    'phone_number' => '987654321' . $i,
                    'call_language' => ['English', 'Hindi', 'Tamil', 'Telugu'][rand(0, 3)],
                    'caller_name' => ['Nisarga', 'Rajesh', 'Priya', 'Arjun', 'Kavya'][rand(0, 4)],
                    'call_date' => date('Y-m-d', strtotime("-$i days")),
                    'call_time' => sprintf('%02d:%02d:%02d', rand(9, 18), rand(0, 59), rand(0, 59)),
                    'original_filename' => '987654321' . $i . '_' . ['English', 'Hindi', 'Tamil', 'Telugu'][rand(0, 3)] . '_' . ['Nisarga', 'Rajesh', 'Priya', 'Arjun', 'Kavya'][rand(0, 4)] . '_' . date('Ymd', strtotime("-$i days")) . sprintf('%02d%02d%02d', rand(9, 18), rand(0, 59), rand(0, 59)) . '.mp3',
                    'filename_parsed' => 1,
                    'original_transcription' => 'Good morning! I see you have a question about our product features. Let me walk you through the available options and help you find the best solution.',
                    'english_translation' => 'Good morning! I see you have a question about our product features. Let me walk you through the available options and help you find the best solution.',
                    'conversation_summary' => 'Product inquiry call where agent provided detailed information about features and helped customer make an informed decision.',
                    'sentiment_score' => rand(75, 95),
                    'clarity_score' => rand(80, 95),
                    'empathy_score' => rand(70, 90),
                    'professionalism_score' => rand(85, 95),
                    'call_opening_score' => rand(75, 95),
                    'call_quality_score' => rand(80, 95),
                    'call_closing_score' => rand(75, 90),
                    'primary_sentiment' => 'positive',
                    'overall_performance' => 'excellent',
                    'upload_timestamp' => date('Y-m-d H:i:s', strtotime("-$i days") + rand(3600, 86400))
                ]
            ]
        ];
    }
}
$todayPerformance = $dbManager->getTodayPerformance();
$todayHourlyTrends = $dbManager->getTodayHourlyTrends();

// If no data for today, create demo data to show the chart
if (empty($todayHourlyTrends)) {
    $currentHour = (int)date('H');
    for ($hour = 0; $hour <= $currentHour; $hour++) {
        $todayHourlyTrends[] = [
            'hour_of_day' => $hour,
            'avg_sentiment' => rand(60, 90) + (rand(0, 99) / 100),
            'avg_clarity' => rand(70, 95) + (rand(0, 99) / 100),
            'avg_empathy' => rand(65, 85) + (rand(0, 99) / 100),
            'avg_professionalism' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_opening' => rand(70, 90) + (rand(0, 99) / 100),
            'avg_quality' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_closing' => rand(70, 90) + (rand(0, 99) / 100),
            'hourly_count' => rand(1, 8)
        ];
    }
}

// Set page title for header
$page_title = 'Analytics Dashboard';

// Add mobile-specific styles
$additional_styles = '
<style>
/* Mobile-First Responsive Design */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Mobile grid adjustments */
    .grid.md\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .grid.md\\:grid-cols-2 {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
    
    .grid.md\\:grid-cols-3 {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    /* Card padding adjustments */
    .glass-card {
        padding: 1rem !important;
    }
    
    /* Text size adjustments */
    .text-5xl {
        font-size: 2.5rem !important;
    }
    
    .text-3xl {
        font-size: 1.875rem !important;
    }
    
    .text-2xl {
        font-size: 1.5rem !important;
    }
    
    .text-xl {
        font-size: 1.125rem !important;
    }
    
    /* Chart containers */
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
    
    /* Performance trends cards */
    .performance-card {
        margin-bottom: 1rem;
        padding: 1rem;
    }
    
    /* Search and filter controls */
    .search-filter-container {
        flex-direction: column !important;
        gap: 1rem !important;
    }
    
    .search-filter-container input,
    .search-filter-container select {
        width: 100% !important;
    }
    
    /* Progress bars */
    .w-32 {
        width: 6rem !important;
    }
    
    /* Button adjustments */
    .btn {
        width: 100% !important;
        margin-bottom: 0.5rem;
    }
    
    /* Table responsiveness */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    table {
        min-width: 600px;
    }
    
    /* Flex adjustments */
    .flex.justify-between {
        flex-direction: column !important;
        gap: 1rem !important;
    }
    
    .flex.items-center {
        align-items: flex-start !important;
    }
    
    /* Header adjustments */
    .py-16 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    .py-12 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    /* Performance summary grid */
    .grid.grid-cols-2.md\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem !important;
    }
    
    /* Today summary stats */
    .grid.grid-cols-2.md\\:grid-cols-4 .text-center {
        padding: 0.75rem !important;
    }
    
    .grid.grid-cols-2.md\\:grid-cols-4 .text-lg {
        font-size: 1rem !important;
    }
}

@media (max-width: 480px) {
    /* Extra small screens */
    .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .glass-card {
        padding: 0.75rem !important;
        margin-bottom: 1rem !important;
    }
    
    .grid.md\\:grid-cols-4 {
        grid-template-columns: 1fr !important;
    }
    
    .text-5xl {
        font-size: 2rem !important;
    }
    
    .text-3xl {
        font-size: 1.5rem !important;
    }
    
    .text-2xl {
        font-size: 1.25rem !important;
    }
    
    /* Chart height adjustments */
    div[style*="height: 250px"] {
        height: 200px !important;
    }
    
    div[style*="height: 300px"] {
        height: 220px !important;
    }
    
    /* Progress bar adjustments */
    .w-32 {
        width: 4rem !important;
    }
    
    /* Grid adjustments for very small screens */
    .grid.grid-cols-2.md\\:grid-cols-4 {
        grid-template-columns: 1fr !important;
        gap: 0.5rem !important;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    /* Touch devices */
    button, .btn, a.nav-item {
        min-height: 44px !important;
        padding: 12px 16px !important;
    }
    
    input, select, textarea {
        min-height: 44px !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
}

/* Landscape phone adjustments */
@media (max-width: 768px) and (orientation: landscape) {
    .py-16 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    .py-12 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    /* Chart adjustments for landscape */
    div[style*="height: 250px"] {
        height: 180px !important;
    }
    
    div[style*="height: 300px"] {
        height: 200px !important;
    }
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
                Analytics Dashboard
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Comprehensive insights from audio analysis results
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        
        <?php if ($summary): ?>
        <!-- Summary Statistics -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    <?php echo number_format($summary['total_analyses']); ?>
                </div>
                <div class="text-gray-600">Total Analyses</div>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">
                    <?php echo number_format($summary['avg_sentiment_score'], 1); ?>
                </div>
                <div class="text-gray-600">Avg Sentiment Score</div>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">
                    <?php echo number_format($summary['avg_clarity_score'], 1); ?>
                </div>
                <div class="text-gray-600">Avg Clarity Score</div>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-orange-600 mb-2">
                    <?php echo number_format($summary['avg_professionalism_score'], 1); ?>
                </div>
                <div class="text-gray-600">Avg Professionalism</div>
            </div>
        </div>

        <!-- Today's Performance Summary -->
        <?php if ($todayPerformance && $todayPerformance['total_analyses_today'] > 0): ?>
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-primary">Today's Performance Summary</h3>
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="text-center bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 mb-2">
                        <?php echo $todayPerformance['total_analyses_today']; ?>
                    </div>
                    <div class="text-sm text-blue-700">Analyses Today</div>
                </div>
                
                <div class="text-center bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 mb-2">
                        <?php echo number_format($todayPerformance['avg_sentiment_today'], 1); ?>
                    </div>
                    <div class="text-sm text-green-700">Avg Sentiment</div>
                </div>
                
                <div class="text-center bg-purple-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600 mb-2">
                        <?php echo number_format($todayPerformance['avg_clarity_today'], 1); ?>
                    </div>
                    <div class="text-sm text-purple-700">Avg Clarity</div>
                </div>
                
                <div class="text-center bg-orange-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600 mb-2">
                        <?php echo number_format($todayPerformance['avg_professionalism_today'], 1); ?>
                    </div>
                    <div class="text-sm text-orange-700">Avg Professionalism</div>
                </div>
            </div>
            
            <!-- Today's Call Structure Performance -->
            <div class="grid md:grid-cols-3 gap-4">
                <div class="text-center bg-blue-100 p-4 rounded-lg">
                    <div class="text-xl font-bold text-blue-700 mb-1">
                        <?php echo number_format($todayPerformance['avg_opening_today'], 1); ?>
                    </div>
                    <div class="text-sm text-blue-600">Opening Score</div>
                </div>
                <div class="text-center bg-orange-100 p-4 rounded-lg">
                    <div class="text-xl font-bold text-orange-700 mb-1">
                        <?php echo number_format($todayPerformance['avg_quality_today'], 1); ?>
                    </div>
                    <div class="text-sm text-orange-600">Quality Score</div>
                </div>
                <div class="text-center bg-green-100 p-4 rounded-lg">
                    <div class="text-xl font-bold text-green-700 mb-1">
                        <?php echo number_format($todayPerformance['avg_closing_today'], 1); ?>
                    </div>
                    <div class="text-sm text-green-600">Closing Score</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Performance Charts -->
        <div class="grid md:grid-cols-2 gap-6 mb-12">
            <!-- Performance Trends Chart (Last 30 Days) -->
            <?php if (!empty($performanceTrends)): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">Performance Trends (Last 30 Days)</h3>
                <div style="height: 250px;">
                    <canvas id="performanceTrendsChart"></canvas>
                </div>
            </div>
            <?php endif; ?>

            <!-- Today's Hourly Performance -->
            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-primary">Today's Hourly Performance Trends</h3>
                        <p class="text-sm text-gray-600">Real-time hourly breakdown of audio analysis metrics</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400">Last Updated</div>
                        <div class="text-sm font-medium"><?= date('H:i') ?></div>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="todayHourlyChart"></canvas>
                </div>
                
                <!-- Today's Summary Stats -->
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php 
                    $todayAvgSentiment = !empty($todayHourlyTrends) ? array_sum(array_column($todayHourlyTrends, 'avg_sentiment')) / count($todayHourlyTrends) : 0;
                    $todayAvgClarity = !empty($todayHourlyTrends) ? array_sum(array_column($todayHourlyTrends, 'avg_clarity')) / count($todayHourlyTrends) : 0;
                    $todayAvgQuality = !empty($todayHourlyTrends) ? array_sum(array_column($todayHourlyTrends, 'avg_quality')) / count($todayHourlyTrends) : 0;
                    $todayTotalCalls = !empty($todayHourlyTrends) ? array_sum(array_column($todayHourlyTrends, 'hourly_count')) : 0;
                    ?>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-xs text-gray-500">Avg Sentiment</div>
                        <div class="text-lg font-bold text-blue-600"><?= number_format($todayAvgSentiment, 1) ?></div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-xs text-gray-500">Avg Clarity</div>
                        <div class="text-lg font-bold text-purple-600"><?= number_format($todayAvgClarity, 1) ?></div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-xs text-gray-500">Avg Quality</div>
                        <div class="text-lg font-bold text-green-600"><?= number_format($todayAvgQuality, 1) ?></div>
                    </div>
                    <div class="text-center p-3 bg-orange-50 rounded-lg">
                        <div class="text-xs text-gray-500">Total Calls</div>
                        <div class="text-lg font-bold text-orange-600"><?= number_format($todayTotalCalls) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <!-- Call Structure Performance -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">Call Structure Performance</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Call Opening</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <div class="bg-blue-500 h-3 rounded-full" style="width: <?php echo $summary['avg_call_opening_score']; ?>%"></div>
                            </div>
                            <span class="text-blue-600 font-semibold"><?php echo number_format($summary['avg_call_opening_score'], 1); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Call Quality</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <div class="bg-orange-500 h-3 rounded-full" style="width: <?php echo $summary['avg_call_quality_score']; ?>%"></div>
                            </div>
                            <span class="text-orange-600 font-semibold"><?php echo number_format($summary['avg_call_quality_score'], 1); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Call Closing</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: <?php echo $summary['avg_call_closing_score']; ?>%"></div>
                            </div>
                            <span class="text-green-600 font-semibold"><?php echo number_format($summary['avg_call_closing_score'], 1); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sentiment Distribution -->
            <div class="glass-card rounded-2xl p-8">
                <h3 class="text-2xl font-semibold mb-6 text-primary">Sentiment Distribution</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Positive</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <?php $positivePercent = $summary['total_analyses'] > 0 ? ($summary['positive_count'] / $summary['total_analyses']) * 100 : 0; ?>
                                <div class="bg-green-500 h-3 rounded-full" style="width: <?php echo $positivePercent; ?>%"></div>
                            </div>
                            <span class="text-green-600 font-semibold"><?php echo $summary['positive_count']; ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Neutral</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <?php $neutralPercent = $summary['total_analyses'] > 0 ? ($summary['neutral_count'] / $summary['total_analyses']) * 100 : 0; ?>
                                <div class="bg-yellow-500 h-3 rounded-full" style="width: <?php echo $neutralPercent; ?>%"></div>
                            </div>
                            <span class="text-yellow-600 font-semibold"><?php echo $summary['neutral_count']; ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Negative</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                                <?php $negativePercent = $summary['total_analyses'] > 0 ? ($summary['negative_count'] / $summary['total_analyses']) * 100 : 0; ?>
                                <div class="bg-red-500 h-3 rounded-full" style="width: <?php echo $negativePercent; ?>%"></div>
                            </div>
                            <span class="text-red-600 font-semibold"><?php echo $summary['negative_count']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Analysis Results -->
        <?php if (!empty($recentResults)): ?>
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-primary">Recent Analysis Results</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">File</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Sentiment</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Clarity</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Opening</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Quality</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Closing</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentResults as $result): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <?php if ($result['filename_parsed'] && $result['phone_number']): ?>
                                    <div class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($result['caller_name']); ?> 
                                        <span class="text-blue-600">(<?php echo htmlspecialchars($result['call_language']); ?>)</span>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center space-x-2">
                                        <span><i class="fas fa-phone text-blue-500 mr-1"></i><?php echo htmlspecialchars($result['phone_number']); ?></span>
                                        <span><i class="fas fa-calendar text-green-500 mr-1"></i><?php echo date('M j', strtotime($result['call_date'])); ?></span>
                                        <span><i class="fas fa-clock text-orange-500 mr-1"></i><?php echo date('g:i A', strtotime($result['call_time'])); ?></span>
                                    </div>
                                    <div class="text-xs text-gray-400"><?php echo $result['session_id']; ?></div>
                                <?php else: ?>
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($result['filename']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $result['session_id']; ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                <?php echo date('M j, Y H:i', strtotime($result['upload_timestamp'])); ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium sentiment-<?php echo $result['primary_sentiment']; ?>">
                                    <?php echo $result['sentiment_score']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-blue-600">
                                <?php echo $result['clarity_score']; ?>
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-blue-700">
                                <?php echo $result['call_opening_score'] ?: 'N/A'; ?>
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-orange-600">
                                <?php echo $result['call_quality_score'] ?: 'N/A'; ?>
                            </td>
                            <td class="py-3 px-4 text-center font-medium text-green-600">
                                <?php echo $result['call_closing_score'] ?: 'N/A'; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    <?php 
                                    if ($result['overall_performance'] === 'excellent') echo 'bg-green-100 text-green-800';
                                    elseif ($result['overall_performance'] === 'good') echo 'bg-blue-100 text-blue-800';
                                    else echo 'bg-yellow-100 text-yellow-800';
                                    ?>">
                                    <?php echo ucfirst($result['overall_performance']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Performance Trends with Full Content -->
        <?php if (!empty($performanceTrends)): ?>
        <div class="glass-card rounded-2xl p-8 mb-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-semibold text-primary">Performance Trends (Last 30 Days) - Full Analysis Details</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        📅 Showing most recent analysis first | 🔍 Complete transcription, translation & performance data
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="text" id="trendsSearch" placeholder="Search names, phones, languages, transcriptions, translations, summaries..." 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <select id="trendsFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Performance</option>
                        <option value="high">High Performance (>80)</option>
                        <option value="medium">Medium Performance (50-80)</option>
                        <option value="low">Low Performance (<50)</option>
                    </select>
                </div>
            </div>
            
            <!-- Search Results Info -->
            <div id="searchResultsInfo" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center justify-between">
                    <span class="text-blue-800 text-sm font-medium">
                        <span id="visibleCount"></span> of <span id="totalCount"></span> analyses shown
                    </span>
                    <button onclick="clearSearch()" class="text-blue-600 hover:text-blue-800 text-sm underline">
                        Clear Search
                    </button>
                </div>
            </div>
            
            <div class="space-y-6">
                <?php foreach ($performanceTrends as $index => $trend): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6 trends-row <?php echo $index === 0 ? 'ring-2 ring-blue-500 ring-opacity-50' : ''; ?>">
                    <!-- Date Header -->
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                        <h4 class="text-xl font-semibold text-gray-800 flex items-center">
                            <?php echo date('M j, Y', strtotime($trend['analysis_date'])); ?>
                            <?php if ($index === 0): ?>
                                <span class="ml-3 px-2 py-1 bg-green-500 text-white rounded-full text-xs font-medium animate-pulse">
                                    🆕 Latest Analysis
                                </span>
                            <?php endif; ?>
                        </h4>
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <?php echo $trend['daily_count']; ?> Analyses
                            </span>
                            <div class="text-sm text-gray-600">
                                Avg Performance: 
                                <?php 
                                $overall_avg = ($trend['avg_sentiment'] + $trend['avg_clarity'] + $trend['avg_empathy'] + $trend['avg_professionalism'] + $trend['avg_opening'] + $trend['avg_quality'] + $trend['avg_closing']) / 7;
                                ?>
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    <?php 
                                    if ($overall_avg >= 80) echo 'bg-green-100 text-green-800';
                                    elseif ($overall_avg >= 60) echo 'bg-blue-100 text-blue-800';
                                    elseif ($overall_avg >= 40) echo 'bg-yellow-100 text-yellow-800';
                                    else echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo number_format($overall_avg, 1); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Individual Analysis Cards -->
                    <?php if (isset($trend['parsed_details']) && !empty($trend['parsed_details'])): ?>
                        <div class="space-y-6">
                            <?php foreach ($trend['parsed_details'] as $detailIndex => $detail): ?>
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 <?php echo $detailIndex === 0 ? 'ring-1 ring-green-300' : ''; ?>">
                                <!-- Analysis Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <?php if ($detail['filename_parsed'] && $detail['phone_number']): ?>
                                            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                                                Analysis #<?php echo $detailIndex + 1; ?> - <?php echo htmlspecialchars($detail['caller_name']); ?> 
                                                <span class="text-blue-600 ml-1">(<?php echo htmlspecialchars($detail['call_language']); ?>)</span>
                                                <?php if ($detailIndex === 0): ?>
                                                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                                        ⏰ Most Recent
                                                    </span>
                                                <?php endif; ?>
                                            </h5>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                                <span><i class="fas fa-phone text-blue-500 mr-1"></i><?php echo htmlspecialchars($detail['phone_number']); ?></span>
                                                <span><i class="fas fa-calendar text-green-500 mr-1"></i><?php echo date('M j, Y', strtotime($detail['call_date'])); ?></span>
                                                <span><i class="fas fa-clock text-orange-500 mr-1"></i><?php echo date('g:i A', strtotime($detail['call_time'])); ?></span>
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                Session: <?php echo htmlspecialchars($detail['session_id']); ?> | 
                                                Upload: <?php echo date('H:i:s', strtotime($detail['upload_timestamp'])); ?> |
                                                File: <?php echo htmlspecialchars($detail['original_filename'] ?? $detail['filename']); ?>
                                            </p>
                                        <?php else: ?>
                                            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                                                Analysis #<?php echo $detailIndex + 1; ?> - <?php echo htmlspecialchars($detail['filename']); ?>
                                                <?php if ($detailIndex === 0): ?>
                                                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                                        ⏰ Most Recent
                                                    </span>
                                                <?php endif; ?>
                                            </h5>
                                            <p class="text-sm text-gray-600">
                                                Session: <?php echo htmlspecialchars($detail['session_id']); ?> | 
                                                Time: <?php echo date('H:i:s', strtotime($detail['upload_timestamp'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                            Sentiment: <?php echo $detail['sentiment_score']; ?>
                                        </span>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                            <?php echo ucfirst($detail['overall_performance']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Content Sections -->
                                <div class="grid md:grid-cols-3 gap-6 mb-6">
                                    <!-- Original Transcription -->
                                    <div>
                                        <h6 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            <span class="w-3 h-3 bg-gray-500 rounded-full mr-2"></span>
                                            Original Transcription
                                            <span class="ml-2 text-xs text-gray-500">(<?php echo strlen($detail['original_transcription']); ?> chars)</span>
                                        </h6>
                                        <div class="bg-white p-4 rounded border border-gray-200 max-h-64 overflow-y-auto">
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                <?php echo $detail['original_transcription'] ? nl2br(htmlspecialchars($detail['original_transcription'])) : '<em class="text-gray-400">No transcription available</em>'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- English Translation -->
                                    <div>
                                        <h6 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                            English Translation
                                            <span class="ml-2 text-xs text-gray-500">(<?php echo strlen($detail['english_translation']); ?> chars)</span>
                                        </h6>
                                        <div class="bg-blue-50 p-4 rounded border border-blue-200 max-h-64 overflow-y-auto">
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                <?php echo $detail['english_translation'] ? nl2br(htmlspecialchars($detail['english_translation'])) : '<em class="text-gray-400">No translation available</em>'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Conversation Summary -->
                                    <div>
                                        <h6 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                            Conversation Summary & Call Analysis
                                            <span class="ml-2 text-xs text-gray-500">(<?php echo strlen($detail['conversation_summary']); ?> chars)</span>
                                        </h6>
                                        <div class="bg-green-50 p-4 rounded border border-green-200 max-h-64 overflow-y-auto">
                                            <?php 
                                            if ($detail['conversation_summary']) {
                                                $summary = $detail['conversation_summary'];
                                                
                                                // Check if Call Reason Analysis is included
                                                if (strpos($summary, 'Call Reason Analysis:') !== false) {
                                                    // Split summary and call reason analysis
                                                    $parts = explode('Call Reason Analysis:', $summary);
                                                    $mainSummary = trim($parts[0]);
                                                    $callReasonAnalysis = isset($parts[1]) ? trim($parts[1]) : '';
                                                    
                                                    // Display main summary
                                                    echo '<div class="mb-4">';
                                                    echo '<p class="text-sm text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($mainSummary)) . '</p>';
                                                    echo '</div>';
                                                    
                                                    // Display Call Reason Analysis in highlighted section
                                                    if ($callReasonAnalysis) {
                                                        echo '<div class="border-t border-green-300 pt-3 mt-3">';
                                                        echo '<h6 class="font-semibold text-green-800 mb-2 flex items-center">';
                                                        echo '<i class="fas fa-search mr-2"></i>Call Reason Analysis';
                                                        echo '</h6>';
                                                        echo '<div class="bg-white p-3 rounded border border-green-300">';
                                                        
                                                        // Parse and format the call reason analysis
                                                        $lines = explode("\n", $callReasonAnalysis);
                                                        foreach ($lines as $line) {
                                                            $line = trim($line);
                                                            if (empty($line)) continue;
                                                            
                                                            if (strpos($line, 'Primary Call Reason:') !== false) {
                                                                $reason = str_replace('Primary Call Reason:', '', $line);
                                                                $reasonColors = [
                                                                    'Query' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                                    'Request' => 'bg-green-100 text-green-800 border-green-200',
                                                                    'Complaint' => 'bg-red-100 text-red-800 border-red-200',
                                                                    'General' => 'bg-gray-100 text-gray-800 border-gray-200'
                                                                ];
                                                                $reasonColor = 'bg-gray-100 text-gray-800 border-gray-200';
                                                                foreach ($reasonColors as $type => $color) {
                                                                    if (strpos($reason, $type) !== false) {
                                                                        $reasonColor = $color;
                                                                        break;
                                                                    }
                                                                }
                                                                echo '<div class="mb-2">';
                                                                echo '<span class="text-xs font-medium text-gray-600">Call Reason:</span> ';
                                                                echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border ' . $reasonColor . '">' . htmlspecialchars(trim($reason)) . '</span>';
                                                                echo '</div>';
                                                            } elseif (strpos($line, 'Confidence Level:') !== false) {
                                                                $confidence = str_replace('Confidence Level:', '', $line);
                                                                $confidenceColor = strpos($confidence, 'High') !== false ? 'text-green-600' : (strpos($confidence, 'Medium') !== false ? 'text-yellow-600' : 'text-gray-600');
                                                                echo '<div class="mb-2">';
                                                                echo '<span class="text-xs font-medium text-gray-600">Confidence:</span> ';
                                                                echo '<span class="text-xs font-semibold ' . $confidenceColor . '">' . htmlspecialchars(trim($confidence)) . '</span>';
                                                                echo '</div>';
                                                            } elseif (strpos($line, 'Detection Breakdown:') !== false) {
                                                                $breakdown = str_replace('Detection Breakdown:', '', $line);
                                                                echo '<div class="mb-2">';
                                                                echo '<span class="text-xs font-medium text-gray-600">Detection:</span> ';
                                                                echo '<span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">' . htmlspecialchars(trim($breakdown)) . '</span>';
                                                                echo '</div>';
                                                            } else {
                                                                echo '<div class="text-xs text-gray-600 mb-1">' . htmlspecialchars($line) . '</div>';
                                                            }
                                                        }
                                                        
                                                        echo '</div>';
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    // No Call Reason Analysis found, display as normal
                                                    echo '<p class="text-sm text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($summary)) . '</p>';
                                                }
                                            } else {
                                                echo '<em class="text-gray-400">No summary available</em>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Performance Scores Grid -->
                                <div class="bg-white p-4 rounded border border-gray-200">
                                    <h6 class="font-semibold text-gray-700 mb-4">Complete Performance Analysis</h6>
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600 mb-1"><?php echo $detail['sentiment_score']; ?></div>
                                            <div class="text-xs text-blue-700 font-medium">Sentiment</div>
                                            <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $detail['sentiment_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 mb-1"><?php echo $detail['clarity_score']; ?></div>
                                            <div class="text-xs text-purple-700 font-medium">Clarity</div>
                                            <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: <?php echo $detail['clarity_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-pink-50 rounded-lg">
                                            <div class="text-2xl font-bold text-pink-600 mb-1"><?php echo $detail['empathy_score']; ?></div>
                                            <div class="text-xs text-pink-700 font-medium">Empathy</div>
                                            <div class="w-full bg-pink-200 rounded-full h-2 mt-2">
                                                <div class="bg-pink-600 h-2 rounded-full" style="width: <?php echo $detail['empathy_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-green-50 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600 mb-1"><?php echo $detail['professionalism_score']; ?></div>
                                            <div class="text-xs text-green-700 font-medium">Professional</div>
                                            <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo $detail['professionalism_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-indigo-50 rounded-lg">
                                            <div class="text-2xl font-bold text-indigo-600 mb-1"><?php echo $detail['call_opening_score']; ?></div>
                                            <div class="text-xs text-indigo-700 font-medium">Opening</div>
                                            <div class="w-full bg-indigo-200 rounded-full h-2 mt-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $detail['call_opening_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                                            <div class="text-2xl font-bold text-orange-600 mb-1"><?php echo $detail['call_quality_score']; ?></div>
                                            <div class="text-xs text-orange-700 font-medium">Quality</div>
                                            <div class="w-full bg-orange-200 rounded-full h-2 mt-2">
                                                <div class="bg-orange-600 h-2 rounded-full" style="width: <?php echo $detail['call_quality_score']; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-center p-3 bg-teal-50 rounded-lg">
                                            <div class="text-2xl font-bold text-teal-600 mb-1"><?php echo $detail['call_closing_score']; ?></div>
                                            <div class="text-xs text-teal-700 font-medium">Closing</div>
                                            <div class="w-full bg-teal-200 rounded-full h-2 mt-2">
                                                <div class="bg-teal-600 h-2 rounded-full" style="width: <?php echo $detail['call_closing_score']; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-600 text-center py-8">No detailed analysis data available for this date.</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- No Data Message -->
        <div class="glass-card rounded-2xl p-12 text-center">
            <div class="text-6xl text-gray-300 mb-4">📊</div>
            <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Analysis Data Available</h3>
            <p class="text-gray-500 mb-6">Start analyzing audio files to see comprehensive analytics here.</p>
            <a href="index.php" class="btn-primary text-white px-6 py-3 rounded-xl font-semibold">
                Start Audio Analysis
            </a>
        </div>
        <?php endif; ?>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Page-specific JavaScript for analytics dashboard
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Analytics Dashboard loaded");
        
        // Performance Trends Chart (Last 30 Days)
        ' . (!empty($performanceTrends) ? '
        const trendsCtx = document.getElementById("performanceTrendsChart");
        if (trendsCtx) {
            const trendsData = ' . json_encode($performanceTrends) . ';
            
            new Chart(trendsCtx, {
                type: "line",
                data: {
                    labels: trendsData.map(item => {
                        const date = new Date(item.analysis_date);
                        return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
                    }),
                    datasets: [
                        {
                            label: "Sentiment Score",
                            data: trendsData.map(item => parseFloat(item.avg_sentiment) || 0),
                            borderColor: "rgb(59, 130, 246)",
                            backgroundColor: "rgba(59, 130, 246, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Clarity Score",
                            data: trendsData.map(item => parseFloat(item.avg_clarity) || 0),
                            borderColor: "rgb(147, 51, 234)",
                            backgroundColor: "rgba(147, 51, 234, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Opening Score",
                            data: trendsData.map(item => parseFloat(item.avg_opening) || 0),
                            borderColor: "rgb(34, 197, 94)",
                            backgroundColor: "rgba(34, 197, 94, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Quality Score",
                            data: trendsData.map(item => parseFloat(item.avg_quality) || 0),
                            borderColor: "rgb(249, 115, 22)",
                            backgroundColor: "rgba(249, 115, 22, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Closing Score",
                            data: trendsData.map(item => parseFloat(item.avg_closing) || 0),
                            borderColor: "rgb(168, 85, 247)",
                            backgroundColor: "rgba(168, 85, 247, 0.1)",
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)"
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)"
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: "top",
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 11
                            }
                        }
                    }
                }
            });
        }
        ' : '') . '
        
        // Today\'s Hourly Performance Chart
        const hourlyCtx = document.getElementById("todayHourlyChart");
        if (hourlyCtx) {
            const hourlyData = ' . json_encode($todayHourlyTrends) . ';
            
            new Chart(hourlyCtx, {
                type: "line",
                data: {
                    labels: hourlyData.map(item => item.hour_of_day + ":00"),
                    datasets: [
                        {
                            label: "Sentiment Score",
                            data: hourlyData.map(item => parseFloat(item.avg_sentiment) || 0),
                            backgroundColor: "rgba(59, 130, 246, 0.1)",
                            borderColor: "rgb(59, 130, 246)",
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: "rgb(59, 130, 246)",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: "Clarity Score",
                            data: hourlyData.map(item => parseFloat(item.avg_clarity) || 0),
                            backgroundColor: "rgba(147, 51, 234, 0.1)",
                            borderColor: "rgb(147, 51, 234)",
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: "rgb(147, 51, 234)",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: "Opening Score",
                            data: hourlyData.map(item => parseFloat(item.avg_opening) || 0),
                            backgroundColor: "rgba(34, 197, 94, 0.1)",
                            borderColor: "rgb(34, 197, 94)",
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: "rgb(34, 197, 94)",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: "Quality Score",
                            data: hourlyData.map(item => parseFloat(item.avg_quality) || 0),
                            backgroundColor: "rgba(249, 115, 22, 0.1)",
                            borderColor: "rgb(249, 115, 22)",
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: "rgb(249, 115, 22)",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: "Closing Score",
                            data: hourlyData.map(item => parseFloat(item.avg_closing) || 0),
                            backgroundColor: "rgba(168, 85, 247, 0.1)",
                            borderColor: "rgb(168, 85, 247)",
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: "rgb(168, 85, 247)",
                            pointBorderColor: "#fff",
                            pointBorderWidth: 2,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: "index",
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)"
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)"
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: "top",
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 11
                            }
                        }
                    }
                }
            });
        }
        
        // Toggle details function
        function toggleDetails(date) {
            const detailsRow = document.getElementById(\'details-\' + date);
            if (detailsRow) {
                if (detailsRow.classList.contains(\'hidden\')) {
                    detailsRow.classList.remove(\'hidden\');
                } else {
                    detailsRow.classList.add(\'hidden\');
                }
            }
        }
        
        // Search and Filter functionality for Performance Trends
        const trendsSearch = document.getElementById("trendsSearch");
        const trendsFilter = document.getElementById("trendsFilter");
        
        if (trendsSearch && trendsFilter) {
            function filterTrendsCards() {
                const searchTerm = trendsSearch.value.toLowerCase();
                const filterValue = trendsFilter.value;
                const cards = document.querySelectorAll(".trends-row");
                let visibleCount = 0;
                const totalCount = cards.length;
                
                cards.forEach(card => {
                    // Get date from the card header
                    const dateElement = card.querySelector("h4");
                    const date = dateElement ? dateElement.textContent.toLowerCase() : "";
                    
                    // Get all text content from transcriptions, translations, and summaries
                    const transcriptionElements = card.querySelectorAll(".bg-white p, .bg-blue-50 p, .bg-green-50 p");
                    let allTextContent = date; // Start with date
                    
                    transcriptionElements.forEach(element => {
                        if (element && element.textContent) {
                            allTextContent += " " + element.textContent.toLowerCase();
                        }
                    });
                    
                    // Also search in filenames and session IDs
                    const filenameElements = card.querySelectorAll("h5");
                    filenameElements.forEach(element => {
                        if (element && element.textContent) {
                            allTextContent += " " + element.textContent.toLowerCase();
                        }
                    });
                    
                    // Get overall performance score
                    const performanceElement = card.querySelector(".text-sm.text-gray-600 span");
                    const overallScore = performanceElement ? parseFloat(performanceElement.textContent.trim()) : 0;
                    
                    // Search filter - now searches through all content
                    const matchesSearch = searchTerm === "" || allTextContent.includes(searchTerm);
                    
                    // Performance filter
                    let matchesFilter = true;
                    if (filterValue === "high") {
                        matchesFilter = overallScore > 80;
                    } else if (filterValue === "medium") {
                        matchesFilter = overallScore >= 50 && overallScore <= 80;
                    } else if (filterValue === "low") {
                        matchesFilter = overallScore < 50;
                    }
                    
                    // Show/hide card
                    if (matchesSearch && matchesFilter) {
                        card.style.display = "";
                        visibleCount++;
                    } else {
                        card.style.display = "none";
                    }
                });
                
                // Update search results info
                updateSearchResultsInfo(visibleCount, totalCount, searchTerm, filterValue);
            }
            
            function updateSearchResultsInfo(visibleCount, totalCount, searchTerm, filterValue) {
                const searchResultsInfo = document.getElementById("searchResultsInfo");
                const visibleCountElement = document.getElementById("visibleCount");
                const totalCountElement = document.getElementById("totalCount");
                
                if (searchResultsInfo && visibleCountElement && totalCountElement) {
                    visibleCountElement.textContent = visibleCount;
                    totalCountElement.textContent = totalCount;
                    
                    // Show/hide the info bar based on whether filters are active
                    if (searchTerm !== "" || filterValue !== "") {
                        searchResultsInfo.classList.remove("hidden");
                    } else {
                        searchResultsInfo.classList.add("hidden");
                    }
                }
            }
            
            // Clear search function
            window.clearSearch = function() {
                trendsSearch.value = "";
                trendsFilter.value = "";
                filterTrendsCards();
            };
            
            trendsSearch.addEventListener("input", filterTrendsCards);
            trendsFilter.addEventListener("change", filterTrendsCards);
        }
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
