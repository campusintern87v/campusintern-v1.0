<?php
// Start session securely
session_start([
    'use_strict_mode' => true,
    'cookie_httponly' => true,
    'cookie_secure' => true, // Enable in production with HTTPS
    'cookie_samesite' => 'Strict'
]);

// Unset all session variables
$_SESSION = [];

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Clear any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect to login with anti-caching headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Location: login.php", true, 303); // 303 See Other
exit;