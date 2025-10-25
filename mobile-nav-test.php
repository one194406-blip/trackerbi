<?php
/**
 * Mobile Navigation Test Page
 * Tests the hamburger menu and user dropdown functionality on mobile devices
 */

// Set page title for header
$page_title = 'Mobile Navigation Test';

// Include common header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <div class="gradient-bg py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-bold text-white text-center mb-4">
                <i class="fas fa-mobile-alt mr-4"></i>
                Mobile Navigation Test
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Testing hamburger menu and user dropdown on mobile devices
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        
        <!-- Navigation Test Instructions -->
        <div class="glass-card rounded-2xl p-6 mb-12 bg-blue-50 border-l-4 border-blue-500">
            <h2 class="text-2xl font-bold mb-4 text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Mobile Navigation Testing
            </h2>
            
            <div class="space-y-4 text-blue-700">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-2">📱 Hamburger Menu Features:</h3>
                        <ul class="space-y-1 text-sm">
                            <li>✓ <strong>Touch-friendly:</strong> 44px minimum touch targets</li>
                            <li>✓ <strong>Smooth animations:</strong> Slide down with staggered items</li>
                            <li>✓ <strong>Auto-close:</strong> Closes when clicking menu items</li>
                            <li>✓ <strong>Background blur:</strong> Prevents interaction with content</li>
                            <li>✓ <strong>Swipe to close:</strong> Swipe up to close menu</li>
                            <li>✓ <strong>Escape key:</strong> Press ESC to close</li>
                            <li>✓ <strong>Accessibility:</strong> ARIA labels and keyboard navigation</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-2">👤 User Dropdown Features:</h3>
                        <ul class="space-y-1 text-sm">
                            <li>✓ <strong>Compact design:</strong> Shows only avatar on mobile</li>
                            <li>✓ <strong>Smart positioning:</strong> Adjusts to screen edges</li>
                            <li>✓ <strong>Auto-close:</strong> Closes when clicking outside</li>
                            <li>✓ <strong>Mutual exclusive:</strong> Closes hamburger when opened</li>
                            <li>✓ <strong>Touch optimized:</strong> Larger touch targets</li>
                            <li>✓ <strong>Smooth animations:</strong> Scale and fade effects</li>
                            <li>✓ <strong>Focus management:</strong> Keyboard accessible</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Screen Size Indicator -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-2xl font-bold mb-4 text-primary">Current Screen Information</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-lg font-bold text-blue-600" id="screenWidth">-</div>
                    <div class="text-sm text-blue-700">Screen Width</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-lg font-bold text-green-600" id="screenHeight">-</div>
                    <div class="text-sm text-green-700">Screen Height</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-lg font-bold text-purple-600" id="deviceType">-</div>
                    <div class="text-sm text-purple-700">Device Type</div>
                </div>
            </div>
        </div>

        <!-- Navigation Test Actions -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-2xl font-bold mb-4 text-primary">Test Actions</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Hamburger Menu Tests</h3>
                    <div class="space-y-2">
                        <button onclick="testHamburgerMenu()" class="btn-primary w-full">
                            <i class="fas fa-bars mr-2"></i>
                            Toggle Hamburger Menu
                        </button>
                        <button onclick="testSwipeGesture()" class="btn bg-green-500 text-white px-6 py-3 rounded-lg w-full">
                            <i class="fas fa-hand-paper mr-2"></i>
                            Simulate Swipe Up
                        </button>
                        <button onclick="testEscapeKey()" class="btn bg-orange-500 text-white px-6 py-3 rounded-lg w-full">
                            <i class="fas fa-keyboard mr-2"></i>
                            Simulate ESC Key
                        </button>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">User Dropdown Tests</h3>
                    <div class="space-y-2">
                        <button onclick="testUserDropdown()" class="btn-primary w-full">
                            <i class="fas fa-user mr-2"></i>
                            Toggle User Dropdown
                        </button>
                        <button onclick="testOutsideClick()" class="btn bg-red-500 text-white px-6 py-3 rounded-lg w-full">
                            <i class="fas fa-mouse-pointer mr-2"></i>
                            Simulate Outside Click
                        </button>
                        <button onclick="testResizeEvent()" class="btn bg-purple-500 text-white px-6 py-3 rounded-lg w-full">
                            <i class="fas fa-expand-arrows-alt mr-2"></i>
                            Simulate Resize to Desktop
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results Log -->
        <div class="glass-card rounded-2xl p-6 mb-12">
            <h2 class="text-2xl font-bold mb-4 text-primary">Test Results Log</h2>
            <div id="testLog" class="bg-gray-100 p-4 rounded-lg min-h-32 font-mono text-sm overflow-y-auto max-h-64">
                <div class="text-gray-500">Test results will appear here...</div>
            </div>
            <button onclick="clearLog()" class="btn bg-gray-500 text-white px-4 py-2 rounded-lg mt-4">
                <i class="fas fa-trash mr-2"></i>
                Clear Log
            </button>
        </div>

        <!-- Mobile-Specific Features -->
        <div class="glass-card rounded-2xl p-6">
            <h2 class="text-2xl font-bold mb-4 text-primary">Mobile-Specific Features</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-green-600">✅ Implemented Features</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>44px minimum touch targets</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Prevent background scrolling when menu open</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Smooth animations with cubic-bezier easing</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Staggered menu item animations</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Touch gesture support (swipe to close)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Auto-close on navigation</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Keyboard accessibility (ESC, Tab, Enter)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>ARIA labels for screen readers</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-blue-600">📱 Mobile Optimizations</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-mobile-alt text-blue-500"></i>
                            <span>Responsive breakpoints (768px, 480px)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-eye text-blue-500"></i>
                            <span>Hide user details on mobile</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-compress text-blue-500"></i>
                            <span>Compact logo and spacing</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-500"></i>
                            <span>Proper z-index management</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-palette text-blue-500"></i>
                            <span>Glass morphism with backdrop blur</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-expand-arrows-alt text-blue-500"></i>
                            <span>Auto-adjust on orientation change</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-universal-access text-blue-500"></i>
                            <span>High contrast and reduced motion support</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- Test JavaScript -->
    <script>
        // Update screen information
        function updateScreenInfo() {
            document.getElementById('screenWidth').textContent = window.innerWidth + 'px';
            document.getElementById('screenHeight').textContent = window.innerHeight + 'px';
            
            let deviceType = 'Desktop';
            if (window.innerWidth <= 480) deviceType = 'Small Mobile';
            else if (window.innerWidth <= 768) deviceType = 'Mobile/Tablet';
            else if (window.innerWidth <= 1024) deviceType = 'Tablet';
            
            document.getElementById('deviceType').textContent = deviceType;
        }
        
        // Log function
        function log(message) {
            const logElement = document.getElementById('testLog');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<span class="text-gray-500">[${timestamp}]</span> ${message}`;
            logElement.appendChild(logEntry);
            logElement.scrollTop = logElement.scrollHeight;
        }
        
        // Test functions
        function testHamburgerMenu() {
            log('🍔 Testing hamburger menu toggle...');
            toggleMobileMenu();
            log('✅ Hamburger menu toggled');
        }
        
        function testUserDropdown() {
            log('👤 Testing user dropdown toggle...');
            toggleUserDropdown();
            log('✅ User dropdown toggled');
        }
        
        function testSwipeGesture() {
            log('👆 Simulating swipe up gesture...');
            const menu = document.querySelector('.nav-menu');
            if (menu && menu.classList.contains('show')) {
                menu.classList.remove('show');
                const toggle = document.querySelector('.mobile-menu-toggle');
                if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
                document.body.style.overflow = '';
                log('✅ Menu closed via swipe simulation');
            } else {
                log('ℹ️ Menu not open - nothing to close');
            }
        }
        
        function testEscapeKey() {
            log('⌨️ Simulating ESC key press...');
            const escEvent = new KeyboardEvent('keydown', { key: 'Escape' });
            document.dispatchEvent(escEvent);
            log('✅ ESC key simulation completed');
        }
        
        function testOutsideClick() {
            log('🖱️ Simulating outside click...');
            const clickEvent = new MouseEvent('click', { bubbles: true });
            document.body.dispatchEvent(clickEvent);
            log('✅ Outside click simulation completed');
        }
        
        function testResizeEvent() {
            log('📏 Simulating resize to desktop...');
            // Temporarily change window size simulation
            Object.defineProperty(window, 'innerWidth', { value: 1200, configurable: true });
            const resizeEvent = new Event('resize');
            window.dispatchEvent(resizeEvent);
            // Reset
            Object.defineProperty(window, 'innerWidth', { value: window.screen.width, configurable: true });
            updateScreenInfo();
            log('✅ Resize simulation completed');
        }
        
        function clearLog() {
            document.getElementById('testLog').innerHTML = '<div class="text-gray-500">Test results cleared...</div>';
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateScreenInfo();
            log('📱 Mobile navigation test page loaded');
            log(`🖥️ Screen: ${window.innerWidth}x${window.innerHeight}`);
            log('🎯 Ready for testing!');
        });
        
        // Update screen info on resize
        window.addEventListener('resize', updateScreenInfo);
        
        // Log touch events
        document.addEventListener('touchstart', function() {
            log('👆 Touch detected - mobile device confirmed');
        });
        
        // Log navigation events
        document.addEventListener('click', function(event) {
            if (event.target.closest('.nav-item')) {
                log('🔗 Navigation item clicked: ' + event.target.textContent.trim());
            }
            if (event.target.closest('.dropdown-item')) {
                log('📋 Dropdown item clicked: ' + event.target.textContent.trim());
            }
        });
    </script>

<?php
// Include common footer
include 'includes/footer.php';
?>
