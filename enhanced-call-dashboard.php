<?php
/**
 * Enhanced Call Dashboard - Comprehensive Analytics with All API Attributes
 * Utilizes all available API data for meaningful insights and analytics
 */

require_once 'config.php';

// API endpoint
$apiEndpoint = 'https://commodiously-appositional-yung.ngrok-free.dev/api.php';
$apiData = null;
$apiError = null;

// Fetch API data
try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'method' => 'GET',
            'header' => [
                'User-Agent: TrackerBI Enhanced Dashboard',
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = @file_get_contents($apiEndpoint, false, $context);
    
    if ($response !== false) {
        $apiData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $apiError = 'Invalid JSON: ' . json_last_error_msg();
            $apiData = null;
        }
    } else {
        $apiError = 'Failed to connect to API';
    }
} catch (Exception $e) {
    $apiError = 'Error: ' . $e->getMessage();
}

// Process and analyze data
$analytics = [
    'total_calls' => 0,
    'completed_calls' => 0,
    'abandoned_calls' => 0,
    'avg_talk_time' => 0,
    'avg_wait_time' => 0,
    'avg_hold_time' => 0,
    'total_bill_seconds' => 0,
    'agents' => [],
    'processes' => [],
    'dispositions' => [],
    'hourly_distribution' => [],
    'call_outcomes' => [],
    'recording_stats' => ['with_recording' => 0, 'without_recording' => 0],
    'transfer_stats' => ['transferred' => 0, 'not_transferred' => 0]
];

if ($apiData && is_array($apiData)) {
    foreach ($apiData as $call) {
        $analytics['total_calls']++;
        
        // Call outcomes (exclude 'chanunaval')
        $status = strtoupper($call['status'] ?? '');
        if ($status !== 'CHANUNAVAL') {
            $analytics['call_outcomes'][$status] = ($analytics['call_outcomes'][$status] ?? 0) + 1;
        }
        
        if ($status === 'COMPLETED') {
            $analytics['completed_calls']++;
        } elseif ($status === 'ABANDONED') {
            $analytics['abandoned_calls']++;
        }
        
        // Parse call timer JSON
        $timerData = json_decode($call['call_timer_json'] ?? '{}', true);
        if ($timerData) {
            $analytics['total_bill_seconds'] += intval($timerData['bill_sec'] ?? 0);
            $analytics['avg_talk_time'] += intval($timerData['talk_sec'] ?? 0);
            $analytics['avg_wait_time'] += intval($timerData['wait_sec'] ?? 0);
            $analytics['avg_hold_time'] += intval($timerData['hold_sec'] ?? 0);
        }
        
        // Agent analysis
        $agentId = $call['agent_id'] ?? 'Unknown';
        if (!isset($analytics['agents'][$agentId])) {
            $analytics['agents'][$agentId] = [
                'total_calls' => 0,
                'completed' => 0,
                'abandoned' => 0,
                'total_talk_time' => 0,
                'avg_performance' => 0
            ];
        }
        $analytics['agents'][$agentId]['total_calls']++;
        if ($status === 'COMPLETED') $analytics['agents'][$agentId]['completed']++;
        if ($status === 'ABANDONED') $analytics['agents'][$agentId]['abandoned']++;
        if ($timerData) $analytics['agents'][$agentId]['total_talk_time'] += intval($timerData['talk_sec'] ?? 0);
        
        // Process analysis
        $process = $call['process_name'] ?? 'Unknown';
        $analytics['processes'][$process] = ($analytics['processes'][$process] ?? 0) + 1;
        
        // Disposition analysis
        $disposition = $call['disposition'] ?? 'Unknown';
        if ($disposition) {
            $analytics['dispositions'][$disposition] = ($analytics['dispositions'][$disposition] ?? 0) + 1;
        }
        
        // Hourly distribution
        $hour = date('H', strtotime($call['call_start_time'] ?? '00:00:00'));
        $analytics['hourly_distribution'][$hour] = ($analytics['hourly_distribution'][$hour] ?? 0) + 1;
        
        // Recording stats
        if (intval($call['recording_flag'] ?? 0) === 1) {
            $analytics['recording_stats']['with_recording']++;
        } else {
            $analytics['recording_stats']['without_recording']++;
        }
        
        // Transfer stats
        if (intval($call['is_transfered_call'] ?? 0) === 1) {
            $analytics['transfer_stats']['transferred']++;
        } else {
            $analytics['transfer_stats']['not_transferred']++;
        }
    }
    
    // Calculate averages
    if ($analytics['total_calls'] > 0) {
        $analytics['avg_talk_time'] = round($analytics['avg_talk_time'] / $analytics['total_calls'], 2);
        $analytics['avg_wait_time'] = round($analytics['avg_wait_time'] / $analytics['total_calls'], 2);
        $analytics['avg_hold_time'] = round($analytics['avg_hold_time'] / $analytics['total_calls'], 2);
        $analytics['success_rate'] = round(($analytics['completed_calls'] / $analytics['total_calls']) * 100, 1);
        $analytics['abandon_rate'] = round(($analytics['abandoned_calls'] / $analytics['total_calls']) * 100, 1);
    }
    
    // Calculate agent performance scores
    foreach ($analytics['agents'] as $agentId => &$agent) {
        if ($agent['total_calls'] > 0) {
            $agent['success_rate'] = round(($agent['completed'] / $agent['total_calls']) * 100, 1);
            $agent['avg_talk_time'] = round($agent['total_talk_time'] / $agent['total_calls'], 2);
            $agent['avg_performance'] = $agent['success_rate']; // Simplified performance score
        }
    }
}

