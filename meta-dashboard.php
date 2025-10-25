<?php
/**********************************************************
 * META DASHBOARD — TrackerBI Integration
 * - Facebook Pages Insights
 * - Meta Ads Insights  
 * - System Configuration
 * - Modern Glass UI Design
 **********************************************************/

require_once 'env-loader.php';
require_once 'config.php';
require_once 'ErrorHandler.php';

/* =====================  META API CONFIG FROM .ENV  ===================== */
$ACCESS_TOKEN = env('FACEBOOK_ACCESS_TOKEN', '');
$APP_SECRET   = env('FACEBOOK_APP_SECRET', '');
$APP_ID       = env('FACEBOOK_APP_ID', '');

/* Ad accounts from .env */
$AD_ACCOUNTS = [
  env('FACEBOOK_AD_ACCOUNT_INSTAGRAM', 'act_837599231811269') => 'Instagram',
  env('FACEBOOK_AD_ACCOUNT_FACEBOOK', 'act_782436794463558') => 'Facebook',
];

/* Pages mapping for page metrics/cards from .env */
$PAGES = [
   env('FACEBOOK_PAGE_HARISHSHOPPY_ID', '613327751869662') => 'Harishshoppy',
   env('FACEBOOK_PAGE_ADAMANDEVE_ID', '665798336609925') => 'Adamandeveinc.in'
];

/* List of Pages with long-lived Page Access Tokens from .env */
$pages = [
    [
        'id' => env('FACEBOOK_PAGE_HARISHSHOPPY_ID', '613327751869662'),
        'name' => 'Harishshoppy',
        'token' => env('FACEBOOK_PAGE_HARISHSHOPPY_TOKEN', '')
    ],
    [
        'id' => env('FACEBOOK_PAGE_ADAMANDEVE_ID', '665798336609925'),
        'name' => 'Adamandeveinc.in',
        'token' => env('FACEBOOK_PAGE_ADAMANDEVE_TOKEN', '')
    ]
];

$DEFAULT_AD_ACCOUNT = array_key_first($AD_ACCOUNTS);
$DEFAULT_DATE_PRESET = env('META_DEFAULT_DATE_PRESET', 'last_30d');
$GRAPH_API_VERSION = env('META_GRAPH_API_VERSION', 'v22.0');
$PAGE_GRAPH_VER = env('META_PAGE_GRAPH_VERSION', 'v23.0');

if ($ACCESS_TOKEN && $APP_SECRET) {
    $appsecret_proof = hash_hmac('sha256', $ACCESS_TOKEN, $APP_SECRET);
} else {
    $appsecret_proof = '';
}

/* ---------- Helpers ---------- */
function fb_get(string $endpoint, array $params = [], string $version = 'v22.0', string $token = '', string $proof = ''): array {
  if (!$token) return ['error' => ['message' => 'No access token provided']];
  
  $base = "https://graph.facebook.com/$version";
  $params['access_token'] = $token;
  if ($proof) $params['appsecret_proof'] = $proof;
  $url = $base . $endpoint . '?' . http_build_query($params);

  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_USERAGENT => 'TrackerBI/1.0 (Windows; PHP/' . PHP_VERSION . ')',
    CURLOPT_HTTPHEADER => [
      'Accept: application/json',
      'Content-Type: application/json',
      'Connection: close'
    ],
    // Additional SSL/TLS options for Windows/XAMPP
    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
    CURLOPT_SSL_CIPHER_LIST => 'ECDHE+AESGCM:ECDHE+CHACHA20:DHE+AESGCM:DHE+CHACHA20:!aNULL:!MD5:!DSS',
  ]);
  
  $raw = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlError = curl_error($ch);
  curl_close($ch);
  
  if ($raw === false || !empty($curlError)) {
    error_log("Facebook API cURL Error: $curlError");
    return ['error' => ['message' => "Connection failed: $curlError", 'type' => 'network_error']];
  }
  
  if ($httpCode >= 400) {
    error_log("Facebook API HTTP Error: $httpCode - $raw");
    return ['error' => ['message' => "HTTP Error $httpCode", 'raw' => $raw, 'type' => 'http_error']];
  }
  
  $json = json_decode($raw, true);
  if (!is_array($json)) {
    error_log("Facebook API JSON Error: Invalid response - $raw");
    return ['error' => ['message' => 'Invalid JSON from Graph API', 'raw' => $raw, 'type' => 'json_error']];
  }
  
  return $json;
}

