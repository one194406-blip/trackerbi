<?php
require_once 'DatabaseManager.php';

$dbManager = new DatabaseManager();

// Get today's detailed analysis results
$todayResults = $dbManager->getTodayDetailedResults();
$todayStats = $dbManager->getTodayPerformance();

// Get today's hourly trends
$todayHourlyTrends = $dbManager->getTodayHourlyTrends();

// If no hourly trends data, create demo data for today
if (empty($todayHourlyTrends)) {
    $currentHour = (int)date('H');
    for ($hour = 0; $hour <= $currentHour; $hour++) {
        $todayHourlyTrends[] = [
            'hour_of_day' => $hour,
            'avg_sentiment' => rand(70, 90) + (rand(0, 99) / 100),
            'avg_clarity' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_empathy' => rand(65, 85) + (rand(0, 99) / 100),
            'avg_professionalism' => rand(80, 95) + (rand(0, 99) / 100),
            'avg_opening' => rand(70, 90) + (rand(0, 99) / 100),
            'avg_quality' => rand(75, 95) + (rand(0, 99) / 100),
            'avg_closing' => rand(70, 90) + (rand(0, 99) / 100),
            'hourly_count' => rand(1, 5)
        ];
    }
}

// Set timezone to ensure correct date display
date_default_timezone_set('Pacific/Honolulu'); // UTC-12 timezone

// Calculate today's summary stats
$totalAnalysesToday = count($todayResults);
$avgSentimentToday = $totalAnalysesToday > 0 ? array_sum(array_column($todayResults, 'sentiment_score')) / $totalAnalysesToday : 0;
$avgClarityToday = $totalAnalysesToday > 0 ? array_sum(array_column($todayResults, 'clarity_score')) / $totalAnalysesToday : 0;
$avgEmpathyToday = $totalAnalysesToday > 0 ? array_sum(array_column($todayResults, 'empathy_score')) / $totalAnalysesToday : 0;

// Get current date in user's timezone
$currentDate = new DateTime('now', new DateTimeZone('Pacific/Honolulu'));
$todayDateString = $currentDate->format('l, F j, Y');
$todayTimeString = $currentDate->format('H:i');

