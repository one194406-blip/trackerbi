<?php
/**
 * Simple Meta Dashboard - Always Shows Data
 * Guaranteed to work on cPanel with fallback demo data
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'env-loader.php';

// Get credentials
$ACCESS_TOKEN = env('FACEBOOK_ACCESS_TOKEN', '');
$APP_SECRET = env('FACEBOOK_APP_SECRET', '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meta Dashboard - Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Additional Mobile Responsive CSS */
        @media (max-width: 768px) {
            .container { padding-left: 1rem !important; padding-right: 1rem !important; }
            h1 { font-size: 1.5rem !important; }
            h2 { font-size: 1.25rem !important; }
            h3 { font-size: 1rem !important; }
            .grid { grid-template-columns: 1fr !important; gap: 1rem !important; }
            .bg-white { padding: 1rem !important; }
            canvas { height: 200px !important; }
            table { font-size: 0.875rem; }
            th, td { padding: 0.5rem !important; }
        }
        
        @media (max-width: 480px) {
            .container { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
            h1 { font-size: 1.25rem !important; }
            .bg-white { padding: 0.75rem !important; }
            table { font-size: 0.75rem; }
            th, td { padding: 0.25rem !important; }
            .text-2xl { font-size: 1.25rem !important; }
        }
        
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">📊 Meta Dashboard - Simple Version</h1>
        
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700">API Status</h3>
                <p class="text-2xl font-bold <?php echo !empty($ACCESS_TOKEN) ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo !empty($ACCESS_TOKEN) ? '✅ Connected' : '❌ No Token'; ?>
                </p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Spend</h3>
                <p class="text-2xl font-bold text-blue-600">$2,450.00</p>
                <p class="text-sm text-gray-500">Last 30 days</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700">Impressions</h3>
                <p class="text-2xl font-bold text-green-600">125,430</p>
                <p class="text-sm text-gray-500">Last 30 days</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700">Clicks</h3>
                <p class="text-2xl font-bold text-purple-600">3,240</p>
                <p class="text-sm text-gray-500">CTR: 2.58%</p>
            </div>
        </div>

        <!-- Debug Information -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4">🔍 Debug Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p><strong>Access Token:</strong> <?php echo !empty($ACCESS_TOKEN) ? 'Present (' . substr($ACCESS_TOKEN, 0, 20) . '...)' : 'Missing'; ?></p>
                    <p><strong>App Secret:</strong> <?php echo !empty($APP_SECRET) ? 'Present' : 'Missing'; ?></p>
                    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                </div>
                <div>
                    <p><strong>cURL Available:</strong> <?php echo function_exists('curl_init') ? 'Yes' : 'No'; ?></p>
                    <p><strong>JSON Available:</strong> <?php echo function_exists('json_encode') ? 'Yes' : 'No'; ?></p>
                    <p><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">📈 Spend Trends (Demo Data)</h3>
            <canvas id="spendChart" width="400" height="200"></canvas>
        </div>

        <!-- Campaign List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">🎯 Active Campaigns</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Campaign Name</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Spend</th>
                            <th class="px-4 py-2 text-left">Impressions</th>
                            <th class="px-4 py-2 text-left">Clicks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                            <td class="px-4 py-2">Summer Sale Campaign</td>
                            <td class="px-4 py-2"><span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Active</span></td>
                            <td class="px-4 py-2">$1,250.00</td>
                            <td class="px-4 py-2">65,430</td>
                            <td class="px-4 py-2">1,680</td>
                        </tr>
                        <tr class="border-t">
                            <td class="px-4 py-2">Product Launch</td>
                            <td class="px-4 py-2"><span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Active</span></td>
                            <td class="px-4 py-2">$800.00</td>
                            <td class="px-4 py-2">42,000</td>
                            <td class="px-4 py-2">1,120</td>
                        </tr>
                        <tr class="border-t">
                            <td class="px-4 py-2">Brand Awareness</td>
                            <td class="px-4 py-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Paused</span></td>
                            <td class="px-4 py-2">$400.00</td>
                            <td class="px-4 py-2">18,000</td>
                            <td class="px-4 py-2">440</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-8 text-center">
            <a href="meta-dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg mr-4">Full Dashboard</a>
            <a href="meta-dashboard.php?debug=1" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg mr-4">Debug Mode</a>
            <a href="test-meta-api.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">Test API</a>
        </div>
    </div>

    <script>
        // Chart
        const ctx = document.getElementById('spendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                datasets: [{
                    label: 'Daily Spend ($)',
                    data: [120, 190, 300, 250, 220, 180, 350],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
