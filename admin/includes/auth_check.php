<?php
/**
 * Authentication & Session Timeout Check
 * This file ensures that the user is logged in and handles auto-logout after inactivity.
 * It must be included at the very top of every admin page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. Implement Session Timeout (30 minutes = 1800 seconds)
$timeout_duration = 1800; 

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expired, destroy and redirect
        session_unset();
        session_destroy();
        header("Location: login.php?reason=timeout");
        exit();
    }
}

// 3. Update last activity time for current request
$_SESSION['last_activity'] = time();

// 4. Provide database access using absolute path
require_once(__DIR__ . '/../../includes/db.php');
?>