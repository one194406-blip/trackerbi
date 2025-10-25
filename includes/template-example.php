<?php
// Example template for creating new PHP pages using common header and footer

// Your PHP logic goes here
$your_data = "example data";

// Set page title (optional - will default to "TrackerBI" if not set)
$page_title = 'Your Page Title';

// Optional: Add additional CSS styles specific to this page
$additional_styles = '
<style>
    .custom-class {
        /* Your custom styles here */
    }
</style>
';

// Include common header
include 'includes/header.php';
?>

    <!-- Your page content goes here -->
    <div class="gradient-bg py-16">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-bold text-white text-center mb-4">
                <i class="fas fa-your-icon mr-4 icon-accent"></i>
                Your Page Title
            </h1>
            <p class="text-white text-center text-xl opacity-90 max-w-2xl mx-auto">
                Your page description
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 py-12">
        <!-- Your main content -->
        <div class="glass-card rounded-2xl p-8">
            <h2 class="text-2xl font-semibold mb-6 text-primary">
                Your Content Section
            </h2>
            <!-- Add your content here -->
        </div>
    </div>

<?php
// Optional: Add page-specific JavaScript
$additional_scripts = '
<script>
    // Your page-specific JavaScript here
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Page loaded");
    });
</script>
';

// Include common footer
include 'includes/footer.php';
?>
