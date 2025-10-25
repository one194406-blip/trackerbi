<?php
require_once 'AudioAnalyzer.php';
require_once 'FilenameParser.php';

$analyzer = new AudioAnalyzer();
$results = null;
$processing = false;
$debug_info = [];

// Check if form was submitted at all
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_info['form_submitted'] = true;
    
    // Check for both single file and multiple files
    $single_file_uploaded = isset($_FILES['audio_file']) && !empty($_FILES['audio_file']['name']);
    $multiple_files_uploaded = isset($_FILES['audio_files']) && !empty($_FILES['audio_files']['name'][0]);
    
    $debug_info['single_file_exist'] = $single_file_uploaded;
    $debug_info['multiple_files_exist'] = $multiple_files_uploaded;
    
    if ($single_file_uploaded) {
        // Handle single file upload
        $processing = true;
        $debug_info['upload_type'] = 'single';
        $debug_info['file_info'] = $_FILES['audio_file'];
        
        error_log("Single file upload info: " . print_r($_FILES['audio_file'], true));
        
        try {
            $results = $analyzer->processAudio($_FILES['audio_file']);
            $debug_info['results_generated'] = true;
            error_log("Single file processed successfully");
        } catch (Exception $e) {
            error_log("Exception processing single file: " . $e->getMessage());
            $results = [
                'errors' => ['Exception: ' . $e->getMessage()],
                'upload' => ['success' => false, 'error' => $e->getMessage()]
            ];
            $debug_info['exception'] = $e->getMessage();
        }
    } elseif ($multiple_files_uploaded) {
        // Handle multiple files upload
        $processing = true;
        $debug_info['upload_type'] = 'multiple';
        $debug_info['file_info'] = $_FILES['audio_files'];
        
        // Debug: Check file upload
        error_log("Multiple files upload info: " . print_r($_FILES['audio_files'], true));
        
        $results = [
            'files_processed' => [],
            'total_files' => count($_FILES['audio_files']['name']),
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        // Process each file
        for ($i = 0; $i < count($_FILES['audio_files']['name']); $i++) {
            if ($_FILES['audio_files']['error'][$i] === UPLOAD_ERR_OK) {
                // Create individual file array for processing
                $individual_file = [
                    'name' => $_FILES['audio_files']['name'][$i],
                    'type' => $_FILES['audio_files']['type'][$i],
                    'tmp_name' => $_FILES['audio_files']['tmp_name'][$i],
                    'error' => $_FILES['audio_files']['error'][$i],
                    'size' => $_FILES['audio_files']['size'][$i]
                ];
                
                try {
                    $file_result = $analyzer->processAudio($individual_file);
                    $file_result['filename'] = $_FILES['audio_files']['name'][$i];
                    $file_result['file_index'] = $i + 1;
                    $results['files_processed'][] = $file_result;
                    $results['successful']++;
                    
                    error_log("File " . ($i + 1) . " processed successfully: " . $_FILES['audio_files']['name'][$i]);
                } catch (Exception $e) {
                    error_log("Exception processing file " . ($i + 1) . ": " . $e->getMessage());
                    $results['files_processed'][] = [
                        'filename' => $_FILES['audio_files']['name'][$i],
                        'file_index' => $i + 1,
                        'errors' => ['Exception: ' . $e->getMessage()],
                        'upload' => ['success' => false, 'error' => $e->getMessage()]
                    ];
                    $results['failed']++;
                    $results['errors'][] = "File " . ($i + 1) . " (" . $_FILES['audio_files']['name'][$i] . "): " . $e->getMessage();
                }
            } else {
                $results['files_processed'][] = [
                    'filename' => $_FILES['audio_files']['name'][$i],
                    'file_index' => $i + 1,
                    'errors' => ['Upload error: ' . $_FILES['audio_files']['error'][$i]],
                    'upload' => ['success' => false, 'error' => 'Upload failed']
                ];
                $results['failed']++;
                $results['errors'][] = "File " . ($i + 1) . " (" . $_FILES['audio_files']['name'][$i] . "): Upload failed";
            }
        }
        
        $debug_info['results_generated'] = true;
        error_log("Multiple files processed. Results summary: " . $results['successful'] . " successful, " . $results['failed'] . " failed");
    } else {
        $debug_info['no_file'] = true;
    }
} else {
    $debug_info['form_submitted'] = false;
}

// Set page title for header
$page_title = 'Audio Analysis System';

