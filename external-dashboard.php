<?php
require_once 'config.php';

// External API endpoint
$externalApiUrl = 'https://commodiously-appositional-yung.ngrok-free.dev/api.php';

// Function to fetch data from external API
function fetchExternalData($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            'User-Agent: TrackerBI-Dashboard/1.0',
            'Accept: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'success' => $httpCode === 200 && !$error,
        'data' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

// Fetch data from external API
$apiResponse = fetchExternalData($externalApiUrl);
$externalData = null;
$apiError = null;

if ($apiResponse['success']) {
    $decodedData = json_decode($apiResponse['data'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $externalData = $decodedData;
    } else {
        $apiError = "Invalid JSON response from external API";
    }
} else {
    $apiError = $apiResponse['error'] ?: "HTTP Error: " . $apiResponse['http_code'];
}

// Set page title for header
$page_title = 'Call Dashboard';

// Additional styles for this page
$additional_styles = '
<style>
    .data-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .data-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .status-indicator {
        position: relative;
    }
    .status-indicator::before {
        content: "";
        position: absolute;
        top: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    .status-online::before {
        background-color: #10b981;
    }
    .status-offline::before {
        background-color: #ef4444;
    }
    
    .json-viewer {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        overflow-x: auto;
        font-family: "JetBrains Mono", "Fira Code", "Courier New", monospace;
        font-size: 14px;
        line-height: 1.6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .json-key { color: #60a5fa; }
    .json-string { color: #34d399; }
    .json-number { color: #fbbf24; }
    .json-boolean { color: #f87171; }
    .json-null { color: #9ca3af; }
    
    .status-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
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
                <i class="fas fa-external-link-alt mr-4 icon-accent"></i>
                External API Dashboard
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto mb-4">
                Real-time data integration from external API endpoints
            </p>
            <div class="text-center">
                <span class="inline-block px-4 py-2 bg-white bg-opacity-20 rounded-full text-white text-sm font-medium">
                    <i class="fas fa-link mr-2"></i>
                    <?php echo htmlspecialchars(parse_url($externalApiUrl, PHP_URL_HOST)); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <!-- API Status -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-wifi mr-3 icon-accent"></i>
                API Connection Status
            </h3>
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <?php if ($apiResponse['success']): ?>
                            <div class="status-indicator status-online w-5 h-5 bg-green-500 rounded-full mr-4"></div>
                            <div>
                                <span class="text-green-700 font-semibold text-lg">Connected</span>
                                <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium status-badge">LIVE</span>
                            </div>
                        <?php else: ?>
                            <div class="status-indicator status-offline w-5 h-5 bg-red-500 rounded-full mr-4"></div>
                            <div>
                                <span class="text-red-700 font-semibold text-lg">Connection Failed</span>
                                <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">OFFLINE</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-sm text-secondary">
                        <i class="fas fa-clock mr-2"></i>
                        <?php echo date('M j, Y \a\t g:i A'); ?>
                    </div>
                </div>
                
                <?php if ($apiError): ?>
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                        <div>
                            <p class="text-red-700 font-medium">Connection Error</p>
                            <p class="text-red-600 text-sm mt-1"><?php echo htmlspecialchars($apiError); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($externalData): ?>
        <!-- Data Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <?php
            // Extract key metrics from the data
            $metrics = [];
            
            if (is_array($externalData)) {
                // Count total items
                $metrics['Total Items'] = count($externalData);
                
                // If it's an associative array, show key count
                if (!empty($externalData) && !is_numeric(array_keys($externalData)[0])) {
                    $metrics['Data Fields'] = count(array_keys($externalData));
                }
                
                // Check for common fields
                if (isset($externalData['success'])) {
                    $metrics['Success'] = $externalData['success'] ? 'Yes' : 'No';
                }
                
                if (isset($externalData['timestamp'])) {
                    $metrics['Last Update'] = date('H:i:s', strtotime($externalData['timestamp']));
                }
            }
            
            // Default metrics if none found
            if (empty($metrics)) {
                $metrics = [
                    'Data Size' => number_format(strlen(json_encode($externalData))) . ' bytes',
                    'Data Type' => ucfirst(gettype($externalData)),
                    'Status' => 'Loaded',
                    'Format' => 'JSON'
                ];
            }
            ?>
            
            <?php foreach ($metrics as $label => $value): ?>
            <div class="data-card rounded-2xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary text-sm font-medium mb-2"><?php echo htmlspecialchars($label); ?></p>
                        <p class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($value); ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-100 to-cyan-100">
                        <i class="fas fa-chart-bar text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Raw Data Display -->
        <div class="glass-card rounded-2xl p-8 mb-12">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-primary">
                    <i class="fas fa-code mr-3 icon-accent"></i>
                    API Response Data
                </h3>
                <button onclick="refreshData()" class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-6 py-3 rounded-xl transition duration-300 font-medium shadow-lg">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Data
                </button>
            </div>
            
            <!-- Formatted JSON Display -->
            <div class="json-viewer">
                <pre id="jsonData"><?php echo htmlspecialchars(json_encode($externalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
            </div>
        </div>

        <!-- Data Analysis -->
        <?php if (is_array($externalData)): ?>
        <div class="glass-card rounded-2xl p-8 mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-search mr-3 icon-accent"></i>
                Data Structure Analysis
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Structure -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border">
                    <h4 class="font-semibold mb-4 text-primary">Data Structure</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Type:</span>
                            <span class="font-medium text-primary px-3 py-1 bg-blue-100 rounded-full text-sm">
                                <?php echo is_array($externalData) ? (array_keys($externalData) === range(0, count($externalData) - 1) ? 'Indexed Array' : 'Associative Array') : gettype($externalData); ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Size:</span>
                            <span class="font-medium text-primary"><?php echo is_array($externalData) ? count($externalData) . ' items' : 'N/A'; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Memory:</span>
                            <span class="font-medium text-primary"><?php echo number_format(strlen(json_encode($externalData))); ?> bytes</span>
                        </div>
                    </div>
                </div>
                
                <!-- Key Fields -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                    <h4 class="font-semibold mb-4 text-primary">Key Fields</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <?php
                        $keys = is_array($externalData) ? array_keys($externalData) : [];
                        $displayKeys = array_slice($keys, 0, 10); // Show first 10 keys
                        ?>
                        <?php foreach ($displayKeys as $key): ?>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-700 font-mono bg-blue-100 px-2 py-1 rounded"><?php echo htmlspecialchars($key); ?></span>
                            <span class="text-secondary text-xs"><?php echo gettype($externalData[$key]); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($keys) > 10): ?>
                        <div class="text-sm text-secondary text-center pt-2 border-t border-blue-200">
                            ... and <?php echo count($keys) - 10; ?> more fields
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- No Data State -->
        <div class="glass-card rounded-2xl p-16 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-3xl font-semibold text-primary mb-4">No Data Available</h3>
            <p class="text-secondary mb-8 max-w-md mx-auto">
                Unable to fetch data from the external API endpoint. This could be due to network issues or API unavailability.
            </p>
            <button onclick="refreshData()" class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-8 py-4 rounded-xl transition duration-300 font-medium shadow-lg">
                <i class="fas fa-sync-alt mr-2"></i>
                Try Again
            </button>
        </div>
        <?php endif; ?>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script>
    // Refresh data function
    function refreshData() {
        window.location.reload();
    }
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        const refreshBtn = document.querySelector("button[onclick=\"refreshData()\"]");
        if (refreshBtn) {
            refreshBtn.innerHTML = "<i class=\"fas fa-sync-alt fa-spin mr-2\"></i>Refreshing...";
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }, 30000);
    
    // Syntax highlighting for JSON
    document.addEventListener("DOMContentLoaded", function() {
        const jsonElement = document.getElementById("jsonData");
        if (jsonElement) {
            let content = jsonElement.textContent;
            
            // Basic JSON syntax highlighting
            content = content.replace(/"([^"]+)":/g, "<span class=\"json-key\">\"$1\":</span>");
            content = content.replace(/: "([^"]+)"/g, ": <span class=\"json-string\">\"$1\"</span>");
            content = content.replace(/: (\\d+\\.?\\d*)/g, ": <span class=\"json-number\">$1</span>");
            content = content.replace(/: (true|false)/g, ": <span class=\"json-boolean\">$1</span>");
            content = content.replace(/: null/g, ": <span class=\"json-null\">null</span>");
            
            jsonElement.innerHTML = content;
        }
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
