<?php
// api/auth_check.php — Include this at TOP of any protected PHP page
// Usage: require_once '../api/auth_check.php';

// Secure session cookie settings for modern browsers (Chrome/Brave)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Save intended URL so we can redirect back after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: /car-rent-web/signinUP.html?msg=login_required");
    exit();
}

// Make user data available globally
$CURRENT_USER = [
    'id'    => $_SESSION['user_id'],
    'name'  => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];
?>
