<?php
/**
 * Enhanced Call Dashboard - Clean Design
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

// Process and analyze data for meaningful insights
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
    'transfer_stats' => ['transferred' => 0, 'not_transferred' => 0],
    'queue_performance' => [],
    'call_duration_ranges' => ['0-30s' => 0, '30s-2m' => 0, '2m-5m' => 0, '5m+' => 0]
];

// Process API data if available
if ($apiData && is_array($apiData)) {
    foreach ($apiData as $call) {
        $analytics['total_calls']++;
        
        // Call outcomes analysis
        $status = strtoupper($call['status'] ?? '');
        $analytics['call_outcomes'][$status] = ($analytics['call_outcomes'][$status] ?? 0) + 1;
        
        if ($status === 'COMPLETED') {
            $analytics['completed_calls']++;
        } elseif ($status === 'ABANDONED') {
            $analytics['abandoned_calls']++;
        }
        
        // Parse call timer JSON for detailed timing analysis
        $timerData = json_decode($call['call_timer_json'] ?? '{}', true);
        if ($timerData) {
            $billSec = intval($timerData['bill_sec'] ?? 0);
            $talkSec = intval($timerData['talk_sec'] ?? 0);
            $waitSec = intval($timerData['wait_sec'] ?? 0);
            $holdSec = intval($timerData['hold_sec'] ?? 0);
            
            $analytics['total_bill_seconds'] += $billSec;
            $analytics['avg_talk_time'] += $talkSec;
            $analytics['avg_wait_time'] += $waitSec;
            $analytics['avg_hold_time'] += $holdSec;
        }
        
        // Agent performance analysis
        $agentId = $call['agent_id'] ?? 'Unknown';
        if ($agentId && $agentId !== '') {
            if (!isset($analytics['agents'][$agentId])) {
                $analytics['agents'][$agentId] = [
                    'total_calls' => 0,
                    'completed' => 0,
                    'abandoned' => 0,
                    'total_talk_time' => 0,
                    'total_bill_time' => 0,
                    'avg_performance' => 0
                ];
            }
            $analytics['agents'][$agentId]['total_calls']++;
            if ($status === 'COMPLETED') $analytics['agents'][$agentId]['completed']++;
            if ($status === 'ABANDONED') $analytics['agents'][$agentId]['abandoned']++;
            if ($timerData) {
                $analytics['agents'][$agentId]['total_talk_time'] += intval($timerData['talk_sec'] ?? 0);
                $analytics['agents'][$agentId]['total_bill_time'] += intval($timerData['bill_sec'] ?? 0);
            }
        }
        
        // Process and queue analysis
        $process = $call['process_name'] ?? 'Unknown';
        $analytics['processes'][$process] = ($analytics['processes'][$process] ?? 0) + 1;
        
        // Recording and transfer statistics
        if (intval($call['recording_flag'] ?? 0) === 1) {
            $analytics['recording_stats']['with_recording']++;
        } else {
            $analytics['recording_stats']['without_recording']++;
        }
        
        if (intval($call['is_transfered_call'] ?? 0) === 1) {
            $analytics['transfer_stats']['transferred']++;
        } else {
            $analytics['transfer_stats']['not_transferred']++;
        }
    }
    
    // Calculate averages and rates
    if ($analytics['total_calls'] > 0) {
        $analytics['avg_talk_time'] = round($analytics['avg_talk_time'] / $analytics['total_calls'], 2);
        $analytics['avg_wait_time'] = round($analytics['avg_wait_time'] / $analytics['total_calls'], 2);
        $analytics['avg_hold_time'] = round($analytics['avg_hold_time'] / $analytics['total_calls'], 2);
        $analytics['success_rate'] = round(($analytics['completed_calls'] / $analytics['total_calls']) * 100, 1);
        $analytics['abandon_rate'] = round(($analytics['abandoned_calls'] / $analytics['total_calls']) * 100, 1);
        $analytics['avg_call_duration'] = round($analytics['total_bill_seconds'] / $analytics['total_calls'], 2);
    }
    
    // Calculate agent performance scores
    foreach ($analytics['agents'] as $agentId => &$agent) {
        if ($agent['total_calls'] > 0) {
            $agent['success_rate'] = round(($agent['completed'] / $agent['total_calls']) * 100, 1);
            $agent['avg_talk_time'] = round($agent['total_talk_time'] / $agent['total_calls'], 2);
            $agent['avg_call_duration'] = round($agent['total_bill_time'] / $agent['total_calls'], 2);
            $agent['avg_performance'] = $agent['success_rate'];
        }
    }
}

$page_title = 'Call Dashboard';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Enhanced Page Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-8">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold text-white text-center mb-4">
                <i class="fas fa-chart-line mr-3"></i>
                Call Center Dashboard
            </h1>
            <div class="text-center">
                <span class="px-4 py-2 <?php echo $apiData ? 'bg-green-500' : 'bg-red-500'; ?> text-white rounded-full text-sm font-semibold">
                    <?php echo $apiData ? '🟢 Live Data Connected' : '🔴 API Connection Error'; ?>
                </span>
                <div class="mt-2 text-sm text-white/80">
                    Last Updated: <?php echo date('Y-m-d H:i:s'); ?> | 
                    Total Records: <?php echo $analytics['total_calls']; ?> | 
                    Success Rate: <?php echo $analytics['success_rate'] ?? 0; ?>%
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Clean Executive Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Calls -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-phone text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Calls</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['total_calls']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Success Rate</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $analytics['success_rate'] ?? 0; ?>%</p>
                    </div>
                </div>
            </div>

            <!-- Average Talk Time -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Avg Talk Time</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo gmdate("i:s", $analytics['avg_talk_time'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Agents -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Active Agents</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($analytics['agents']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Call Summary -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">📞 Overall Call Summary</h2>
                <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Answered Calls -->
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-phone text-white"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600"><?php echo $analytics['completed_calls']; ?></p>
                    <p class="text-sm text-gray-600">Answered Calls</p>
                </div>
                
                <!-- Missed Calls -->
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-phone-slash text-white"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-600"><?php echo $analytics['abandoned_calls']; ?></p>
                    <p class="text-sm text-gray-600">Missed Calls</p>
                </div>
                
                <!-- Predictive Calls -->
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600"><?php echo count($analytics['processes']); ?></p>
                    <p class="text-sm text-gray-600">Predictive Calls</p>
                </div>
                
                <!-- Unique Answered Calls -->
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <p class="text-2xl font-bold text-purple-600"><?php echo $analytics['recording_stats']['with_recording']; ?></p>
                    <p class="text-sm text-gray-600">Unique Answered Calls</p>
                </div>
            </div>
        </div>

        <!-- Agent Call Summary -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800">👥 Agent Call Summary</h2>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Active</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total Duration: <?php echo gmdate("H:i:s", $analytics['total_bill_seconds'] ?? 0); ?>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">All Agents</p>
                            <p class="text-sm text-gray-600"><?php echo count($analytics['agents']); ?> agents active</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-green-600"><?php echo gmdate("H:i:s", $analytics['total_bill_seconds'] ?? 0); ?></p>
                        <p class="text-sm text-gray-600">Total Duration</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Agent Performance -->
        <?php if (!empty($analytics['agents'])): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php 
            // Sort agents by performance and limit to top 6
            uasort($analytics['agents'], function($a, $b) {
                return $b['avg_performance'] <=> $a['avg_performance'];
            });
            
            $topAgents = array_slice($analytics['agents'], 0, 6, true);
            foreach ($topAgents as $agentId => $agent): 
                $statusColor = $agent['success_rate'] >= 80 ? 'green' : ($agent['success_rate'] >= 60 ? 'yellow' : 'red');
                $statusBg = $agent['success_rate'] >= 80 ? 'bg-green-50 border-green-200' : ($agent['success_rate'] >= 60 ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200');
            ?>
            <div class="bg-white rounded-xl shadow-sm border <?php echo $statusBg; ?> p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-<?php echo $statusColor; ?>-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-<?php echo $statusColor; ?>-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($agentId); ?></p>
                            <p class="text-sm text-gray-500"><?php echo $agent['total_calls']; ?> calls</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-<?php echo $statusColor; ?>-600"><?php echo $agent['success_rate']; ?>%</p>
                        <p class="text-xs text-gray-500">Success Rate</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Completed:</span>
                        <span class="font-medium"><?php echo $agent['completed']; ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Avg Duration:</span>
                        <span class="font-medium"><?php echo gmdate("i:s", $agent['avg_call_duration']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Call Status Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Leads by State -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Leads by State</h3>
                <div class="space-y-3">
                    <?php if (!empty($analytics['call_outcomes'])): ?>
                        <?php foreach (array_slice($analytics['call_outcomes'], 0, 5) as $outcome => $count): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($outcome); ?></span>
                            <span class="text-sm font-bold text-gray-900"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No data available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Leads by Industry Type -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🏢 Leads by Industry Type</h3>
                <div class="space-y-3">
                    <?php if (!empty($analytics['processes'])): ?>
                        <?php foreach (array_slice($analytics['processes'], 0, 5) as $process => $count): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($process); ?></span>
                            <span class="text-sm font-bold text-gray-900"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Call Status Overview -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Call Status Overview</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objective</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        if ($apiData && is_array($apiData)):
                            foreach (array_slice($apiData, 0, 10) as $call):
                                $status = strtoupper($call['status'] ?? 'UNKNOWN');
                                $statusClass = $status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 
                                              ($status === 'ABANDONED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($call['disposition'] ?? 'OBJECTIVE'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($call['process_name'] ?? 'SUBJECT'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($call['queue_name'] ?? 'ASSIGNED'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($call['agent_id'] ?? 'AGENT'); ?></td>
                        </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <div class="text-lg">No call data available</div>
                                <div class="text-sm">Connect to API to view call records</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    console.log('Clean Call Dashboard loaded');
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        location.reload();
    }, 30000);
</script>

<?php include 'includes/footer.php'; ?>
