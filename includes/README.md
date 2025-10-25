# TrackerBI Common Header and Footer Implementation

This directory contains common header and footer files that should be used across all PHP pages in the TrackerBI application.

## Files

- `header.php` - Common header with navigation, styles, and HTML structure
- `footer.php` - Common footer with scripts and closing HTML tags
- `template-example.php` - Example template showing how to use the common files
- `README.md` - This documentation file

## How to Use

### 1. Basic Implementation

For any new PHP page, follow this structure:

```php
<?php
// Your PHP logic here
$your_data = "example";

// Set page title (optional)
$page_title = 'Your Page Name';

// Include common header
include 'includes/header.php';
?>

<!-- Your page content here -->
<div class="container mx-auto px-6 py-12">
    <!-- Your content -->
</div>

<?php
// Include common footer
include 'includes/footer.php';
?>
```

### 2. Advanced Features

#### Custom Page Title
```php
$page_title = 'Dashboard'; // Will display as "Dashboard - TrackerBI"
```

#### Additional CSS Styles
```php
$additional_styles = '
<style>
    .custom-class {
        background: #f0f0f0;
    }
</style>
';
```

#### Additional JavaScript
```php
$additional_scripts = '
<script>
    // Your custom JavaScript
    console.log("Custom script loaded");
</script>
';
```

### 3. Navigation Active States

The header automatically detects the current page and applies the `active` class to the corresponding navigation link based on the filename.

### 4. Available CSS Classes

The header includes all common styles:
- `.glass-card` - Glass morphism card effect
- `.gradient-bg` - Primary gradient background
- `.btn-primary` - Primary button styling
- `.nav-link` - Navigation link styling
- `.loading-spinner` - Loading animation
- `.sentiment-positive/negative/neutral` - Sentiment styling
- `.card-shadow` - Standard card shadow
- `.sidebar-glass` - Sidebar glass effect

### 5. JavaScript Utilities

The footer includes common JavaScript functions:
- `showNotification(message, type)` - Show toast notifications
- `formatFileSize(bytes)` - Format file sizes
- Automatic form validation and loading states
- File upload size validation

## Converting Existing Pages

To convert an existing PHP page:

1. **Remove the HTML structure** (DOCTYPE, html, head, body tags)
2. **Add the header include** at the top after your PHP logic
3. **Set the page title** variable
4. **Remove duplicate CSS/JS** that's now in the common files
5. **Add the footer include** at the bottom
6. **Move page-specific scripts** to the `$additional_scripts` variable

## Example Conversion

**Before:**
```php
<?php
// logic here
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Page</title>
    <!-- styles -->
</head>
<body>
    <!-- navigation -->
    <!-- content -->
    <!-- footer -->
    <!-- scripts -->
</body>
</html>
```

**After:**
```php
<?php
// logic here
$page_title = 'My Page';
include 'includes/header.php';
?>
<!-- content only -->
<?php
include 'includes/footer.php';
?>
```

## Benefits

1. **Consistency** - All pages use the same navigation and styling
2. **Maintainability** - Update navigation/footer in one place
3. **Performance** - Shared CSS/JS reduces duplication
4. **Active States** - Automatic navigation highlighting
5. **Responsive** - All pages inherit responsive design
6. **Accessibility** - Common accessibility features across all pages

## File Structure

```
trackerbi/
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── template-example.php
│   └── README.md
├── index.php (updated to use common files)
├── dashboard.php (should be updated)
├── external-dashboard.php (should be updated)
├── meta-dashboard.php (should be updated)
└── other-pages.php (should be updated)
```
