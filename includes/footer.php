    </div> <!-- Close main-content -->
    
    <!-- Professional Footer -->
    <footer class="glass-card mx-4 mb-4 p-8 animate-fade-in">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-6 md:mb-0">
                    <div class="logo-icon mr-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <!-- Audio waveform bars -->
                            <rect x="2" y="8" width="2" height="8" rx="1"/>
                            <rect x="6" y="4" width="2" height="16" rx="1"/>
                            <rect x="10" y="6" width="2" height="12" rx="1"/>
                            <rect x="14" y="2" width="2" height="20" rx="1"/>
                            <rect x="18" y="7" width="2" height="10" rx="1"/>
                            <!-- Analytics trend line -->
                            <path d="M2 15 L6 12 L10 14 L14 8 L18 10 L22 6" stroke="currentColor" stroke-width="2" fill="none" opacity="0.7"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-gradient text-xl font-bold">TrackerBI</span>
                        <p class="text-sm text-gray-600 mt-1">BPO Business Intelligence</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-800 font-medium">&copy; 2025 TrackerBI</p>
                    <p class="text-sm text-gray-600 mt-1">Powered by AI Business Intelligence</p>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-8 pt-6">
                <div class="flex flex-wrap justify-center gap-8 text-sm text-gray-600">
                    <!-- <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-500"></i>
                        <span>Secure Processing</span>
                    </div> -->
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-500"></i>
                        <span>Real-time Analysis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-globe text-blue-500"></i>
                        <span>Multi-language Support</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i>
                        <span>Advanced Analytics</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Common JavaScript -->
    <script>
        // Common functionality for all pages
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling to all anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add loading states to buttons (but don't prevent submission)
            document.querySelectorAll('button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Don't prevent default - let form submit normally
                    if (this.form && this.form.checkValidity()) {
                        // Add loading state after a short delay to allow form submission
                        setTimeout(() => {
                            this.disabled = true;
                            const originalText = this.innerHTML;
                            this.innerHTML = '<div class="flex items-center justify-center"><div class="loading-spinner mr-3"></div>Processing...</div>';
                        }, 100);
                        
                        // Re-enable after 30 seconds as fallback
                        setTimeout(() => {
                            this.disabled = false;
                            this.innerHTML = originalText;
                        }, 30000);
                    }
                });
            });

            // File upload validation
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const fileSize = file.size / 1024 / 1024; // Convert to MB
                        const maxSize = this.getAttribute('data-max-size') || 50;
                        
                        if (fileSize > maxSize) {
                            alert(`File size exceeds ${maxSize}MB limit. Please choose a smaller file.`);
                            this.value = '';
                        }
                    }
                });
            });
        });

        // Utility functions
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
    
    <?php if (isset($additional_scripts)) echo $additional_scripts; ?>
</body>
</html>