$page_title = 'Enhanced Call Analytics Dashboard';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-8">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-white text-center mb-4">
                <i class="fas fa-chart-line mr-3"></i>
                Enhanced Call Analytics Dashboard
            </h1>
            <div class="text-center">
                <span class="px-4 py-2 <?php echo $apiData ? 'bg-green-500' : 'bg-red-500'; ?> text-white rounded-full text-sm font-semibold">
                    <?php echo $apiData ? '🟢 Live Data Connected' : '🔴 API Connection Error'; ?>
                </span>
                <div class="mt-2 text-sm text-white/80">
                    Last Updated: <?php echo date('Y-m-d H:i:s'); ?> | 
                    Total Records: <?php echo $analytics['total_calls']; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Executive Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Calls -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Calls</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo number_format($analytics['total_calls']); ?></p>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-phone text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-500">All call records</span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-phone-alt text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Success Rate</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $analytics['success_rate'] ?? 0; ?>%</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: <?php echo $analytics['success_rate'] ?? 0; ?>%"></div>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Talk Time -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg Talk Time</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo gmdate("i:s", $analytics['avg_talk_time'] ?? 0); ?></p>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-clock text-purple-500 mr-2"></i>
                            <span class="text-sm text-gray-500">Per call average</span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-stopwatch text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Agents -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Agents</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo count($analytics['agents']); ?></p>
                        <div class="flex items-center mt-2">
                            <i class="fas fa-users text-orange-500 mr-2"></i>
                            <span class="text-sm text-gray-500">Handling calls</span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Call Timing Analysis -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-indigo-600 mr-3"></i>
                    Call Timing Analysis
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Average Talk Time</p>
                            <p class="text-2xl font-bold text-blue-900"><?php echo gmdate("i:s", $analytics['avg_talk_time'] ?? 0); ?></p>
                        </div>
                        <div class="text-blue-600">
                            <i class="fas fa-microphone text-2xl"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Average Wait Time</p>
                            <p class="text-2xl font-bold text-yellow-900"><?php echo gmdate("i:s", $analytics['avg_wait_time'] ?? 0); ?></p>
                        </div>
                        <div class="text-yellow-600">
                            <i class="fas fa-hourglass-half text-2xl"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-red-800">Average Hold Time</p>
                            <p class="text-2xl font-bold text-red-900"><?php echo gmdate("i:s", $analytics['avg_hold_time'] ?? 0); ?></p>
                        </div>
                        <div class="text-red-600">
                            <i class="fas fa-pause text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call Outcomes Distribution -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-green-600 mr-3"></i>
                    Call Outcomes Distribution
                </h3>
                <div class="h-64">
                    <canvas id="outcomeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Agent Performance Matrix -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-users text-purple-600 mr-3"></i>
                Agent Performance Matrix
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Calls</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Success Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Talk Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        // Sort agents by performance
                        uasort($analytics['agents'], function($a, $b) {
                            return $b['avg_performance'] <=> $a['avg_performance'];
                        });
                        
                        foreach ($analytics['agents'] as $agentId => $agent): 
                            $performanceClass = $agent['success_rate'] >= 80 ? 'text-green-600' : ($agent['success_rate'] >= 60 ? 'text-yellow-600' : 'text-red-600');
                            $performanceBg = $agent['success_rate'] >= 80 ? 'bg-green-100' : ($agent['success_rate'] >= 60 ? 'bg-yellow-100' : 'bg-red-100');
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($agentId); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $agent['total_calls']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $agent['completed']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $performanceBg . ' ' . $performanceClass; ?>">
                                    <?php echo $agent['success_rate']; ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo gmdate("i:s", $agent['avg_talk_time']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="<?php echo $agent['success_rate'] >= 80 ? 'bg-green-500' : ($agent['success_rate'] >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?> h-2 rounded-full" style="width: <?php echo $agent['success_rate']; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Hourly Call Distribution -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Hourly Distribution
                </h3>
                <div class="h-48">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>

            <!-- Recording Statistics -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-record-vinyl text-red-600 mr-2"></i>
                    Recording Stats
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">With Recording</span>
                        <span class="font-bold text-green-600"><?php echo $analytics['recording_stats']['with_recording']; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Without Recording</span>
                        <span class="font-bold text-red-600"><?php echo $analytics['recording_stats']['without_recording']; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <?php 
                        $totalRecordings = $analytics['recording_stats']['with_recording'] + $analytics['recording_stats']['without_recording'];
                        $recordingPercentage = $totalRecordings > 0 ? ($analytics['recording_stats']['with_recording'] / $totalRecordings) * 100 : 0;
                        ?>
                        <div class="bg-green-500 h-3 rounded-full" style="width: <?php echo $recordingPercentage; ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-500 text-center"><?php echo round($recordingPercentage, 1); ?>% calls recorded</p>
                </div>
            </div>

            <!-- Process Distribution -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cogs text-purple-600 mr-2"></i>
                    Process Distribution
                </h3>
                <div class="space-y-3">
                    <?php foreach ($analytics['processes'] as $process => $count): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 truncate"><?php echo htmlspecialchars($process); ?></span>
                        <span class="font-bold text-purple-600"><?php echo $count; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Call Outcomes Chart
const outcomeCtx = document.getElementById('outcomeChart').getContext('2d');
new Chart(outcomeCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($analytics['call_outcomes'])); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($analytics['call_outcomes'])); ?>,
            backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#06B6D4'],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Hourly Distribution Chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($analytics['hourly_distribution'])); ?>,
        datasets: [{
            label: 'Calls per Hour',
            data: <?php echo json_encode(array_values($analytics['hourly_distribution'])); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Auto-refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);
</script>

<?php include 'includes/footer.php'; ?>
