<?php
require_once 'includes/header.php';
require_once 'DatabaseManager.php';

// Get database statistics
$db = new DatabaseManager();
$stats = $db->getQuickStats();
?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-rocket text-primary me-2"></i>TrackerBI Features
            </h1>
            <p class="text-muted mb-0">Comprehensive AI-powered audio analysis platform</p>
        </div>
        <div class="text-end">
            <span class="badge bg-success fs-6">All Systems Operational</span>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-chart-line me-2"></i>Quick Stats
                </h4>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card bg-primary">
                            <div class="stat-icon">
                                <i class="fas fa-microphone"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['total_analyses'] ?? 0); ?></h3>
                                <p>Total Audio Analyses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card bg-success">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['today_analyses'] ?? 0); ?></h3>
                                <p>Today's Analyses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card bg-info">
                            <div class="stat-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo number_format($stats['avg_sentiment'] ?? 0, 1); ?>%</h3>
                                <p>Avg Sentiment Score</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card bg-warning">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $stats['last_analysis'] ?? 'N/A'; ?></h3>
                                <p>Last Analysis</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Features -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h4 class="text-primary mb-4">
                    <i class="fas fa-star me-2"></i>Core Features
                </h4>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item">
                            <div class="feature-icon bg-primary">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h5>AI Audio Analysis</h5>
                            <p>Advanced sentiment analysis, emotion detection, and conversation intelligence powered by Google Gemini 2.0</p>
                            <div class="feature-tags">
                                <span class="badge bg-light text-dark">Gemini 2.0</span>
                                <span class="badge bg-light text-dark">Real-time</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item">
                            <div class="feature-icon bg-success">
                                <i class="fas fa-language"></i>
                            </div>
                            <h5>Multi-language Support</h5>
                            <p>Automatic transcription and translation with high accuracy processing for global business needs</p>
                            <div class="feature-tags">
                                <span class="badge bg-light text-dark">50+ Languages</span>
                                <span class="badge bg-light text-dark">Auto-detect</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-item">
                            <div class="feature-icon bg-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>Performance Tracking</h5>
                            <p>Comprehensive agent scoring with 6 key metrics: Clarity, Empathy, Professionalism, Opening, Quality, Closing</p>
                            <div class="feature-tags">
                                <span class="badge bg-light text-dark">6 Metrics</span>
                                <span class="badge bg-light text-dark">Detailed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Features -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-tachometer-alt me-2"></i>Analytics Dashboards
                </h4>
                <ul class="feature-list">
                    <li><i class="fas fa-check text-success me-2"></i>Real-time Performance Trends</li>
                    <li><i class="fas fa-check text-success me-2"></i>Hourly Analysis Charts</li>
                    <li><i class="fas fa-check text-success me-2"></i>Call Structure Breakdown</li>
                    <li><i class="fas fa-check text-success me-2"></i>Sentiment Analysis Visualization</li>
                    <li><i class="fas fa-check text-success me-2"></i>Advanced Search & Filtering</li>
                    <li><i class="fas fa-check text-success me-2"></i>Export & Reporting Tools</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-shield-alt me-2"></i>Security & Management
                </h4>
                <ul class="feature-list">
                    <li><i class="fas fa-check text-success me-2"></i>Role-based Access Control</li>
                    <li><i class="fas fa-check text-success me-2"></i>Secure Authentication System</li>
                    <li><i class="fas fa-check text-success me-2"></i>Admin Panel for User Management</li>
                    <li><i class="fas fa-check text-success me-2"></i>Audit Logs & Activity Tracking</li>
                    <li><i class="fas fa-check text-success me-2"></i>Data Encryption & Privacy</li>
                    <li><i class="fas fa-check text-success me-2"></i>Backup & Recovery Systems</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Technical Specifications -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h4 class="text-primary mb-4">
                    <i class="fas fa-cogs me-2"></i>Technical Specifications
                </h4>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="tech-spec">
                            <h6 class="text-muted">AI Engine</h6>
                            <p class="mb-0">Google Gemini 2.0 Flash</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="tech-spec">
                            <h6 class="text-muted">Audio Formats</h6>
                            <p class="mb-0">MP3, WAV, M4A, AAC, OGG, FLAC</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="tech-spec">
                            <h6 class="text-muted">Max File Size</h6>
                            <p class="mb-0">50MB per upload</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="tech-spec">
                            <h6 class="text-muted">Database</h6>
                            <p class="mb-0">MySQL with UTF-8 support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="glass-card p-4">
                <h4 class="text-primary mb-3">Get Started with TrackerBI</h4>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="trackerbi-audio.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-microphone me-2"></i>Try Audio Analysis
                    </a>
                    <a href="analytics-dashboard.php" class="btn btn-success btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>View Analytics
                    </a>
                    <a href="call-dashboard.php" class="btn btn-info btn-lg">
                        <i class="fas fa-phone me-2"></i>Call Dashboard
                    </a>
                    <a href="meta-dashboard.php" class="btn btn-warning btn-lg">
                        <i class="fas fa-chart-pie me-2"></i>Meta Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    border-radius: 15px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.stat-content p {
    margin: 0;
    opacity: 0.9;
}

.feature-item {
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    color: white;
}

.feature-tags {
    margin-top: 10px;
}

.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.feature-list li:last-child {
    border-bottom: none;
}

.tech-spec {
    padding: 15px;
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    text-align: center;
}

.tech-spec h6 {
    font-weight: 600;
    margin-bottom: 5px;
}
</style>

<?php require_once 'includes/footer.php'; ?>
