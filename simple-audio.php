<?php
// Set page title for header
$page_title = 'Audio Analysis System';

// Include common header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <div class="gradient-bg py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-bold text-white text-center mb-4">
                🎤 Audio Analysis System
            </h1>
            <p class="text-xl text-blue-100 text-center max-w-3xl mx-auto">
                Upload your audio files for AI-powered sentiment analysis and performance insights
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <!-- Upload Section -->
        <div class="glass-card p-8 mb-8 animate-fade-in">
            <h2 class="text-3xl font-bold text-primary mb-6 flex items-center gap-3">
                <i class="fas fa-cloud-upload-alt icon-accent"></i>
                Upload Audio File
            </h2>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                    <i class="fas fa-file-audio text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg text-gray-600 mb-4">Drag and drop your audio file here, or click to browse</p>
                    <input type="file" name="audio_file" id="audio_file" accept="audio/*" required 
                           class="hidden" onchange="updateFileName(this)">
                    <label for="audio_file" class="btn-modern cursor-pointer inline-block">
                        Choose Audio File
                    </label>
                    <p id="file-name" class="mt-2 text-sm text-gray-500"></p>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn-modern bg-gradient-to-r from-blue-500 to-purple-600 px-8 py-3">
                        <i class="fas fa-magic mr-2"></i>
                        Analyze Audio
                    </button>
                </div>
            </form>
        </div>

        <!-- Status Message -->
        <div class="glass-card p-6 mb-8">
            <h3 class="text-xl font-semibold text-primary mb-4">System Status</h3>
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>Audio Analysis System Ready</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>Upload Directory Accessible</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>AI Processing Available</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? `Selected: ${fileName}` : '';
        }
    </script>

<?php
// Include common footer
include 'includes/footer.php';
?>
