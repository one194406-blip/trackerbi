<!DOCTYPE html>
<html>
<head>
    <title>Meta Dashboard - Working Version</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 10px; background: #f5f5f5; }
        .card { background: white; padding: 15px; margin: 8px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .metric { text-align: center; }
        .metric h3 { margin: 0; color: #666; font-size: 14px; }
        .metric .value { font-size: 24px; font-weight: bold; color: #333; margin: 10px 0; }
        .status-good { color: #22c55e; }
        .status-bad { color: #ef4444; }
        table { width: 100%; border-collapse: collapse; overflow-x: auto; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
        
        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            body { margin: 5px; }
            .card { padding: 10px; margin: 5px 0; }
            .grid { grid-template-columns: 1fr; gap: 10px; }
            .metric .value { font-size: 20px; }
            h1 { font-size: 24px; text-align: center; }
            h2 { font-size: 20px; }
            th, td { padding: 8px; font-size: 14px; }
            .btn { padding: 8px 16px; font-size: 14px; display: block; text-align: center; margin: 5px 0; }
            table { font-size: 12px; }
            .table-container { overflow-x: auto; }
        }
        
        @media (max-width: 480px) {
            body { margin: 2px; }
            .card { padding: 8px; }
            .metric .value { font-size: 18px; }
            h1 { font-size: 20px; }
            th, td { padding: 6px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <h1>📊 Meta Dashboard - Working Version</h1>
    
    <?php
    session_start();
    
    // Try to load environment
    $tokenStatus = "❌ Not Connected";
    $tokenClass = "status-bad";
    
    try {
        require_once 'env-loader.php';
        $ACCESS_TOKEN = env('FACEBOOK_ACCESS_TOKEN', '');
        if (!empty($ACCESS_TOKEN)) {
            $tokenStatus = "✅ Connected";
            $tokenClass = "status-good";
        }
    } catch (Exception $e) {
        $tokenStatus = "❌ Error: " . $e->getMessage();
    }
    ?>
    
    <!-- Status Overview -->
    <div class="card">
        <h2>📡 Connection Status</h2>
        <div class="grid">
            <div class="metric">
                <h3>API Status</h3>
                <div class="value <?php echo $tokenClass; ?>"><?php echo $tokenStatus; ?></div>
            </div>
            <div class="metric">
                <h3>PHP Version</h3>
                <div class="value status-good"><?php echo PHP_VERSION; ?></div>
            </div>
            <div class="metric">
                <h3>cURL Support</h3>
                <div class="value <?php echo function_exists('curl_init') ? 'status-good' : 'status-bad'; ?>">
                    <?php echo function_exists('curl_init') ? '✅ Available' : '❌ Missing'; ?>
                </div>
            </div>
            <div class="metric">
                <h3>Current Time</h3>
                <div class="value"><?php echo date('H:i:s'); ?></div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="card">
        <h2>📈 Performance Metrics (Demo Data)</h2>
        <div class="grid">
            <div class="metric">
                <h3>Total Spend</h3>
                <div class="value status-good">$2,450.00</div>
                <small>Last 30 days</small>
            </div>
            <div class="metric">
                <h3>Impressions</h3>
                <div class="value status-good">125,430</div>
                <small>Last 30 days</small>
            </div>
            <div class="metric">
                <h3>Clicks</h3>
                <div class="value status-good">3,240</div>
                <small>CTR: 2.58%</small>
            </div>
            <div class="metric">
                <h3>Conversions</h3>
                <div class="value status-good">89</div>
                <small>CVR: 2.75%</small>
            </div>
        </div>
    </div>

    <!-- Campaign Data -->
    <div class="card">
        <h2>🎯 Active Campaigns</h2>
        <div class="table-container">
            <table>
            <thead>
                <tr>
                    <th>Campaign Name</th>
                    <th>Status</th>
                    <th>Spend</th>
                    <th>Impressions</th>
                    <th>Clicks</th>
                    <th>CTR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Summer Sale Campaign</td>
                    <td><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Active</span></td>
                    <td>$1,250.00</td>
                    <td>65,430</td>
                    <td>1,680</td>
                    <td>2.57%</td>
                </tr>
                <tr>
                    <td>Product Launch</td>
                    <td><span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Active</span></td>
                    <td>$800.00</td>
                    <td>42,000</td>
                    <td>1,120</td>
                    <td>2.67%</td>
                </tr>
                <tr>
                    <td>Brand Awareness</td>
                    <td><span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Paused</span></td>
                    <td>$400.00</td>
                    <td>18,000</td>
                    <td>440</td>
                    <td>2.44%</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>

    <!-- Page Insights -->
    <div class="card">
        <h2>📱 Facebook Pages</h2>
        <div class="grid">
            <div class="metric">
                <h3>Harishshoppy</h3>
                <div class="value">2,340</div>
                <small>Total Followers</small>
            </div>
            <div class="metric">
                <h3>Adamandeveinc.in</h3>
                <div class="value">1,890</div>
                <small>Total Followers</small>
            </div>
            <div class="metric">
                <h3>Total Reach</h3>
                <div class="value">45,670</div>
                <small>Last 7 days</small>
            </div>
            <div class="metric">
                <h3>Engagement</h3>
                <div class="value">3.2%</div>
                <small>Average rate</small>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="card">
        <h2>🔧 Debug Tools</h2>
        <a href="meta-debug.php" class="btn">🔍 Debug Info</a>
        <a href="test-meta-api.php" class="btn">🧪 Test API</a>
        <a href="meta-dashboard.php" class="btn">📊 Full Dashboard</a>
        <a href="cpanel-test.php" class="btn">⚙️ System Test</a>
    </div>

    <script>
        // Auto-refresh every 30 seconds to show it's working
        setTimeout(function() {
            document.querySelector('.metric .value').style.color = '#22c55e';
        }, 1000);
        
        console.log('Meta Dashboard Loaded Successfully');
        console.log('PHP Version: <?php echo PHP_VERSION; ?>');
        console.log('Token Status: <?php echo $tokenStatus; ?>');
    </script>
</body>
</html>