function get_action_value(array $actions = null, array $action_types = []): int {
  if (!$actions || !is_array($actions)) return 0;
  $sum = 0;
  foreach ($actions as $a) {
    if (!isset($a['action_type'])) continue;
    if (in_array($a['action_type'], $action_types, true)) {
      $sum += (int)($a['value'] ?? 0);
    }
  }
  return $sum;
}

function g($arr, $key, $default = 0) { return isset($arr[$key]) ? $arr[$key] : $default; }
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ======================= ADDITIONAL HELPERS =======================
function fbApiGet($endpoint, $params, $token, $ver) {
    $params['access_token'] = $token;
    $url = "https://graph.facebook.com/{$ver}/{$endpoint}?" . http_build_query($params);
    $resp = @file_get_contents($url);
    if ($resp === false) { return []; }
    $json = json_decode($resp, true);
    return is_array($json) ? $json : [];
}

function buildDailySeries($metricResp) {
    $labels = [];
    $values = [];
    
    if (isset($metricResp['data']) && is_array($metricResp['data'])) {
        foreach ($metricResp['data'] as $dataPoint) {
            if (isset($dataPoint['date_start']) && isset($dataPoint['value'])) {
                $labels[] = $dataPoint['date_start'];
                $values[] = (float)$dataPoint['value'];
            }
        }
    }
    
    return ['labels' => $labels, 'values' => $values];
}

/* ---------- System Information ---------- */
$systemInfo = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'extensions' => [
        'curl' => extension_loaded('curl'),
        'json' => extension_loaded('json'),
        'fileinfo' => extension_loaded('fileinfo'),
        'openssl' => extension_loaded('openssl')
    ]
];

// Get file system info
$uploadDirSize = 0;
$logDirSize = 0;
$uploadFileCount = 0;
$logFileCount = 0;

if (is_dir(UPLOAD_DIR)) {
    $files = glob(UPLOAD_DIR . '*');
    $uploadFileCount = count($files);
    foreach ($files as $file) {
        if (is_file($file)) {
            $uploadDirSize += filesize($file);
        }
    }
}

