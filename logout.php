<?php
/**
 * Logout Handler
 * Destroys user session and redirects to landing page
 */

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Regenerate session ID for security
session_start();
session_regenerate_id(true);

// Redirect to index page
header('Location: index.php');
exit();
?>