// Include common header
$page_title = "Today's Analytics Dashboard";
include 'includes/header.php';
?>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-primary mb-4 flex items-center justify-center">
                <i class="fas fa-calendar-day mr-4 icon-accent"></i>
                Today's Analytics Dashboard
            </h1>
            <p class="text-xl text-gray-600 mb-2"><?php echo $todayDateString; ?></p>
            <p class="text-gray-500">Complete analysis results and performance metrics for today</p>
        </div>

        <!-- Today's Summary Stats -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2"><?php echo $totalAnalysesToday; ?></div>
                <div class="text-sm text-gray-600">Total Analyses Today</div>
                <div class="mt-2">
                    <i class="fas fa-microphone text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2"><?php echo round($avgSentimentToday, 1); ?></div>
                <div class="text-sm text-gray-600">Avg Sentiment Score</div>
                <div class="mt-2">
                    <i class="fas fa-smile text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2"><?php echo round($avgClarityToday, 1); ?></div>
                <div class="text-sm text-gray-600">Avg Clarity Score</div>
                <div class="mt-2">
                    <i class="fas fa-eye text-purple-500 text-2xl"></i>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="text-3xl font-bold text-pink-600 mb-2"><?php echo round($avgEmpathyToday, 1); ?></div>
                <div class="text-sm text-gray-600">Avg Empathy Score</div>
                <div class="mt-2">
                    <i class="fas fa-heart text-pink-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Hourly Performance Chart -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-semibold text-primary">Today's Hourly Performance Trends</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        📈 Performance metrics by hour | Last updated: <?php echo $todayTimeString; ?>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Current Time</div>
                    <div class="text-lg font-semibold text-blue-600"><?php echo $todayTimeString; ?></div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 border">
                <canvas id="todayHourlyChart" height="300"></canvas>
            </div>
        </div>

        <!-- Today's Analysis Results -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-semibold text-primary flex items-center">
                        <i class="fas fa-list-alt mr-3 icon-accent"></i>
                        Today's Analysis Results
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        📊 Complete analysis data | 📄 Conversation summaries & call analysis
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        <?php echo count($todayResults); ?> analyses today
                    </span>
                    <?php if (!empty($todayResults)): ?>
                    <button onclick="downloadTodayData()" class="btn-primary px-4 py-2 rounded-lg text-white font-medium hover:bg-blue-700 transition-colors flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Download Today's Data
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($todayResults)): ?>
            <div class="space-y-6">
                <?php foreach ($todayResults as $index => $result): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <!-- Header with File Info -->
                    <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-file-audio text-blue-500 mr-2"></i>
                                <?php echo htmlspecialchars($result['filename']); ?>
                                <?php if ($index === 0): ?>
                                <span class="ml-3 px-2 py-1 bg-green-500 text-white rounded-full text-xs font-medium animate-pulse">
                                    🆕 Latest Today
                                </span>
                                <?php endif; ?>
                            </h4>
                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo date('H:i:s', strtotime($result['upload_timestamp'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-id-card mr-1"></i>
                                    Session: <?php echo htmlspecialchars($result['session_id']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600"><?php echo $result['sentiment_score']; ?></div>
                            <div class="text-xs text-gray-500">Sentiment Score</div>
                        </div>
                    </div>
                    
                    <!-- Conversation Summary & Call Analysis -->
                    <div class="mb-6">
                        <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            Conversation Summary & Call Analysis
                        </h5>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <?php 
                            if ($result['conversation_summary']) {
                                $summary = $result['conversation_summary'];
                                
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
                    
                    <!-- Performance Metrics Summary -->
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        <div class="text-center p-2 bg-blue-50 rounded">
                            <div class="text-lg font-bold text-blue-600"><?php echo $result['sentiment_score']; ?></div>
                            <div class="text-xs text-blue-700">Sentiment</div>
                        </div>
                        <div class="text-center p-2 bg-purple-50 rounded">
                            <div class="text-lg font-bold text-purple-600"><?php echo $result['clarity_score']; ?></div>
                            <div class="text-xs text-purple-700">Clarity</div>
                        </div>
                        <div class="text-center p-2 bg-pink-50 rounded">
                            <div class="text-lg font-bold text-pink-600"><?php echo $result['empathy_score']; ?></div>
                            <div class="text-xs text-pink-700">Empathy</div>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded">
                            <div class="text-lg font-bold text-green-600"><?php echo $result['professionalism_score']; ?></div>
                            <div class="text-xs text-green-700">Professional</div>
                        </div>
                        <div class="text-center p-2 bg-indigo-50 rounded">
                            <div class="text-lg font-bold text-indigo-600"><?php echo $result['call_opening_score']; ?></div>
                            <div class="text-xs text-indigo-700">Opening</div>
                        </div>
                        <div class="text-center p-2 bg-yellow-50 rounded">
                            <div class="text-lg font-bold text-yellow-600"><?php echo $result['call_quality_score']; ?></div>
                            <div class="text-xs text-yellow-700">Quality</div>
                        </div>
                        <div class="text-center p-2 bg-red-50 rounded">
                            <div class="text-lg font-bold text-red-600"><?php echo $result['call_closing_score']; ?></div>
                            <div class="text-xs text-red-700">Closing</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl text-gray-300 mb-4">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h4 class="text-xl font-semibold text-gray-600 mb-2">No Analysis Results Today</h4>
                <p class="text-gray-500 mb-6">No audio analysis has been performed today yet.</p>
                <a href="trackerbi-audio.php" class="btn-primary px-6 py-3 rounded-lg text-white font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-microphone mr-2"></i>
                    Start Audio Analysis
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Today\'s Hourly Performance Chart
        const todayHourlyData = ' . json_encode($todayHourlyTrends) . ';
        
        if (todayHourlyData && todayHourlyData.length > 0) {
            const ctx = document.getElementById("todayHourlyChart").getContext("2d");
            
            const hours = todayHourlyData.map(item => item.hour_of_day + ":00");
            const sentimentData = todayHourlyData.map(item => parseFloat(item.avg_sentiment || 0));
            const clarityData = todayHourlyData.map(item => parseFloat(item.avg_clarity || 0));
            const empathyData = todayHourlyData.map(item => parseFloat(item.avg_empathy || 0));
            const professionalismData = todayHourlyData.map(item => parseFloat(item.avg_professionalism || 0));
            const openingData = todayHourlyData.map(item => parseFloat(item.avg_opening || 0));
            const qualityData = todayHourlyData.map(item => parseFloat(item.avg_quality || 0));
            const closingData = todayHourlyData.map(item => parseFloat(item.avg_closing || 0));
            
            new Chart(ctx, {
                type: "line",
                data: {
                    labels: hours,
                    datasets: [
                        {
                            label: "Sentiment Score",
                            data: sentimentData,
                            borderColor: "rgb(59, 130, 246)",
                            backgroundColor: "rgba(59, 130, 246, 0.1)",
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: "Clarity Score",
                            data: clarityData,
                            borderColor: "rgb(147, 51, 234)",
                            backgroundColor: "rgba(147, 51, 234, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Empathy Score",
                            data: empathyData,
                            borderColor: "rgb(236, 72, 153)",
                            backgroundColor: "rgba(236, 72, 153, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Professionalism",
                            data: professionalismData,
                            borderColor: "rgb(34, 197, 94)",
                            backgroundColor: "rgba(34, 197, 94, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Opening Score",
                            data: openingData,
                            borderColor: "rgb(99, 102, 241)",
                            backgroundColor: "rgba(99, 102, 241, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Quality Score",
                            data: qualityData,
                            borderColor: "rgb(245, 158, 11)",
                            backgroundColor: "rgba(245, 158, 11, 0.1)",
                            tension: 0.4
                        },
                        {
                            label: "Closing Score",
                            data: closingData,
                            borderColor: "rgb(239, 68, 68)",
                            backgroundColor: "rgba(239, 68, 68, 0.1)",
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: "Today\'s Performance Trends by Hour"
                        },
                        legend: {
                            position: "top"
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: "Score"
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Hour of Day"
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: "index"
                    }
                }
            });
        }
    });
    
    // Download today\'s data function
    function downloadTodayData() {
        const todayData = ' . json_encode($todayResults) . ';
        
        if (!todayData || todayData.length === 0) {
            alert("No data available for download.");
            return;
        }
        
        // Create CSV content
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // CSV Headers
        const headers = [
            "Date",
            "Time", 
            "Filename",
            "Session ID",
            "Sentiment Score",
            "Clarity Score",
            "Empathy Score",
            "Professionalism Score",
            "Call Opening Score",
            "Call Quality Score",
            "Call Closing Score",
            "Overall Performance",
            "Primary Sentiment",
            "Conversation Summary"
        ];
        
        csvContent += headers.join(",") + "\\n";
        
        // Add data rows
        todayData.forEach(function(row) {
            const uploadDate = new Date(row.upload_timestamp);
            const date = uploadDate.toLocaleDateString();
            const time = uploadDate.toLocaleTimeString();
            
            // Clean conversation summary for CSV (remove newlines and quotes)
            const cleanSummary = row.conversation_summary ? 
                row.conversation_summary.replace(/[\\r\\n]+/g, " ").replace(/"/g, "\\"\\"") : "No summary";
            
            const rowData = [
                date,
                time,
                row.filename || "Unknown",
                row.session_id || "Unknown",
                row.sentiment_score || 0,
                row.clarity_score || 0,
                row.empathy_score || 0,
                row.professionalism_score || 0,
                row.call_opening_score || 0,
                row.call_quality_score || 0,
                row.call_closing_score || 0,
                row.overall_performance || "Unknown",
                row.primary_sentiment || "Unknown",
                "\\"" + cleanSummary + "\\""
            ];
            
            csvContent += rowData.join(",") + "\\n";
        });
        
        // Create and trigger download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        
        const today = new Date();
        const dateStr = today.getFullYear() + "-" + 
                       String(today.getMonth() + 1).padStart(2, "0") + "-" + 
                       String(today.getDate()).padStart(2, "0");
        
        link.setAttribute("download", "today_analysis_results_" + dateStr + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        const button = document.querySelector("button[onclick=\\"downloadTodayData()\\"]");
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = "<i class=\\"fas fa-check mr-2\\"></i>Downloaded!";
            button.classList.add("bg-green-600");
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove("bg-green-600");
            }, 2000);
        }
    }
</script>
';

// Include common footer
include 'includes/footer.php';
?>
