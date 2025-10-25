<?php
/**
 * Enhanced Call Dashboard - Comprehensive Analytics with All API Attributes
 * Utilizes all available API data for meaningful insights and analytics
 */

require_once 'config.php';

// Multi-API Configuration for Gemini APIs
class GeminiAPIManager {
    private $apiKeys = [];
    private $currentKeyIndex = 0;
    private $maxRetries = 3;
    
    public function __construct() {
        // Load all 10 Gemini API keys from environment
        for ($i = 1; $i <= 10; $i++) {
            $key = $_ENV["GEMINI_API_KEY_$i"] ?? getenv("GEMINI_API_KEY_$i");
            if ($key) {
                $this->apiKeys[] = [
                    'key' => $key,
                    'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$key",
                    'active' => true,
                    'last_used' => 0,
                    'error_count' => 0,
                    'name' => "Gemini API $i"
                ];
            }
        }
        
        // Fallback demo keys if no env keys found
        if (empty($this->apiKeys)) {
            for ($i = 1; $i <= 10; $i++) {
                $this->apiKeys[] = [
                    'key' => "demo_gemini_key_$i",
                    'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent",
                    'active' => true,
                    'last_used' => 0,
                    'error_count' => 0,
                    'name' => "Demo Gemini API $i"
                ];
            }
        }
    }
    
    public function getNextAvailableAPI() {
        // Round-robin with error handling
        $attempts = 0;
        $totalAPIs = count($this->apiKeys);
        
        while ($attempts < $totalAPIs) {
            $api = $this->apiKeys[$this->currentKeyIndex];
            
            if ($api['active'] && $api['error_count'] < 5) {
                $this->apiKeys[$this->currentKeyIndex]['last_used'] = time();
                $this->currentKeyIndex = ($this->currentKeyIndex + 1) % $totalAPIs;
                return $api;
            }
            
            $this->currentKeyIndex = ($this->currentKeyIndex + 1) % $totalAPIs;
            $attempts++;
        }
        
        return null;
    }
    
    public function markAPIError($apiKey) {
        foreach ($this->apiKeys as &$api) {
            if ($api['key'] === $apiKey) {
                $api['error_count']++;
                if ($api['error_count'] > 5) {
                    $api['active'] = false;
                }
                break;
            }
        }
    }
    
    public function resetAPIErrors() {
        foreach ($this->apiKeys as &$api) {
            $api['error_count'] = 0;
            $api['active'] = true;
        }
    }
    
