<?php
// api/logout.php

// ── MUST match the cookie params used in login.php / auth_check.php ──
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 1. Clear all session variables
$_SESSION = [];

// 2. Explicitly delete the session cookie from the browser
//    (Chrome/Brave require this — session_destroy() alone is not enough)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy the server-side session
session_destroy();

// 4. Redirect with cache-busting headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Location: ../signinUP.html?msg=logged_out");
exit();
?>