// Add mobile-specific styles
$additional_styles = '
<style>
/* Mobile-First Responsive Design for Audio Analysis */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    /* Form adjustments */
    .glass-card {
        padding: 1.5rem !important;
    }
    
    .upload-area {
        padding: 2rem 1rem !important;
        min-height: 150px !important;
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
    
    .grid.md\\:grid-cols-6 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
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
    
    /* Button adjustments */
    .btn-primary {
        width: 100% !important;
        padding: 1rem !important;
        font-size: 1.1rem !important;
    }
    
    /* Progress bars */
    .progress-bar {
        height: 8px !important;
    }
    
    /* Results sections */
    .results-section {
        margin-bottom: 2rem !important;
    }
    
    .results-section h4 {
        font-size: 1.25rem !important;
    }
    
    /* Performance metrics */
    .performance-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .metric-card {
        padding: 1rem !important;
    }
    
    /* File upload styling */
    .file-input-wrapper {
        margin-bottom: 1.5rem !important;
    }
    
    input[type="file"] {
        width: 100% !important;
        padding: 1rem !important;
        font-size: 1rem !important;
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
}

@media (max-width: 480px) {
    .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .glass-card {
        padding: 1rem !important;
    }
    
    .upload-area {
        padding: 1.5rem 0.75rem !important;
        min-height: 120px !important;
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
    .grid.md\\:grid-cols-6 {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
    
    .metric-card {
        padding: 0.75rem !important;
    }
    
    .btn-primary {
        padding: 0.875rem !important;
        font-size: 1rem !important;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    button, .btn, input[type="file"] {
        min-height: 44px !important;
        padding: 12px 16px !important;
    }
    
    input, select, textarea {
        min-height: 44px !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
    
    .upload-area {
        min-height: 120px !important;
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
    
    .upload-area {
        min-height: 100px !important;
    }
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
                <i class="fas fa-microphone mr-4 icon-accent"></i>
                Audio Analysis System
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Advanced AI-powered audio transcription, translation, and sentiment analysis
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <!-- Upload Form -->
        <div class="max-w-3xl mx-auto mb-12">
            <div class="glass-card rounded-2xl p-10">
                <h2 class="text-3xl font-semibold mb-8 text-primary text-center">
                    <i class="fas fa-upload mr-3 icon-accent"></i>
                    Upload Multiple Audio Files
                </h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <!-- Upload Options -->
                    <div class="mb-8">
                        <div class="text-center mb-6">
                            <i class="fas fa-cloud-upload-alt text-6xl icon-accent mb-4"></i>
                            <h3 class="text-xl font-semibold text-primary mb-2">Choose Upload Method</h3>
                            <p class="text-secondary">Select single file or multiple files for batch processing</p>
                        </div>
                        
                        <!-- Upload Mode Selection -->
                        <div class="flex justify-center gap-4 mb-6">
                            <button type="button" onclick="setUploadMode('single')" id="singleModeBtn" 
                                    class="px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 transition-colors">
                                <i class="fas fa-file mr-2"></i>
                                Single File
                            </button>
                            <button type="button" onclick="setUploadMode('multiple')" id="multipleModeBtn" 
                                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-400 transition-colors">
                                <i class="fas fa-files mr-2"></i>
                                Multiple Files
                            </button>
                        </div>
                        
                        <!-- Single File Upload -->
                        <div id="singleFileUpload" class="upload-area p-6 text-center border-2 border-dashed border-gray-300 rounded-xl">
                            <div class="mb-4">
                                <i class="fas fa-file-audio text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600 mb-2">Upload one audio file</p>
                            </div>
                            <input type="file" id="single_audio_file" name="audio_file" accept="audio/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        
                        <!-- Multiple Files Upload -->
                        <div id="multipleFileUpload" class="upload-area p-6 text-center border-2 border-dashed border-gray-300 rounded-xl hidden">
                            <div class="mb-4">
                                <i class="fas fa-files text-4xl text-green-500 mb-3"></i>
                                <p class="text-gray-700 mb-2 font-semibold">Upload Multiple Audio Files</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                                    <p class="text-sm text-yellow-800 font-medium mb-1">📋 How to select multiple files:</p>
                                    <ul class="text-xs text-yellow-700 text-left space-y-1">
                                        <li>• <strong>Windows:</strong> Hold <kbd class="bg-white px-1 rounded">Ctrl</kbd> key while clicking each file</li>
                                        <li>• <strong>Mac:</strong> Hold <kbd class="bg-white px-1 rounded">Cmd</kbd> key while clicking each file</li>
                                        <li>• <strong>Range:</strong> Click first file, then <kbd class="bg-white px-1 rounded">Shift</kbd> + click last file</li>
                                    </ul>
                                </div>
                            </div>
                            <input type="file" id="multiple_audio_files" name="audio_files[]" accept="audio/*" multiple="multiple" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer">
                            <p class="text-xs text-gray-500 mt-2">Supported: MP3, WAV, M4A, AAC, OGG, WebM, FLAC (Max 50MB each)</p>
                        </div>
                        
                        <!-- Selected Files Preview -->
                        <div id="selectedFiles" class="mt-4 hidden">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Selected Files:</h4>
                            <div id="filesList" class="space-y-2"></div>
                        </div>
                        
                        <div class="mt-6 flex flex-wrap justify-center gap-2 text-xs text-secondary">
                            <span class="px-3 py-1 bg-gray-100 rounded-full">MP3</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">WAV</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">M4A</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">AAC</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">OGG</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">WebM</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full">FLAC</span>
                        </div>
                        <p class="text-xs text-secondary mt-3">Maximum file size: 50MB per file | Select multiple files at once</p>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" 
                                class="btn-primary flex-1 text-white font-semibold py-4 px-8 rounded-xl text-lg"
                                <?php echo $processing ? 'disabled' : ''; ?>>
                            <?php if ($processing): ?>
                                <div class="flex items-center justify-center">
                                    <div class="loading-spinner mr-3"></div>
                                    Processing Audio...
                                </div>
                            <?php else: ?>
                                <i class="fas fa-magic mr-3"></i>
                                Analyze Audio
                            <?php endif; ?>
                        </button>
                        
                        <button type="button" 
                                onclick="cancelUpload()" 
                                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-4 px-8 rounded-xl text-lg transition-colors duration-300"
                                <?php echo $processing ? 'disabled' : ''; ?>>
                            <i class="fas fa-times mr-3"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Debug Information -->
        <?php if (!empty($debug_info) && isset($debug_info['form_submitted'])): ?>
        <div class="max-w-7xl mx-auto mb-8">
            <div class="glass-card rounded-2xl p-6 border-l-4 border-yellow-400">
                <h3 class="text-lg font-semibold mb-4 text-yellow-700">
                    <i class="fas fa-bug mr-2"></i>
                    Debug Information
                </h3>
                <div class="text-sm space-y-2">
                    <p><strong>Form Submitted:</strong> <?php echo $debug_info['form_submitted'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Single File Exists:</strong> <?php echo isset($debug_info['single_file_exist']) ? ($debug_info['single_file_exist'] ? 'Yes' : 'No') : 'Not checked'; ?></p>
                    <p><strong>Multiple Files Exist:</strong> <?php echo isset($debug_info['multiple_files_exist']) ? ($debug_info['multiple_files_exist'] ? 'Yes' : 'No') : 'Not checked'; ?></p>
                    <p><strong>Upload Type:</strong> <?php echo isset($debug_info['upload_type']) ? $debug_info['upload_type'] : 'None detected'; ?></p>
                    <p><strong>Processing:</strong> <?php echo $processing ? 'Yes' : 'No'; ?></p>
                    <?php if (isset($debug_info['file_info'])): ?>
                    <p><strong>File Info:</strong></p>
                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto"><?php print_r($debug_info['file_info']); ?></pre>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Results Section -->
        <?php if ($results): ?>
        <div class="max-w-7xl mx-auto result-card space-y-8">
            <!-- Multiple Files Summary -->
            <?php if (isset($results['total_files']) && $results['total_files'] > 1): ?>
            <div class="glass-card rounded-2xl p-6 border-l-4 border-blue-400">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-files-audio mr-2 icon-accent"></i>
                    Multiple Files Processing Summary
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo $results['total_files']; ?></div>
                        <div class="text-sm text-blue-700">Total Files</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo $results['successful']; ?></div>
                        <div class="text-sm text-green-700">Successful</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600"><?php echo $results['failed']; ?></div>
                        <div class="text-sm text-red-700">Failed</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Global Error Messages -->
            <?php if (!empty($results['errors'])): ?>
            <div class="glass-card rounded-2xl p-6 border-l-4 border-red-400">
                <h3 class="font-semibold mb-4 text-red-700 text-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Processing Errors
                </h3>
                <ul class="space-y-2">
                    <?php foreach ($results['errors'] as $error): ?>
                    <li class="text-red-600 flex items-start">
                        <i class="fas fa-times-circle mr-2 mt-1 text-sm"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Individual File Results -->
            <?php if (isset($results['files_processed'])): ?>
            <?php foreach ($results['files_processed'] as $file_result): ?>
            <div class="glass-card rounded-2xl p-6 border-l-4 <?php echo (!empty($file_result['errors'])) ? 'border-red-400' : 'border-green-400'; ?>">
                <?php 
                // Parse filename for structured display
                $parsedFilename = FilenameParser::parseFilename($file_result['filename']);
                $displayInfo = FilenameParser::formatForDisplay($parsedFilename);
                ?>
                
                <div class="mb-4">
                    <h3 class="text-xl font-semibold mb-2 text-primary">
                        <i class="fas fa-file-audio mr-2 icon-accent"></i>
                        File <?php echo $file_result['file_index']; ?>: <?php echo htmlspecialchars($displayInfo['display_name']); ?>
                    </h3>
                    
                    <?php if ($displayInfo['structured']): ?>
                    <!-- Structured Filename Information -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Call Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-blue-600 mr-2"></i>
                                <span class="text-gray-600">Phone:</span>
                                <span class="ml-1 font-mono font-semibold text-blue-700"><?php echo htmlspecialchars($displayInfo['phone_number']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-globe text-green-600 mr-2"></i>
                                <span class="text-gray-600">Language:</span>
                                <span class="ml-1 font-semibold text-green-700"><?php echo htmlspecialchars($displayInfo['language']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user text-purple-600 mr-2"></i>
                                <span class="text-gray-600">Name:</span>
                                <span class="ml-1 font-semibold text-purple-700"><?php echo htmlspecialchars($displayInfo['caller_name']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-orange-600 mr-2"></i>
                                <span class="text-gray-600">Date & Time:</span>
                                <span class="ml-1 font-semibold text-orange-700"><?php echo htmlspecialchars($displayInfo['call_datetime']); ?></span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-file mr-1"></i>
                            Original filename: <?php echo htmlspecialchars($file_result['filename']); ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Unstructured Filename Information -->
                    <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-yellow-800 font-medium">Filename Pattern Not Recognized</p>
                                <p class="text-xs text-yellow-700 mt-1"><?php echo htmlspecialchars($displayInfo['details']); ?></p>
                                <p class="text-xs text-gray-600 mt-2">
                                    <strong>Expected pattern:</strong> {phone}_{language}_{name}_{YYYYMMDDHHMMSS}<br>
                                    <strong>Example:</strong> 9080093260_English_Nisarga_20251022164012
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Upload Status for this file -->
                <?php if (isset($file_result['upload'])): ?>
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 text-gray-700">Upload Status</h4>
                    <?php if ($file_result['upload']['success']): ?>
                        <div class="flex items-center p-4 bg-green-50 rounded-xl border border-green-200">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-green-700 font-medium">File uploaded successfully</p>
                                <?php if (isset($file_result['upload']['filename'])): ?>
                                <p class="text-green-600 text-sm">
                                    <?php echo htmlspecialchars($file_result['upload']['filename']); ?>
                                    <?php if (isset($file_result['upload']['size'])): ?>
                                    (<?php echo number_format($file_result['upload']['size'] / 1024, 2); ?> KB)
                                    <?php endif; ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center p-4 bg-red-50 rounded-xl border border-red-200">
                            <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-red-700 font-medium">Upload failed</p>
                                <p class="text-red-600 text-sm"><?php echo htmlspecialchars($file_result['upload']['error']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Display detailed results for successful files -->
                <?php if (empty($file_result['errors']) && isset($file_result['transcription'])): ?>
                    <div class="space-y-6">
                        
                        <!-- Original Transcription -->
                        <?php if (isset($file_result['transcription']) && $file_result['transcription']['success']): ?>
                        <div class="bg-white p-6 rounded-xl border border-gray-200">
                            <h5 class="text-lg font-semibold text-gray-800 mb-3">
                                <i class="fas fa-file-alt mr-2 text-blue-500"></i>
                                Original Transcription
                                <span class="text-sm font-normal text-gray-500 ml-2">(Full Conversation with Speakers & Timestamps)</span>
                            </h5>
                            <div class="bg-gray-50 p-4 rounded-lg max-h-96 overflow-y-auto">
                                <?php 
                                // Display full conversation with speakers and timestamps
                                if (isset($file_result['transcription']['full_conversation'])) {
                                    echo '<div class="space-y-2 font-mono text-sm">';
                                    echo nl2br(htmlspecialchars($file_result['transcription']['full_conversation']));
                                    echo '</div>';
                                } elseif (isset($file_result['transcription']['segments'])) {
                                    echo '<div class="space-y-2">';
                                    foreach ($file_result['transcription']['segments'] as $segment) {
                                        $timestamp = isset($segment['timestamp']) ? $segment['timestamp'] : '[00:00:00]';
                                        $speaker = isset($segment['speaker']) ? $segment['speaker'] : 'Speaker';
                                        $text = isset($segment['text']) ? $segment['text'] : '';
                                        echo '<div class="mb-2">';
                                        echo '<span class="text-blue-600 font-mono text-xs">' . htmlspecialchars($timestamp) . '</span> ';
                                        echo '<span class="font-semibold text-gray-700">' . htmlspecialchars($speaker) . ':</span> ';
                                        echo '<span class="text-gray-700">' . htmlspecialchars($text) . '</span>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    // Fallback to regular text if no structured data available
                                    echo '<p class="text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($file_result['transcription']['text'])) . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- English Translation -->
                        <?php if (isset($file_result['translation']) && $file_result['translation']['success']): ?>
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-200">
                            <h5 class="text-lg font-semibold text-blue-800 mb-3">
                                <i class="fas fa-language mr-2 text-blue-600"></i>
                                English Translation
                                <span class="text-sm font-normal text-blue-600 ml-2">(Full Conversation with Speakers & Timestamps)</span>
                            </h5>
                            <div class="bg-white p-4 rounded-lg border border-blue-100 max-h-96 overflow-y-auto">
                                <?php 
                                // Display full English conversation with speakers and timestamps
                                if (isset($file_result['translation']['full_conversation'])) {
                                    echo '<div class="space-y-2 font-mono text-sm">';
                                    echo nl2br(htmlspecialchars($file_result['translation']['full_conversation']));
                                    echo '</div>';
                                } elseif (isset($file_result['translation']['segments'])) {
                                    echo '<div class="space-y-2">';
                                    foreach ($file_result['translation']['segments'] as $segment) {
                                        $timestamp = isset($segment['timestamp']) ? $segment['timestamp'] : '[00:00:00]';
                                        $speaker = isset($segment['speaker']) ? $segment['speaker'] : 'Speaker';
                                        $text = isset($segment['text']) ? $segment['text'] : '';
                                        echo '<div class="mb-2">';
                                        echo '<span class="text-blue-500 font-mono text-xs">' . htmlspecialchars($timestamp) . '</span> ';
                                        echo '<span class="font-semibold text-blue-800">' . htmlspecialchars($speaker) . ':</span> ';
                                        echo '<span class="text-blue-700">' . htmlspecialchars($text) . '</span>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    // Fallback to regular text if no structured data available
                                    echo '<p class="text-blue-700 leading-relaxed">' . nl2br(htmlspecialchars($file_result['translation']['text'])) . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Conversation Summary -->
                        <?php if (isset($file_result['conversation_summary']) && $file_result['conversation_summary']['success']): ?>
                        <div class="bg-green-50 p-6 rounded-xl border border-green-200">
                            <h5 class="text-lg font-semibold text-green-800 mb-3">
                                <i class="fas fa-comments mr-2 text-green-600"></i>
                                Conversation Summary
                            </h5>
                            <div class="bg-white p-4 rounded-lg border border-green-100">
                                <p class="text-green-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($file_result['conversation_summary']['summary'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Sentiment Analysis & Performance Metrics -->
                        <?php if (isset($file_result['sentiment_analysis']) && $file_result['sentiment_analysis']['success']): ?>
                        <?php $analysis = $file_result['sentiment_analysis']; ?>
                        <div class="bg-purple-50 p-6 rounded-xl border border-purple-200">
                            <h5 class="text-lg font-semibold text-purple-800 mb-4">
                                <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                                Performance Analysis
                            </h5>
                            
                            <!-- Overall Sentiment Score -->
                            <div class="mb-6 p-4 bg-white rounded-lg border border-purple-100">
                                <h6 class="font-semibold text-gray-700 mb-2">Overall Sentiment</h6>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-3 mr-4">
                                        <div class="bg-purple-500 h-3 rounded-full" style="width: <?php echo ($analysis['sentiment_score'] ?? 0) * 100; ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600"><?php echo number_format(($analysis['sentiment_score'] ?? 0) * 100, 1); ?>%</span>
                                </div>
                            </div>
                            
                            <!-- Agent Performance Metrics -->
                            <?php if (isset($analysis['agent_performance'])): ?>
                            <?php $performance = $analysis['agent_performance']; ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php 
                                $metrics = [
                                    'clarity_score' => ['Clarity', 'fas fa-eye', 'blue'],
                                    'empathy_score' => ['Empathy', 'fas fa-heart', 'red'],
                                    'professionalism_score' => ['Professionalism', 'fas fa-user-tie', 'green'],
                                    'call_opening_score' => ['Call Opening', 'fas fa-phone', 'yellow'],
                                    'call_quality_score' => ['Call Quality', 'fas fa-star', 'purple'],
                                    'call_closing_score' => ['Call Closing', 'fas fa-phone-slash', 'indigo']
                                ];
                                foreach ($metrics as $key => $info): 
                                    if (isset($performance[$key])):
                                ?>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <i class="<?php echo $info[1]; ?> text-<?php echo $info[2]; ?>-500 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-700"><?php echo $info[0]; ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="bg-<?php echo $info[2]; ?>-500 h-2 rounded-full" style="width: <?php echo $performance[$key] * 100; ?>%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600"><?php echo number_format($performance[$key] * 100, 1); ?>%</span>
                                    </div>
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Database Storage Status -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h6 class="font-semibold text-gray-700 mb-2">
                                <i class="fas fa-database mr-2 text-gray-600"></i>
                                Database Storage
                            </h6>
                            <?php if (isset($file_result['database_storage']) && $file_result['database_storage']['success']): ?>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span class="text-sm">Successfully stored in database</span>
                                    <?php if (isset($file_result['database_storage']['analysis_id'])): ?>
                                    <span class="text-xs text-gray-500 ml-2">(ID: <?php echo $file_result['database_storage']['analysis_id']; ?>)</span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span class="text-sm">Database storage failed</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                <?php endif; ?>
                
                <!-- Display errors for failed files -->
                <?php if (!empty($file_result['errors'])): ?>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <h5 class="font-semibold text-red-700 mb-2">Processing Errors</h5>
                        <ul class="space-y-1">
                            <?php foreach ($file_result['errors'] as $error): ?>
                            <li class="text-red-600 text-sm flex items-start">
                                <i class="fas fa-times-circle mr-2 mt-1 text-xs"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Legacy single file support -->
            <?php if (!isset($results['files_processed']) && isset($results['upload'])): ?>
                            <p class="text-red-700 font-medium">Upload failed</p>
                            <p class="text-red-600 text-sm"><?php echo htmlspecialchars($results['upload']['error']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Transcription Results -->
            <?php if (isset($results['transcription']) && $results['transcription']['success']): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-file-alt mr-2 icon-accent"></i>
                    Original Transcription
                    <span class="text-sm font-normal text-gray-500 ml-2">(Full Conversation with Timestamps)</span>
                </h3>
                <div class="bg-gray-50 rounded-xl p-6 border max-h-96 overflow-y-auto">
                    <?php 
                    $transcription = $results['transcription']['transcription'];
                    // Split by lines and format each line properly
                    $lines = explode("\n", $transcription);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;
                        
                        // Check if line has timestamp format [HH:MM:SS]
                        if (preg_match('/^\[(\d{2}:\d{2}:\d{2})\]\s*(.*)$/', $line, $matches)) {
                            $timestamp = $matches[1];
                            $content = $matches[2];
                            echo '<div class="mb-2 flex items-start">';
                            echo '<span class="inline-block bg-blue-100 text-blue-800 text-xs font-mono px-2 py-1 rounded mr-3 mt-0.5 min-w-[60px]">' . htmlspecialchars($timestamp) . '</span>';
                            echo '<span class="text-gray-700 flex-1">' . htmlspecialchars($content) . '</span>';
                            echo '</div>';
                        } else {
                            // Regular line without timestamp
                            echo '<div class="mb-2 text-gray-700 ml-16">' . htmlspecialchars($line) . '</div>';
                        }
                    }
                    ?>
                </div>
                
                <!-- Conversation Statistics -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php 
                    $totalLines = count(array_filter(explode("\n", $transcription), 'trim'));
                    $timestampCount = preg_match_all('/\[\d{2}:\d{2}:\d{2}\]/', $transcription);
                    $wordCount = str_word_count(strip_tags($transcription));
                    ?>
                    <div class="bg-white rounded-lg p-3 border text-center">
                        <div class="text-lg font-semibold text-blue-600"><?php echo $timestampCount; ?></div>
                        <div class="text-xs text-gray-500">Timestamped Segments</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border text-center">
                        <div class="text-lg font-semibold text-green-600"><?php echo $wordCount; ?></div>
                        <div class="text-xs text-gray-500">Total Words</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border text-center">
                        <div class="text-lg font-semibold text-purple-600"><?php echo $totalLines; ?></div>
                        <div class="text-xs text-gray-500">Total Lines</div>
                    </div>
                </div>
                
                <!-- Debug: Show raw transcription data -->
                <?php if (isset($_GET['debug'])): ?>
                <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded">
                    <h5 class="font-bold text-yellow-800 mb-2">Debug - Raw Transcription Data:</h5>
                    <pre class="text-xs text-yellow-700 whitespace-pre-wrap"><?php var_export($results['transcription']); ?></pre>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Translation Results -->
            <?php if (isset($results['translation']) && $results['translation']['success']): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-language mr-2 icon-accent"></i>
                    English Translation
                </h3>
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <pre class="whitespace-pre-wrap text-sm text-blue-800 font-mono"><?php echo htmlspecialchars($results['translation']['translation']); ?></pre>
                </div>
                
                <!-- Debug: Show raw translation data -->
                <?php if (isset($_GET['debug'])): ?>
                <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded">
                    <h5 class="font-bold text-yellow-800 mb-2">Debug - Raw Translation Data:</h5>
                    <pre class="text-xs text-yellow-700 whitespace-pre-wrap"><?php var_export($results['translation']); ?></pre>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Conversation Summary -->
            <?php if (isset($results['conversation_summary']) && $results['conversation_summary']['success']): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-clipboard-list mr-2 icon-accent"></i>
                    Conversation Summary & Call Analysis
                </h3>
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <div class="text-sm text-green-800 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($results['conversation_summary']['summary']); ?></div>
                </div>
                
                <!-- Enhanced Call Reason Analysis -->
                <div class="mt-6">
                    <h4 class="text-lg font-medium mb-4 text-gray-700 flex items-center">
                        <i class="fas fa-search mr-2 text-blue-500"></i>
                        Call Reason Analysis
                    </h4>
                    <?php
                    $summary = strtolower($results['conversation_summary']['summary']);
                    $transcription = strtolower($results['transcription']['transcription'] ?? '');
                    $translation = strtolower($results['translation']['translation'] ?? '');
                    
                    // Combine all text for comprehensive analysis
                    $fullText = $summary . ' ' . $transcription . ' ' . $translation;
                    
                    $callReason = 'General';
                    $confidence = 'Medium';
                    $reasoning = 'Based on general conversation patterns';
                    
                    // Enhanced detection logic
                    $queryPatterns = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'can you tell', 'do you know', 'is there', 'are there', 'information', 'details', 'explain', 'clarify', 'understand', 'meaning'];
                    $requestPatterns = ['please', 'need', 'want', 'require', 'help', 'assist', 'provide', 'give', 'send', 'call back', 'contact', 'arrange', 'schedule', 'book', 'reserve', 'order'];
                    $complaintPatterns = ['complain', 'complaint', 'issue', 'problem', 'bad', 'wrong', 'disappointed', 'frustrated', 'angry', 'terrible', 'awful', 'unhappy', 'not satisfied', 'poor service', 'dissatisfied', 'upset', 'annoyed'];
                    
                    $queryScore = 0;
                    $requestScore = 0;
                    $complaintScore = 0;
                    
                    // Count pattern matches
                    foreach ($queryPatterns as $pattern) {
                        $queryScore += substr_count($fullText, $pattern);
                    }
                    foreach ($requestPatterns as $pattern) {
                        $requestScore += substr_count($fullText, $pattern);
                    }
                    foreach ($complaintPatterns as $pattern) {
                        $complaintScore += substr_count($fullText, $pattern);
                    }
                    
                    // Add question mark detection
                    $questionMarks = substr_count($fullText, '?');
                    $queryScore += $questionMarks * 2;
                    
                    // Determine call reason based on scores
                    if ($complaintScore > 0 && $complaintScore >= max($queryScore, $requestScore)) {
                        $callReason = 'Complaint';
                        $confidence = $complaintScore > 2 ? 'High' : 'Medium';
                        $reasoning = "Detected complaint indicators: $complaintScore matches found";
                    } elseif ($queryScore > 0 && $queryScore >= $requestScore) {
                        $callReason = 'Query';
                        $confidence = $queryScore > 3 ? 'High' : 'Medium';
                        $reasoning = "Detected information-seeking patterns: $queryScore matches found" . ($questionMarks > 0 ? ", including $questionMarks questions" : '');
                    } elseif ($requestScore > 0) {
                        $callReason = 'Request';
                        $confidence = $requestScore > 2 ? 'High' : 'Medium';
                        $reasoning = "Detected service request patterns: $requestScore matches found";
                    }
                    
                    $reasonColors = [
                        'Query' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'Request' => 'bg-green-100 text-green-800 border-green-200',
                        'Complaint' => 'bg-red-100 text-red-800 border-red-200',
                        'General' => 'bg-gray-100 text-gray-800 border-gray-200'
                    ];
                    $reasonColor = $reasonColors[$callReason];
                    
                    $confidenceColors = [
                        'High' => 'bg-green-100 text-green-700',
                        'Medium' => 'bg-yellow-100 text-yellow-700',
                        'Low' => 'bg-gray-100 text-gray-700'
                    ];
                    $confidenceColor = $confidenceColors[$confidence];
                    ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Call Reason -->
                        <div class="bg-white rounded-lg p-4 border">
                            <h5 class="font-medium text-gray-700 mb-2">Primary Call Reason</h5>
                            <div class="flex items-center justify-between mb-2">
                                <div class="inline-flex items-center px-3 py-2 rounded-full border <?php echo $reasonColor; ?>">
                                    <i class="fas fa-tag mr-2"></i>
                                    <span class="font-semibold"><?php echo $callReason; ?></span>
                                </div>
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs <?php echo $confidenceColor; ?>">
                                    <?php echo $confidence; ?> Confidence
                                </div>
                            </div>
                            <p class="text-xs text-gray-600"><?php echo $reasoning; ?></p>
                        </div>
                        
                        <!-- Analysis Breakdown -->
                        <div class="bg-white rounded-lg p-4 border">
                            <h5 class="font-medium text-gray-700 mb-3">Detection Breakdown</h5>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-blue-600">Query Indicators:</span>
                                    <span class="text-xs font-mono bg-blue-50 px-2 py-1 rounded"><?php echo $queryScore; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-green-600">Request Indicators:</span>
                                    <span class="text-xs font-mono bg-green-50 px-2 py-1 rounded"><?php echo $requestScore; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-red-600">Complaint Indicators:</span>
                                    <span class="text-xs font-mono bg-red-50 px-2 py-1 rounded"><?php echo $complaintScore; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-purple-600">Questions Found:</span>
                                    <span class="text-xs font-mono bg-purple-50 px-2 py-1 rounded"><?php echo $questionMarks; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sentiment Analysis Results -->
            <?php if (isset($results['sentiment_analysis']) && $results['sentiment_analysis']['success']): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-6 text-primary">
                    <i class="fas fa-chart-line mr-2 icon-accent"></i>
                    Sentiment Analysis & Performance Metrics
                </h3>
                
                <?php $analysis = $results['sentiment_analysis']['analysis']; ?>
                
                <!-- Overall Sentiment Score -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-3 text-gray-700">Overall Sentiment Score</h4>
                    <div class="bg-gradient-to-r from-red-100 via-yellow-100 to-green-100 rounded-xl p-4">
                        <?php 
                        $score = $analysis['sentiment_score']['numerical_score'] ?? 50;
                        $scoreColor = $score >= 70 ? 'text-green-600' : ($score >= 40 ? 'text-yellow-600' : 'text-red-600');
                        ?>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Sentiment Score</span>
                            <span class="<?php echo $scoreColor; ?> font-bold text-lg"><?php echo $score; ?>/100</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-400 via-yellow-400 to-green-400 h-2 rounded-full" style="width: <?php echo $score; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Confidence: <?php echo round(($analysis['sentiment_score']['confidence'] ?? 0.7) * 100); ?>%</p>
                    </div>
                </div>

                <!-- Agent Performance Scores -->
                <?php if (isset($analysis['agent_performance'])): ?>
                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-4 text-gray-700">Agent Performance Metrics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php 
                        $performanceMetrics = [
                            'clarity_score' => ['label' => 'Clarity', 'icon' => 'fas fa-eye'],
                            'empathy_score' => ['label' => 'Empathy', 'icon' => 'fas fa-heart'],
                            'professionalism_score' => ['label' => 'Professionalism', 'icon' => 'fas fa-user-tie'],
                            'call_opening_score' => ['label' => 'Call Opening', 'icon' => 'fas fa-play'],
                            'call_quality_score' => ['label' => 'Call Quality', 'icon' => 'fas fa-star'],
                            'call_closing_score' => ['label' => 'Call Closing', 'icon' => 'fas fa-stop']
                        ];
                        
                        foreach ($performanceMetrics as $key => $metric):
                            $metricScore = $analysis['agent_performance'][$key] ?? 0;
                            $metricColor = $metricScore >= 80 ? 'bg-green-500' : ($metricScore >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                        ?>
                        <div class="bg-white rounded-lg p-4 border shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <i class="<?php echo $metric['icon']; ?> text-blue-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700"><?php echo $metric['label']; ?></span>
                                </div>
                                <span class="font-bold text-gray-800"><?php echo $metricScore; ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="<?php echo $metricColor; ?> h-2 rounded-full transition-all duration-300" style="width: <?php echo $metricScore; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Overall Performance Rating -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-3 text-gray-700">Overall Performance Rating</h4>
                    <?php 
                    $overallRating = $analysis['agent_performance']['overall_performance'] ?? 'good';
                    $ratingColors = [
                        'excellent' => 'bg-green-100 text-green-800 border-green-200',
                        'good' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'needs_improvement' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                    ];
                    $ratingColor = $ratingColors[$overallRating] ?? $ratingColors['good'];
                    ?>
                    <div class="inline-flex items-center px-4 py-2 rounded-full border <?php echo $ratingColor; ?>">
                        <i class="fas fa-award mr-2"></i>
                        <span class="font-semibold capitalize"><?php echo str_replace('_', ' ', $overallRating); ?></span>
                    </div>
                </div>

                <!-- Strengths and Improvements -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Strengths -->
                    <?php if (!empty($analysis['agent_performance']['strengths'])): ?>
                    <div>
                        <h4 class="text-lg font-medium mb-3 text-gray-700 flex items-center">
                            <i class="fas fa-thumbs-up text-green-500 mr-2"></i>
                            Strengths
                        </h4>
                        <ul class="space-y-2">
                            <?php foreach ($analysis['agent_performance']['strengths'] as $strength): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 text-sm"></i>
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($strength); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Areas for Improvement -->
                    <?php if (!empty($analysis['agent_performance']['areas_for_improvement'])): ?>
                    <div>
                        <h4 class="text-lg font-medium mb-3 text-gray-700 flex items-center">
                            <i class="fas fa-arrow-up text-blue-500 mr-2"></i>
                            Areas for Improvement
                        </h4>
                        <ul class="space-y-2">
                            <?php foreach ($analysis['agent_performance']['areas_for_improvement'] as $area): ?>
                            <li class="flex items-start">
                                <i class="fas fa-lightbulb text-blue-500 mr-2 mt-1 text-sm"></i>
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($area); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Recommendations -->
                <?php if (!empty($analysis['agent_performance']['recommendations'])): ?>
                <div class="mt-6">
                    <h4 class="text-lg font-medium mb-3 text-gray-700 flex items-center">
                        <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                        Recommendations
                    </h4>
                    <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                        <ul class="space-y-2">
                            <?php foreach ($analysis['agent_performance']['recommendations'] as $recommendation): ?>
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right text-purple-500 mr-2 mt-1 text-sm"></i>
                                <span class="text-sm text-purple-800"><?php echo htmlspecialchars($recommendation); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Database Storage Status -->
            <?php if (isset($results['database_storage'])): ?>
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">
                    <i class="fas fa-database mr-2 icon-accent"></i>
                    Database Storage
                </h3>
                <?php if ($results['database_storage']['success']): ?>
                    <div class="flex items-center p-4 bg-green-50 rounded-xl border border-green-200">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-700 font-medium">Analysis results stored successfully</p>
                            <p class="text-green-600 text-sm">
                                Analysis ID: <?php echo htmlspecialchars($results['database_storage']['id']); ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center p-4 bg-red-50 rounded-xl border border-red-200">
                        <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-700 font-medium">Database storage failed</p>
                            <p class="text-red-600 text-sm"><?php echo htmlspecialchars($results['database_storage']['error']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>

<?php
// Additional scripts for this page
$additional_scripts = '
<script>
    let currentUploadMode = "single";
    
    // Set upload mode function
    function setUploadMode(mode) {
        currentUploadMode = mode;
        
        const singleBtn = document.getElementById("singleModeBtn");
        const multipleBtn = document.getElementById("multipleModeBtn");
        const singleUpload = document.getElementById("singleFileUpload");
        const multipleUpload = document.getElementById("multipleFileUpload");
        const selectedFiles = document.getElementById("selectedFiles");
        
        if (mode === "single") {
            // Update button styles
            singleBtn.className = "px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 transition-colors";
            multipleBtn.className = "px-6 py-3 bg-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-400 transition-colors";
            
            // Show/hide upload areas
            singleUpload.classList.remove("hidden");
            multipleUpload.classList.add("hidden");
            selectedFiles.classList.add("hidden");
            
            // Clear multiple files input
            document.getElementById("multiple_audio_files").value = "";
        } else {
            // Update button styles
            multipleBtn.className = "px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition-colors";
            singleBtn.className = "px-6 py-3 bg-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-400 transition-colors";
            
            // Show/hide upload areas
            multipleUpload.classList.remove("hidden");
            singleUpload.classList.add("hidden");
            
            // Clear single file input
            document.getElementById("single_audio_file").value = "";
        }
        
        console.log("Upload mode set to:", mode);
    }
    
    // Handle file selection for multiple files
    function handleMultipleFiles(input) {
        const files = input.files;
        const selectedFilesDiv = document.getElementById("selectedFiles");
        const filesList = document.getElementById("filesList");
        
        console.log("Multiple files selected:", files.length);
        console.log("File names:", Array.from(files).map(f => f.name));
        
        if (files.length > 0) {
            selectedFilesDiv.classList.remove("hidden");
            filesList.innerHTML = "";
            
            // Show selection summary with more details
            const summaryDiv = document.createElement("div");
            summaryDiv.className = "mb-4 p-4 bg-green-50 border border-green-200 rounded-lg";
            
            let totalSize = 0;
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
            }
            
            summaryDiv.innerHTML = "<div class=\"text-center\"><i class=\"fas fa-check-circle text-green-500 mr-2\"></i><span class=\"font-bold text-green-700 text-lg\">" + files.length + " file(s) selected successfully!</span><br><span class=\"text-sm text-green-600\">Total size: " + (totalSize / 1024 / 1024).toFixed(2) + " MB</span></div>";
            filesList.appendChild(summaryDiv);
            
            // Display each file with more details
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement("div");
                fileItem.className = "flex items-center justify-between bg-blue-50 p-3 rounded-lg border border-blue-200 mb-2 hover:bg-blue-100 transition-colors";
                
                const fileInfo = document.createElement("div");
                fileInfo.className = "flex items-center flex-1";
                
                const fileType = file.type || "Unknown";
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                fileInfo.innerHTML = "<div class=\"flex items-center\"><i class=\"fas fa-file-audio text-blue-500 mr-3 text-lg\"></i><div><div class=\"text-sm font-medium text-gray-700\">" + file.name + "</div><div class=\"text-xs text-gray-500\">" + fileSize + " MB • " + fileType + "</div></div></div>";
                
                const statusBadge = document.createElement("div");
                statusBadge.className = "px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full";
                statusBadge.textContent = "Ready";
                
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(statusBadge);
                filesList.appendChild(fileItem);
            }
            
            // Add instruction for next step
            const instructionDiv = document.createElement("div");
            instructionDiv.className = "mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-center";
            instructionDiv.innerHTML = "<p class=\"text-sm text-blue-700\"><i class=\"fas fa-arrow-down mr-2\"></i>Click <strong>\"Analyze Audio\"</strong> button below to process all files</p>";
            filesList.appendChild(instructionDiv);
            
        } else {
            selectedFilesDiv.classList.add("hidden");
            console.log("No files selected");
        }
    }
    
    // Handle single file selection
    function handleSingleFile(input) {
        const file = input.files[0];
        console.log("Single file selected:", file ? file.name : "none");
    }
    
    // Test multiple file input function
    function testMultipleFileInput() {
        const input = document.getElementById("multiple_audio_files");
        
        if (!input) {
            alert("❌ Multiple file input not found!");
            return;
        }
        
        // Check if multiple attribute exists
        const hasMultiple = input.hasAttribute("multiple");
        const multipleValue = input.getAttribute("multiple");
        
        // Check if input is visible and enabled
        const isVisible = !input.closest(".hidden");
        const isEnabled = !input.disabled;
        
        let testResults = [];
        testResults.push("🔍 Multiple File Input Test Results:");
        testResults.push("");
        testResults.push("✅ Input Element: Found");
        testResults.push(hasMultiple ? "✅ Multiple Attribute: Present (" + multipleValue + ")" : "❌ Multiple Attribute: Missing");
        testResults.push(isVisible ? "✅ Visibility: Visible" : "❌ Visibility: Hidden");
        testResults.push(isEnabled ? "✅ Status: Enabled" : "❌ Status: Disabled");
        testResults.push("✅ Accept: " + input.accept);
        testResults.push("✅ Name: " + input.name);
        testResults.push("");
        testResults.push("📝 Instructions:");
        testResults.push("1. Click the file input above");
        testResults.push("2. In the file dialog, hold Ctrl (Windows) or Cmd (Mac)");
        testResults.push("3. Click multiple audio files while holding the key");
        testResults.push("4. Click Open");
        testResults.push("");
        testResults.push("If multiple selection still does not work, it might be a browser limitation.");
        
        alert(testResults.join("\n"));
        
        // Also log to console
        console.log("Multiple file input test:", {
            element: input,
            hasMultiple: hasMultiple,
            multipleValue: multipleValue,
            isVisible: isVisible,
            isEnabled: isEnabled,
            accept: input.accept,
            name: input.name
        });
    }
    
    // Cancel upload function
    function cancelUpload() {
        const singleInput = document.getElementById("single_audio_file");
        const multipleInput = document.getElementById("multiple_audio_files");
        const selectedFilesDiv = document.getElementById("selectedFiles");
        
        if (currentUploadMode === "single" && singleInput.files.length > 0) {
            if (confirm("Are you sure you want to clear the selected file?")) {
                singleInput.value = "";
                alert("File cleared successfully!");
            }
        } else if (currentUploadMode === "multiple" && multipleInput.files.length > 0) {
            const fileCount = multipleInput.files.length;
            if (confirm("Are you sure you want to clear all " + fileCount + " selected files?")) {
                multipleInput.value = "";
                selectedFilesDiv.classList.add("hidden");
                alert("All files cleared successfully!");
            }
        } else {
            alert("No files selected to clear.");
        }
    }
    
    // Initialize page
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Audio upload page initialized");
        
        // Set up event listeners
        const multipleInput = document.getElementById("multiple_audio_files");
        const singleInput = document.getElementById("single_audio_file");
        
        if (multipleInput) {
            multipleInput.addEventListener("change", function() {
                handleMultipleFiles(this);
            });
        }
        
        if (singleInput) {
            singleInput.addEventListener("change", function() {
                handleSingleFile(this);
            });
        }
        
        // Set default mode
        setUploadMode("single");
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
