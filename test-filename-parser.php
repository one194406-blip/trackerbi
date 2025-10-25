<?php
require_once 'FilenameParser.php';

// Set page title for header
$page_title = 'Filename Parser Test';

// Include common header
include 'includes/header.php';
?>

<div class="gradient-bg py-16">
    <div class="container mx-auto px-6">
        <h1 class="text-5xl font-bold text-white text-center mb-4">
            <i class="fas fa-cogs mr-4 icon-accent"></i>
            Filename Parser Test
        </h1>
        <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
            Test the filename parsing functionality for structured audio file names
        </p>
    </div>
</div>

<div class="container mx-auto px-6 py-12">
    <div class="max-w-6xl mx-auto">
        
        <!-- Pattern Information -->
        <div class="glass-card rounded-2xl p-8 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-info-circle mr-3 icon-accent"></i>
                Expected Filename Pattern
            </h2>
            
            <?php $patternInfo = FilenameParser::getPatternDescription(); ?>
            
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Pattern Structure</h3>
                <div class="font-mono text-lg text-blue-700 mb-4 bg-white p-3 rounded border">
                    <?php echo htmlspecialchars($patternInfo['pattern']); ?>
                </div>
                <div class="font-mono text-sm text-green-700 bg-green-50 p-3 rounded border border-green-200">
                    <strong>Example:</strong> <?php echo htmlspecialchars($patternInfo['example']); ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Components</h4>
                    <ul class="space-y-2">
                        <?php foreach ($patternInfo['components'] as $component => $description): ?>
                        <li class="flex items-start">
                            <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <div>
                                <strong class="text-blue-700"><?php echo ucfirst(str_replace('_', ' ', $component)); ?>:</strong>
                                <span class="text-gray-600"><?php echo htmlspecialchars($description); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Validation Rules</h4>
                    <ul class="space-y-2">
                        <?php foreach ($patternInfo['rules'] as $rule): ?>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-600"><?php echo htmlspecialchars($rule); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Test Cases -->
        <div class="glass-card rounded-2xl p-8">
            <h2 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-flask mr-3 icon-accent"></i>
                Test Cases
            </h2>
            
            <?php
            // Test cases
            $testCases = [
                // Valid cases
                '9080093260_English_Nisarga_20251022164012.mp3',
                '9876543210_Hindi_Rajesh_Kumar_20251023090000.wav',
                '1234567890_Tamil_Priya_20251024120000.m4a',
                '9999888877_English_John_Doe_20251025180000.aac',
                
                // Invalid cases
                'invalid_filename.mp3',
                '123_English_Name.mp3',
                '9080093260_English_20251022164012.mp3',
                '9080093260_English_Name_invalid_timestamp.mp3',
                'abc_English_Name_20251022164012.mp3',
                '9080093260_English_Name_20251332164012.mp3', // Invalid date
            ];
            
            foreach ($testCases as $index => $testFilename):
                $parsed = FilenameParser::parseFilename($testFilename);
                $display = FilenameParser::formatForDisplay($parsed);
                $isValid = FilenameParser::isValidPattern($testFilename);
            ?>
            
            <div class="mb-6 p-6 rounded-lg border <?php echo $parsed['success'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'; ?>">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold <?php echo $parsed['success'] ? 'text-green-800' : 'text-red-800'; ?>">
                        Test Case <?php echo $index + 1; ?>
                        <?php if ($parsed['success']): ?>
                            <i class="fas fa-check-circle text-green-600 ml-2"></i>
                        <?php else: ?>
                            <i class="fas fa-times-circle text-red-600 ml-2"></i>
                        <?php endif; ?>
                    </h3>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $isValid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo $isValid ? 'Valid Pattern' : 'Invalid Pattern'; ?>
                    </span>
                </div>
                
                <div class="font-mono text-sm bg-gray-100 p-3 rounded mb-4">
                    <?php echo htmlspecialchars($testFilename); ?>
                </div>
                
                <?php if ($parsed['success']): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <div class="bg-white p-3 rounded border">
                        <div class="text-xs text-gray-500 mb-1">Phone Number</div>
                        <div class="font-semibold text-blue-700"><?php echo htmlspecialchars($parsed['phone_number']); ?></div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="text-xs text-gray-500 mb-1">Language</div>
                        <div class="font-semibold text-green-700"><?php echo htmlspecialchars($parsed['language']); ?></div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="text-xs text-gray-500 mb-1">Caller Name</div>
                        <div class="font-semibold text-purple-700"><?php echo htmlspecialchars($parsed['caller_name']); ?></div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="text-xs text-gray-500 mb-1">Call Date</div>
                        <div class="font-semibold text-orange-700"><?php echo htmlspecialchars($parsed['call_date']); ?></div>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <div class="text-xs text-gray-500 mb-1">Call Time</div>
                        <div class="font-semibold text-indigo-700"><?php echo htmlspecialchars($parsed['call_time']); ?></div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded border">
                    <div class="text-sm text-gray-600 mb-2">
                        <strong>Display Format:</strong> <?php echo htmlspecialchars($display['display_name']); ?>
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong>Details:</strong> <?php echo htmlspecialchars($display['details']); ?>
                    </div>
                </div>
                
                <?php else: ?>
                <div class="bg-white p-4 rounded border border-red-200">
                    <div class="text-red-700 font-medium mb-2">Parsing Failed</div>
                    <div class="text-sm text-red-600">
                        <strong>Error:</strong> <?php echo htmlspecialchars($parsed['error_message'] ?? 'Unknown error'); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <!-- JSON Debug -->
        <div class="glass-card rounded-2xl p-8 mt-8">
            <h2 class="text-2xl font-semibold mb-6 text-primary">
                <i class="fas fa-code mr-3 icon-accent"></i>
                JSON Debug Output (First Test Case)
            </h2>
            
            <?php $debugParsed = FilenameParser::parseFilename($testCases[0]); ?>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto text-sm"><?php echo json_encode($debugParsed, JSON_PRETTY_PRINT); ?></pre>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