$logDir = dirname(LOG_FILE);
if (is_dir($logDir)) {
    $files = glob($logDir . '/*');
    $logFileCount = count($files);
    foreach ($files as $file) {
        if (is_file($file)) {
            $logDirSize += filesize($file);
        }
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

/* ---------- Inputs (GET) ---------- */
$ad = isset($_GET['ad_account']) && isset($AD_ACCOUNTS[$_GET['ad_account']]) ? $_GET['ad_account'] : $DEFAULT_AD_ACCOUNT;
$date_preset = $_GET['date_preset'] ?? $DEFAULT_DATE_PRESET;
$since_in = $_GET['since'] ?? null;
$until_in = $_GET['until'] ?? null;
$use_time_range = $since_in && $until_in;

/* ---------- Meta API Data Fetching ---------- */
$campaigns = [];
$insights = [];
$totalSpend = 0;
$totalImpressions = 0;
$totalClicks = 0;
$totalLeads = 0;
$labels = [];
$spends = [];
$allData = [];

// Check if demo mode is requested (default to demo mode if no API tokens or if explicitly requested)
$demoMode = isset($_GET['demo']) || !$ACCESS_TOKEN;
$apiErrors = [];

// Debug output for cPanel troubleshooting
if (isset($_GET['debug'])) {
    echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px; border: 1px solid #ccc;'>";
    echo "<h3>🔍 Debug Information</h3>";
    echo "<p><strong>Demo Mode:</strong> " . ($demoMode ? "YES" : "NO") . "</p>";
    echo "<p><strong>Access Token:</strong> " . (!empty($ACCESS_TOKEN) ? "Present (" . substr($ACCESS_TOKEN, 0, 20) . "...)" : "Missing") . "</p>";
    echo "<p><strong>App Secret:</strong> " . (!empty($APP_SECRET) ? "Present" : "Missing") . "</p>";
    echo "<p><strong>Selected Ad Account:</strong> $ad</p>";
    echo "<p><strong>Date Preset:</strong> $date_preset</p>";
    echo "<p><strong>Available Ad Accounts:</strong> " . implode(', ', array_keys($AD_ACCOUNTS)) . "</p>";
    echo "</div>";
}

// Only fetch Meta data if tokens are configured and not in demo mode
if ($ACCESS_TOKEN && $APP_SECRET && !$demoMode) {
    try {
        /* Fetch Campaigns */
        $campaign_fields = 'id,name,status,objective,effective_status,start_time,stop_time';
        $campaignsResp = fb_get("/$ad/campaigns", [
          'fields' => $campaign_fields,
          'limit'  => 250
        ], $GRAPH_API_VERSION, $ACCESS_TOKEN, $appsecret_proof);
        
        if (isset($campaignsResp['error'])) {
            $apiErrors[] = "Campaigns: " . $campaignsResp['error']['message'];
            $campaigns = [];
        } else {
            $campaigns = $campaignsResp['data'] ?? [];
        }

        /* Fetch Insights */
        $insights_fields = [
          'campaign_id','campaign_name','spend','impressions','reach','clicks','ctr','cpc','cpm','actions','inline_link_clicks','unique_inline_link_clicks'
        ];
        $insights_params = [
          'level' => 'campaign',
          'fields' => implode(',', $insights_fields),
          'limit' => 500
        ];
        if ($use_time_range) {
          $insights_params['time_range'] = json_encode(['since'=>$since_in,'until'=>$until_in]);
        } else {
          $insights_params['date_preset'] = $date_preset;
        }
        $insightsResp = fb_get("/$ad/insights", $insights_params, $GRAPH_API_VERSION, $ACCESS_TOKEN, $appsecret_proof);
        
        if (isset($insightsResp['error'])) {
            $apiErrors[] = "Insights: " . $insightsResp['error']['message'];
            $insights = [];
        } else {
            $insights = $insightsResp['data'] ?? [];
        }

        /* Calculate totals */
        foreach ($insights as $row) {
          $totalSpend += (float)g($row,'spend',0);
          $totalImpressions += (int)g($row,'impressions',0);
          $totalClicks += (int)g($row,'clicks',0);
          $leads = get_action_value($row['actions'] ?? null, ['lead', 'offsite_conversion.lead']);
          $totalLeads += $leads;
        }

        /* Chart data */
        foreach ($campaigns as $c) {
          $labels[] = $c['name'];
          $spends[] = (float)g($insights[0] ?? [], 'spend', 0);
        }

        /* Fetch Pages Data */
        $PAGE_SINCE = $since_in ?? date('Y-m-d', strtotime('-28 days'));
        $PAGE_UNTIL = $until_in ?? date('Y-m-d');

        foreach ($pages as $p) {
            $data = [
                'id'=>$p['id'],'name'=>$p['name'],'about'=>'',
                'total_followers'=>0,'reach'=>0,'engaged'=>0,
                'labels'=>[],'daily_reach'=>[],'daily_engaged'=>[],
                'page_views'=>0,'video_views'=>0,'engagement_rate'=>0
            ];

            // Basic page info
            $info = fb_get("/{$p['id']}", ['fields'=>'about,followers_count,fan_count'], $PAGE_GRAPH_VER, $p['token']);
            if (!isset($info['error'])) {
                $data['about'] = $info['about'] ?? '';
                $data['total_followers'] = $info['followers_count'] ?? ($info['fan_count'] ?? 0);
            } else {
                $apiErrors[] = "Page {$p['name']}: " . $info['error']['message'];
            }

            $allData[] = $data;
        }
        
        // If we have critical API errors, fall back to demo mode
        if (!empty($apiErrors) && empty($campaigns) && empty($insights)) {
            $demoMode = true;
        }
        
    } catch (Exception $e) {
        error_log("Meta API Exception: " . $e->getMessage());
        $apiErrors[] = "System error: " . $e->getMessage();
        $demoMode = true;
    }
}

// Use demo data if in demo mode or if API completely failed
if ($demoMode || (!empty($apiErrors) && empty($campaigns) && empty($insights))) {
    // Demo data for when API is not available
    $campaigns = [
        [
            'id' => 'demo_1',
            'name' => 'Summer Sale Campaign',
            'status' => 'ACTIVE',
            'objective' => 'CONVERSIONS'
        ],
        [
            'id' => 'demo_2', 
            'name' => 'Brand Awareness Drive',
            'status' => 'ACTIVE',
            'objective' => 'REACH'
        ],
        [
            'id' => 'demo_3',
            'name' => 'Holiday Promotion',
            'status' => 'PAUSED',
            'objective' => 'TRAFFIC'
        ]
    ];
    
    $insights = [
        [
            'campaign_id' => 'demo_1',
            'spend' => '2500.50',
            'impressions' => '45000',
            'clicks' => '1200',
            'ctr' => '2.67'
        ],
        [
            'campaign_id' => 'demo_2',
            'spend' => '1800.25',
            'impressions' => '38000',
            'clicks' => '950',
            'ctr' => '2.50'
        ],
        [
            'campaign_id' => 'demo_3',
            'spend' => '3200.75',
            'impressions' => '52000',
            'clicks' => '1580',
            'ctr' => '3.04'
        ]
    ];
    
    // Calculate demo totals
    $totalSpend = 7501.50;
    $totalImpressions = 135000;
    $totalClicks = 3730;
    $totalLeads = 125;
    
    // Demo chart data
    $labels = ['Summer Sale Campaign', 'Brand Awareness Drive', 'Holiday Promotion'];
    $spends = [2500.50, 1800.25, 3200.75];
    
    // Demo pages data
    $allData = [
        [
            'id' => '613327751869662',
            'name' => 'Harishshoppy',
            'about' => 'Your trusted online shopping destination',
            'total_followers' => 12500,
            'reach' => 45000,
            'engaged' => 3200,
            'page_views' => 8500,
            'video_views' => 2100,
            'engagement_rate' => 7.11
        ],
        [
            'id' => '665798336609925',
            'name' => 'Adamandeveinc.in',
            'about' => 'Premium products and services',
            'total_followers' => 8750,
            'reach' => 32000,
            'engaged' => 2850,
            'page_views' => 6200,
            'video_views' => 1650,
            'engagement_rate' => 8.91
        ]
    ];
}

$avgCPC = $totalClicks > 0 ? $totalSpend / $totalClicks : 0;
$avgCPM = $totalImpressions > 0 ? ($totalSpend / $totalImpressions) * 1000 : 0;
$avgCTR = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0;
$avgCPL = $totalLeads > 0 ? $totalSpend / $totalLeads : 0;

// Set page title for header
$page_title = 'Meta Dashboard';

// Additional styles for this page
$additional_styles = '
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
    :root{--glass-bg: rgba(255,255,255,0.7);--glass-border: rgba(255,255,255,0.12);}
    html,body{height:100%;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial}
    .glass{background:linear-gradient(180deg, rgba(255,255,255,0.72), rgba(255,255,255,0.6)); border:1px solid var(--glass-border); backdrop-filter: blur(6px);}
    .sidebar{background:linear-gradient(180deg,#0f172a,#0b1220);}
    .accent-gradient{background:linear-gradient(90deg,#7c3aed,#06b6d4);}
    .kpi-num{font-weight:700;font-size:1.25rem}
    .muted{color:#6b7280}
    .card-shadow{box-shadow:0 10px 30px rgba(2,6,23,0.12)}
    body { background: linear-gradient(to bottom right, #f8fafc, #e2e8f0) !important; }
    
    /* Mobile Responsive Design */
    @media (max-width: 768px) {
        .max-w-7xl { padding: 0.5rem !important; }
        .container { padding-left: 1rem !important; padding-right: 1rem !important; }
        h1 { font-size: 1.5rem !important; }
        h2 { font-size: 1.25rem !important; }
        h3 { font-size: 1.125rem !important; }
        .grid { grid-template-columns: 1fr !important; gap: 0.75rem !important; }
        .glass { padding: 1rem !important; }
        .text-4xl { font-size: 1.5rem !important; }
        .text-3xl { font-size: 1.25rem !important; }
        .text-2xl { font-size: 1.125rem !important; }
        .py-6 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        .p-6 { padding: 1rem !important; }
        .mb-6 { margin-bottom: 1rem !important; }
        .gap-6 { gap: 0.75rem !important; }
        canvas { height: 200px !important; }
        table { font-size: 0.875rem; }
        th, td { padding: 0.5rem !important; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; }
    }
    
    @media (max-width: 480px) {
        .max-w-7xl { padding: 0.25rem !important; }
        .container { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        h1 { font-size: 1.25rem !important; }
        .glass { padding: 0.75rem !important; }
        .text-4xl { font-size: 1.25rem !important; }
        .py-6 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
        .p-6 { padding: 0.75rem !important; }
        table { font-size: 0.75rem; }
        th, td { padding: 0.25rem !important; }
        .kpi-num { font-size: 1rem !important; }
    }
</style>
';

// Include common header
include 'includes/header.php';
?>


    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-white text-center">
                <i class="fab fa-facebook mr-3"></i>
                Meta Dashboard
            </h1>
            <p class="text-white text-center mt-2 opacity-90">
                Facebook & Instagram Ads and Pages Analytics
            </p>
            
        </div>
    </div>

  <!-- PAGE CONTENT -->
  <main class="max-w-7xl mx-auto p-6">

    <!-- FILTERS PANEL -->
    <div class="mb-6">
      <form method="get" id="filters" class="glass p-4 rounded-xl grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 card-shadow">
        <div class="col-span-1 md:col-span-1 lg:col-span-2">
          <label class="text-xs muted">Ad Account</label>
          <select name="ad_account" class="w-full rounded-md p-2 border">
            <?php foreach ($AD_ACCOUNTS as $id => $label): ?>
              <option value="<?= h($id) ?>" <?= $id===$ad ? 'selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="text-xs muted">Date Preset</label>
          <select name="date_preset" class="w-full rounded-md p-2 border">
            <?php
            $presets = ['last_7d'=>'Last 7 Days','last_14d'=>'Last 14 Days','last_30d'=>'Last 30 Days','this_month'=>'This Month','last_month'=>'Last Month','lifetime'=>'Lifetime'];
            foreach ($presets as $key=>$label) {
              $sel = $date_preset===$key && !$use_time_range ? 'selected':'';
              echo "<option value='".h($key)."' $sel>".h($label)."</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-span-1 md:col-span-1 lg:col-span-2 grid grid-cols-2 gap-2">
          <div>
            <label class="text-xs muted">Since</label>
            <input name="since" type="date" value="<?= h($since_in ?? '') ?>" class="w-full rounded-md p-2 border" />
          </div>
          <div>
            <label class="text-xs muted">Until</label>
            <input name="until" type="date" value="<?= h($until_in ?? '') ?>" class="w-full rounded-md p-2 border" />
          </div>
        </div>

        <div class="col-span-1 md:col-span-1 lg:col-span-1 flex items-end">
          <button class="w-full py-2 rounded-lg accent-gradient text-white font-semibold">Apply Filters</button>
        </div>
      </form>
    </div>

    <!-- KPI HERO SECTION -->
    <section id="overview" class="mb-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
      <div class="lg:col-span-8 bg-white glass rounded-2xl p-5 card-shadow">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold">Meta Campaign Snapshot</h2>
            <div class="muted text-sm">Facebook & Instagram advertising performance</div>
          </div>
          <div class="text-right">
            <div class="text-xs muted">Updated</div>
            <div class="text-sm font-medium"><?= date('Y-m-d H:i') ?></div>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
          <div class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border">
            <div class="text-xs muted">Total Spend</div>
            <div class="kpi-num text-blue-600">₹<?= number_format($totalSpend,2) ?></div>
          </div>
          <div class="p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border">
            <div class="text-xs muted">Impressions</div>
            <div class="kpi-num text-indigo-600"><?= number_format($totalImpressions) ?></div>
          </div>
          <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 border">
            <div class="text-xs muted">Clicks</div>
            <div class="kpi-num text-emerald-600"><?= number_format($totalClicks) ?></div>
          </div>
          <div class="p-4 rounded-xl bg-gradient-to-br from-fuchsia-50 to-fuchsia-100 border">
            <div class="text-xs muted">Leads</div>
            <div class="kpi-num text-fuchsia-600"><?= number_format($totalLeads) ?></div>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="text-sm muted">Avg CPL <div class="font-semibold">₹<?= number_format($avgCPL,2) ?></div></div>
          <div class="text-sm muted">Avg CPC <div class="font-semibold">₹<?= number_format($avgCPC,2) ?></div></div>
          <div class="text-sm muted">Avg CPM <div class="font-semibold">₹<?= number_format($avgCPM,2) ?> • CTR <?= number_format($avgCTR,2) ?>%</div></div>
        </div>
      </div>

      <!-- System Status Cards -->
      <aside class="lg:col-span-4 space-y-4">

        <div class="bg-white glass rounded-2xl p-4 card-shadow">
          <div class="text-xs muted">Meta Integration</div>
          <div class="mt-2 font-semibold"><?= count($AD_ACCOUNTS) ?> Ad Accounts</div>
          <div class="text-sm muted mt-1"><?= count($pages) ?> Pages Connected</div>
        </div>
      </aside>
    </section>

    <!-- ADS INSIGHTS SECTION -->
    <section id="ads" class="mb-8">
      <h2 class="text-2xl font-bold mb-6"><i class="fab fa-facebook mr-2 text-blue-600"></i>Meta Ads Insights</h2>
      
      <?php if (!empty($campaigns)): ?>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 card-shadow">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Spend by Campaign</h3>
            <div class="text-sm muted">Hover for details</div>
          </div>
          <canvas id="spendChart" height="120"></canvas>
        </div>

        <div class="bg-white rounded-2xl p-6 card-shadow">
          <h3 class="text-lg font-semibold mb-3">Performance Metrics</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm muted">Active Campaigns</span>
              <span class="font-semibold"><?= count($campaigns) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm muted">Total Spend</span>
              <span class="font-semibold">₹<?= number_format($totalSpend,2) ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm muted">Avg CTR</span>
              <span class="font-semibold"><?= number_format($avgCTR,2) ?>%</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-4 card-shadow">
        <h3 class="text-lg font-semibold mb-3">Campaign Performance</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-left text-xs muted">
              <tr>
                <th class="p-3">Campaign</th>
                <th class="p-3">Status</th>
                <th class="p-3">Objective</th>
                <th class="p-3 text-right">Spend</th>
                <th class="p-3 text-right">Impressions</th>
                <th class="p-3 text-right">Clicks</th>
                <th class="p-3 text-right">CTR%</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Create insights lookup by campaign_id
              $insightsByCampaign = [];
              foreach ($insights as $insight) {
                $insightsByCampaign[$insight['campaign_id']] = $insight;
              }
              
              foreach ($campaigns as $c): 
                $cid = $c['id'];
                $insight = $insightsByCampaign[$cid] ?? [];
              ?>
                <tr class="hover:bg-slate-50">
                  <td class="p-3 font-medium"><?= h($c['name']) ?></td>
                  <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs <?= $c['status'] === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                      <?= h($c['status']) ?>
                    </span>
                  </td>
                  <td class="p-3"><?= h($c['objective']) ?></td>
                  <td class="p-3 text-right">₹<?= number_format((float)g($insight, 'spend', 0), 2) ?></td>
                  <td class="p-3 text-right"><?= number_format((int)g($insight, 'impressions', 0)) ?></td>
                  <td class="p-3 text-right"><?= number_format((int)g($insight, 'clicks', 0)) ?></td>
                  <td class="p-3 text-right"><?= number_format((float)g($insight, 'ctr', 0), 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php else: ?>
      <div class="bg-white rounded-2xl p-12 text-center card-shadow">
        <div class="text-6xl text-gray-400 mb-4">
          <i class="fab fa-facebook"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Campaign Data Available</h3>
        <p class="text-gray-500 mb-4">
          <?php if (!$ACCESS_TOKEN): ?>
            Meta API access token not configured.
          <?php else: ?>
            Unable to fetch Meta Ads data. This could be due to:
          <?php endif; ?>
        </p>
        
        <?php if ($ACCESS_TOKEN): ?>
        <div class="text-left max-w-md mx-auto">
          <ul class="text-sm text-gray-600 space-y-1">
            <li>• API token may have expired</li>
            <li>• Ad account permissions</li>
            <li>• Network connectivity issues</li>
            <li>• Rate limiting</li>
          </ul>
          
          <?php if (isset($campaignsResp['error'])): ?>
          <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-left">
            <p class="text-sm text-red-700"><strong>API Error:</strong></p>
            <p class="text-xs text-red-600"><?= h($campaignsResp['error']['message'] ?? 'Unknown error') ?></p>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="mt-6 space-x-3">
          <a href="?refresh=1" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-sync-alt mr-2"></i>Retry Connection
          </a>
          <a href="?demo=1" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-eye mr-2"></i>View Demo Data
          </a>
        </div>
      </div>
      <?php endif; ?>
    </section>

    <!-- PAGES INSIGHTS SECTION -->
    <section id="pages" class="mb-12">
      <h2 class="text-2xl font-bold mb-6"><i class="fas fa-users mr-2 text-purple-600"></i>Facebook Pages Insights</h2>

      <?php if (!empty($allData)): ?>
      <div x-data="{ activeTab: '<?= $allData[0]['id'] ?? '' ?>' }">
        <!-- Tabs -->
        <div class="flex space-x-3 border-b border-gray-200 mb-6 overflow-x-auto">
          <?php foreach($allData as $page): ?>
            <button
              @click="activeTab = '<?= $page['id'] ?>'"
              :class="activeTab === '<?= $page['id'] ?>'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
              class="whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm transition">
              <?= htmlspecialchars($page['name']) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Tab Content -->
        <?php foreach($allData as $page): ?>
        <div
          x-show="activeTab === '<?= $page['id'] ?>'"
          x-transition
          class="bg-white rounded-xl card-shadow p-6 mb-8">

          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($page['name']) ?></h3>
              <?php if($page['about']): ?><p class="text-sm text-gray-500"><?= htmlspecialchars($page['about']) ?></p><?php endif; ?>
            </div>
            <div class="text-right">
              <p class="text-xs text-gray-400">Followers</p>
              <p class="text-3xl font-extrabold text-indigo-600"><?= number_format($page['total_followers']) ?></p>
            </div>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <div class="bg-blue-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition">
              <h4 class="text-sm text-gray-500">Reach (28d)</h4>
              <p class="text-xl font-bold text-gray-800"><?= $page['reach'] ?></p>
            </div>
            <div class="bg-green-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition">
              <h4 class="text-sm text-gray-500">Engaged</h4>
              <p class="text-xl font-bold text-gray-800"><?= $page['engaged'] ?></p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition">
              <h4 class="text-sm text-gray-500">Engagement Rate</h4>
              <p class="text-xl font-bold text-gray-800"><?= $page['engagement_rate'] ?>%</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition">
              <h4 class="text-sm text-gray-500">Page Views</h4>
              <p class="text-xl font-bold text-gray-800"><?= $page['page_views'] ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="bg-white rounded-2xl p-12 text-center card-shadow">
        <div class="text-6xl text-gray-400 mb-4">
          <i class="fas fa-users"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Page Data</h3>
        <p class="text-gray-500 mb-6">Unable to fetch Facebook Pages data. Check API configuration.</p>
      </div>
      <?php endif; ?>
    </section>


  </main>

<script>
  // Toggle filters panel
  document.getElementById('toggleFilters')?.addEventListener('click', function(){
    const el = document.getElementById('filters');
    el.classList.toggle('hidden');
  });

  // Chart.js configuration
  const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
  const spends = <?= json_encode($spends) ?>;

  Chart.defaults.color = '#0f172a';
  Chart.defaults.font.family = 'Inter, system-ui';

  if (document.getElementById('spendChart') && labels.length > 0) {
    new Chart(document.getElementById('spendChart'), {
      type: 'bar',
      data: { 
        labels, 
        datasets: [{ 
          label: 'Spend (₹)', 
          data: spends, 
          backgroundColor: '#6366f1',
          borderRadius: 8 
        }] 
      },
      options: { 
        responsive: true, 
        scales: { y: { beginAtZero: true } }, 
        plugins: { legend: { display: false } } 
      }
    });
  }
</script>

<?php
// Additional scripts for this page
$additional_scripts = '
<script>
    // Meta Dashboard specific JavaScript is already included above
</script>
';

// Include common footer
include 'includes/footer.php';
?>
