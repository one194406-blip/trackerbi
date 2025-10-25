<?php
/**
 * Mobile Responsiveness Test Page
 * Tests all mobile CSS features across different screen sizes
 */

// Set page title for header
$page_title = 'Mobile Responsiveness Test';

// Include common header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <div class="gradient-bg py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-bold text-white text-center mb-4">
                <i class="fas fa-mobile-alt mr-4"></i>
                Mobile Test Page
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Testing mobile responsiveness across all TrackerBI components
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        
        <!-- Grid System Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Grid System Test</h2>
            
            <!-- 4 Column Grid -->
            <h3 class="text-xl font-semibold mb-4">4 Column Grid (2 on tablet, 1 on mobile)</h3>
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="glass-card p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">85.2</div>
                    <div class="text-gray-600">Metric 1</div>
                </div>
                <div class="glass-card p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">92.1</div>
                    <div class="text-gray-600">Metric 2</div>
                </div>
                <div class="glass-card p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">78.9</div>
                    <div class="text-gray-600">Metric 3</div>
                </div>
                <div class="glass-card p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">88.4</div>
                    <div class="text-gray-600">Metric 4</div>
                </div>
            </div>
            
            <!-- 2 Column Grid -->
            <h3 class="text-xl font-semibold mb-4">2 Column Grid (1 on mobile)</h3>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="glass-card p-6">
                    <h4 class="text-lg font-semibold mb-2">Left Column</h4>
                    <p class="text-gray-600">This content should stack vertically on mobile devices.</p>
                </div>
                <div class="glass-card p-6">
                    <h4 class="text-lg font-semibold mb-2">Right Column</h4>
                    <p class="text-gray-600">This content should stack vertically on mobile devices.</p>
                </div>
            </div>
        </div>

        <!-- Button Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Button Test</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <button class="btn-primary">Primary Button</button>
                <button class="btn bg-green-500 text-white px-6 py-3 rounded-lg">Secondary Button</button>
                <button class="btn bg-gray-500 text-white px-6 py-3 rounded-lg">Tertiary Button</button>
            </div>
        </div>

        <!-- Form Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Form Test</h2>
            <form class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your email">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your message"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Submit Form</button>
            </form>
        </div>

        <!-- Progress Bar Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Progress Bar Test</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Sentiment Score</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 85%"></div>
                        </div>
                        <span class="text-blue-600 font-semibold">85.0</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Clarity Score</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: 92%"></div>
                        </div>
                        <span class="text-green-600 font-semibold">92.0</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Performance Score</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-3 mr-3">
                            <div class="bg-purple-500 h-3 rounded-full" style="width: 78%"></div>
                        </div>
                        <span class="text-purple-600 font-semibold">78.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Chart Test</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sample Chart Area</h3>
                    <div style="height: 300px;" class="bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Chart would render here</p>
                            <p class="text-sm text-gray-400">Height adjusts on mobile</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Another Chart</h3>
                    <div style="height: 300px;" class="bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-chart-bar text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Chart would render here</p>
                            <p class="text-sm text-gray-400">Responsive height</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Table Test</h2>
            <div class="table-container overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sentiment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clarity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">call_001.mp3</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">85.2</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">92.1</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Good</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">call_002.mp3</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">78.9</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">88.4</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Excellent</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-14</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upload Area Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Upload Area Test</h2>
            <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Upload Your Audio File</h3>
                <p class="text-gray-500 mb-4">Drag and drop or click to select</p>
                <input type="file" class="hidden" id="audioFile" accept="audio/*">
                <label for="audioFile" class="btn-primary cursor-pointer inline-block">Choose File</label>
            </div>
        </div>

        <!-- Typography Test -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-3xl font-bold mb-6 text-primary">Typography Test</h2>
            <div class="space-y-4">
                <h1 class="text-6xl font-bold text-gray-800">Heading 1 (text-6xl)</h1>
                <h2 class="text-5xl font-bold text-gray-800">Heading 2 (text-5xl)</h2>
                <h3 class="text-4xl font-bold text-gray-800">Heading 3 (text-4xl)</h3>
                <h4 class="text-3xl font-bold text-gray-800">Heading 4 (text-3xl)</h4>
                <h5 class="text-2xl font-bold text-gray-800">Heading 5 (text-2xl)</h5>
                <h6 class="text-xl font-bold text-gray-800">Heading 6 (text-xl)</h6>
                <p class="text-base text-gray-600">Regular paragraph text that should be readable on all screen sizes.</p>
                <p class="text-sm text-gray-500">Small text for additional information.</p>
            </div>
        </div>

        <!-- Mobile Instructions -->
        <div class="glass-card rounded-2xl p-6 bg-blue-50 border-l-4 border-blue-500">
            <h2 class="text-2xl font-bold mb-4 text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Mobile Testing Instructions
            </h2>
            <div class="space-y-3 text-blue-700">
                <p><strong>Desktop (>768px):</strong> All elements should display in their full grid layouts</p>
                <p><strong>Tablet (768px):</strong> Grids should collapse to fewer columns, text should be readable</p>
                <p><strong>Mobile (480px):</strong> Most grids become single column, buttons become full width</p>
                <p><strong>Small Mobile (<480px):</strong> All grids single column, smaller text, compact spacing</p>
            </div>
            
            <div class="mt-6 p-4 bg-white rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-2">Test Checklist:</h3>
                <ul class="space-y-1 text-sm text-blue-600">
                    <li>✓ Navigation collapses to hamburger menu</li>
                    <li>✓ Grids stack vertically on mobile</li>
                    <li>✓ Buttons become full width</li>
                    <li>✓ Text sizes scale appropriately</li>
                    <li>✓ Charts adjust height for mobile</li>
                    <li>✓ Tables scroll horizontally</li>
                    <li>✓ Touch targets are minimum 44px</li>
                    <li>✓ Upload areas remain usable</li>
                </ul>
            </div>
        </div>

    </div>

    <!-- Test JavaScript for Mobile Features -->
    <script>
        // Test mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Mobile test page loaded');
            console.log('Screen width:', window.innerWidth);
            console.log('Screen height:', window.innerHeight);
            
            // Log viewport changes
            window.addEventListener('resize', function() {
                console.log('Viewport changed:', window.innerWidth, 'x', window.innerHeight);
            });
            
            // Test touch events
            document.addEventListener('touchstart', function() {
                console.log('Touch detected - mobile device confirmed');
            });
        });
    </script>

<?php
// Include common footer
include 'includes/footer.php';
?>