    public function getAPIStatus() {
        $active = 0;
        $total = count($this->apiKeys);
        $details = [];
        
        foreach ($this->apiKeys as $api) {
            if ($api['active']) $active++;
            $details[] = [
                'name' => $api['name'],
                'active' => $api['active'],
                'errors' => $api['error_count'],
                'last_used' => $api['last_used'] ? date('H:i:s', $api['last_used']) : 'Never'
            ];
        }
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'details' => $details
        ];
    }
    
    public function generateCallInsights($callData) {
        $prompt = "Analyze this call center data and provide 3 key insights in JSON format: " . 
                 json_encode(array_slice($callData, 0, 5)); // Limit data size
        
        return $this->makeAPICall($prompt, 300);
    }
    
    public function generatePerformanceRecommendations($metrics) {
        $prompt = "Based on these call center metrics, provide 3 actionable recommendations: " . 
                 json_encode($metrics);
        
        return $this->makeAPICall($prompt, 200);
    }
    
    private function makeAPICall($prompt, $maxTokens = 500) {
        $attempts = 0;
        
        while ($attempts < $this->maxRetries) {
            $api = $this->getNextAvailableAPI();
            
            if (!$api) {
                return ['error' => 'No available API keys', 'attempts' => $attempts];
            }
            
            $response = $this->callGeminiAPI($api, $prompt, $maxTokens);
            
            if ($response && !isset($response['error'])) {
                return $response;
            }
            
            $this->markAPIError($api['key']);
            $attempts++;
        }
        
        return ['error' => 'All API attempts failed', 'attempts' => $attempts];
    }
    
    private function callGeminiAPI($api, $prompt, $maxTokens) {
        // Simulate API call for demo purposes
        $demoResponses = [
            "Key insights: 1) Peak call volume at 10 AM, 2) 85% success rate trending up, 3) Agent performance varies by 15%",
            "Recommendations: 1) Add more agents during peak hours, 2) Implement call routing optimization, 3) Provide additional training",
            "Analysis shows strong performance with room for improvement in call duration and customer satisfaction metrics"
        ];
        
        // Return demo response
        return [
            'success' => true,
            'text' => $demoResponses[array_rand($demoResponses)],
            'api_used' => $api['name'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Initialize Gemini API Manager
$geminiManager = new GeminiAPIManager();

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
                'User-Agent: TrackerBI Call Dashboard',
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
            
            // Call duration ranges
            if ($billSec <= 30) {
                $analytics['call_duration_ranges']['0-30s']++;
            } elseif ($billSec <= 120) {
                $analytics['call_duration_ranges']['30s-2m']++;
            } elseif ($billSec <= 300) {
                $analytics['call_duration_ranges']['2m-5m']++;
            } else {
                $analytics['call_duration_ranges']['5m+']++;
            }
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
        $queue = $call['queue_name'] ?? 'Unknown';
        $analytics['processes'][$process] = ($analytics['processes'][$process] ?? 0) + 1;
        
        if (!isset($analytics['queue_performance'][$queue])) {
            $analytics['queue_performance'][$queue] = ['total' => 0, 'completed' => 0];
        }
        $analytics['queue_performance'][$queue]['total']++;
        if ($status === 'COMPLETED') {
            $analytics['queue_performance'][$queue]['completed']++;
        }
        
        // Disposition analysis
        $disposition = $call['disposition'] ?? '';
        if ($disposition && $disposition !== '') {
            $analytics['dispositions'][$disposition] = ($analytics['dispositions'][$disposition] ?? 0) + 1;
        }
        
        // Hourly distribution
        $startTime = $call['call_start_time'] ?? '00:00:00';
        if ($startTime && $startTime !== '00:00:00') {
            $hour = date('H', strtotime($startTime));
            $analytics['hourly_distribution'][$hour] = ($analytics['hourly_distribution'][$hour] ?? 0) + 1;
        }
        
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
    
    // Calculate queue performance
    foreach ($analytics['queue_performance'] as $queue => &$queueData) {
        if ($queueData['total'] > 0) {
            $queueData['success_rate'] = round(($queueData['completed'] / $queueData['total']) * 100, 1);
        }
    }
}

// Helper function to safely get value
function getValue($record, $key) {
    return isset($record[$key]) ? $record[$key] : '-';
}

// Get status color class
function getStatusClass($status) {
    $status = strtolower($status);
    if (in_array($status, ['active', 'connected', 'answered', 'success'])) {
        return 'bg-green-100 text-green-800';
    } elseif (in_array($status, ['inactive', 'disconnected', 'failed', 'busy'])) {
        return 'bg-red-100 text-red-800';
    } elseif (in_array($status, ['pending', 'waiting', 'ringing'])) {
        return 'bg-yellow-100 text-yellow-800';
    }
    return 'bg-gray-100 text-gray-800';
}

// Set page title for header
$page_title = 'Call Dashboard';

// Add mobile-specific styles
$additional_styles = '
<style>
/* Mobile-First Responsive Design for Call Dashboard */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Grid adjustments */
    .grid.md\\:grid-cols-2 {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
    
    .grid.md\\:grid-cols-3 {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .grid.md\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    /* Card adjustments */
    .glass-card {
        padding: 1.5rem !important;
    }
    
    /* Text adjustments */
    .text-5xl {
        font-size: 2.5rem !important;
    }
    
    .text-4xl {
        font-size: 2rem !important;
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
    
    /* Button adjustments */
    .btn, button {
        width: 100% !important;
        margin-bottom: 0.5rem !important;
        padding: 0.875rem 1rem !important;
    }
    
    /* Toggle buttons */
    .toggle-btn {
        width: 100% !important;
        margin-bottom: 1rem !important;
        padding: 1rem !important;
        font-size: 1rem !important;
    }
    
    /* API response sections */
    .api-section {
        margin-bottom: 2rem !important;
    }
    
    .api-section h3 {
        font-size: 1.25rem !important;
        margin-bottom: 1rem !important;
    }
    
    /* JSON viewer */
    .json-viewer {
        font-size: 0.875rem !important;
        padding: 1rem !important;
        max-height: 300px !important;
        overflow-y: auto !important;
    }
    
    /* Performance metrics */
    .performance-metric {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .performance-score {
        font-size: 1.5rem !important;
    }
    
    /* Progress bars */
    .progress-bar {
        height: 8px !important;
        margin: 0.5rem 0 !important;
    }
    
    /* Sentiment tags */
    .sentiment-tag {
        display: inline-block !important;
        margin: 0.25rem !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
    }
    
    /* Call data cards */
    .call-card {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .call-card h4 {
        font-size: 1.125rem !important;
    }
    
    /* Header padding */
    .py-16 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    .py-12 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    /* Flex adjustments */
    .flex.justify-between {
        flex-direction: column !important;
        gap: 1rem !important;
    }
    
    .flex.items-center {
        align-items: flex-start !important;
    }
}

@media (max-width: 480px) {
    .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .glass-card {
        padding: 1rem !important;
    }
    
    .text-5xl {
        font-size: 2rem !important;
    }
    
    .text-4xl {
        font-size: 1.75rem !important;
    }
    
    .text-3xl {
        font-size: 1.5rem !important;
    }
    
    .text-2xl {
        font-size: 1.25rem !important;
    }
    
    /* Grid for very small screens */
    .grid.md\\:grid-cols-4 {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
    
    .toggle-btn {
        padding: 0.75rem !important;
        font-size: 0.9rem !important;
    }
    
    .json-viewer {
        font-size: 0.75rem !important;
        padding: 0.75rem !important;
        max-height: 250px !important;
    }
    
    .performance-score {
        font-size: 1.25rem !important;
    }
    
    .call-card {
        padding: 0.75rem !important;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    button, .btn, .toggle-btn {
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
    
    .json-viewer {
        max-height: 200px !important;
    }
}

/* Collapsible sections for mobile */
@media (max-width: 768px) {
    .collapsible-section {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    
    .collapsible-header {
        background: #f9fafb;
        padding: 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .collapsible-content {
        padding: 1rem;
        display: none;
    }
    
    .collapsible-content.active {
        display: block;
    }
    
    .collapsible-arrow {
        transition: transform 0.3s ease;
    }
    
    .collapsible-arrow.rotated {
        transform: rotate(180deg);
    }
}
</style>
';

// Include common header
include 'includes/header.php';
?>

    <!-- Enhanced Page Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-12">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-white text-center mb-4">
                <i class="fas fa-chart-line mr-3"></i>
                   Call Analytics Dashboard
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
                <?php if ($apiError): ?>
                    <div class="mt-2 text-sm text-red-200">
                        <?php echo htmlspecialchars($apiError); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">



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

    <!-- API Response Data Table -->
    <div class="container mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">📋 API Response Data</h2>
                    <p class="text-gray-600 text-sm">Raw call data from API endpoint for detailed analysis</p>
                </div>
                <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>

            <?php if ($apiData): ?>
                <!-- Call Data Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">S.No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Agent ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Phone Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Process Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Queue Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Dialer Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Disposition</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Call Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Call Start Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Call End Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            // Check if data is an array of records or a single record
                            $records = [];
                            
                            if (isset($apiData['data']) && is_array($apiData['data'])) {
                                // If data is nested under 'data' key
                                $records = is_array($apiData['data']) && isset($apiData['data'][0]) ? $apiData['data'] : [$apiData['data']];
                            } elseif (is_array($apiData) && isset($apiData[0])) {
                                // If data is directly an array of records
                                $records = $apiData;
                            } elseif (is_array($apiData)) {
                                // If data is a single record
                                $records = [$apiData];
                            }
                            
                            if (!empty($records)):
                                foreach ($records as $index => $record):
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'agent_id')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'phone_number')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'process_name')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'queue_name')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span class="px-2 py-1 rounded text-xs <?php echo getStatusClass(getValue($record, 'status')); ?>">
                                        <?php echo htmlspecialchars(getValue($record, 'status')); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span class="px-2 py-1 rounded text-xs <?php echo getStatusClass(getValue($record, 'dialer_status')); ?>">
                                        <?php echo htmlspecialchars(getValue($record, 'dialer_status')); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'disposition')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars(getValue($record, 'call_type')); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?php 
                                    $startTime = getValue($record, 'call_start_time');
                                    if ($startTime !== '-' && strtotime($startTime)) {
                                        echo date('Y-m-d H:i:s', strtotime($startTime));
                                    } else {
                                        echo htmlspecialchars($startTime);
                                    }
                                    ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?php 
                                    $endTime = getValue($record, 'call_end_time');
                                    if ($endTime !== '-' && strtotime($endTime)) {
                                        echo date('Y-m-d H:i:s', strtotime($endTime));
                                    } else {
                                        echo htmlspecialchars($endTime);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2">📋</div>
                                    <div class="text-lg font-medium">No Call Records Found</div>
                                    <div class="text-sm">The API response doesn't contain the expected call data structure.</div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Data Summary -->
                <?php if (!empty($records)): ?>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                    <div>
                        Showing <span class="font-medium"><?php echo count($records); ?></span> call record(s)
                    </div>
                    <div>
                        Last updated: <span class="font-medium"><?php echo date('Y-m-d H:i:s'); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Error State -->
                <div class="text-center py-12">
                    <div class="text-6xl text-gray-400 mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Unable to Load API Data</h3>
                    <p class="text-gray-500 mb-4">
                        <?php echo $apiError ? htmlspecialchars($apiError) : 'Unknown error occurred'; ?>
                    </p>
                    <button onclick="location.reload()" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Try Again
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        console.log('Clean Call Dashboard loaded');
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>

<?php include 'includes/footer.php'; ?>
