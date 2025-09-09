<?php
// Database configuration
define('DB_PATH', __DIR__.'/../db/db.sqlite');

// Initialize session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']),
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true
    ]);
}

try {
    // Database connection
    $db = new PDO('sqlite:'.DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA foreign_keys = ON');
    $pdo = $db; 
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("System temporarily unavailable. Please try again later.");
}

// File Upload Configuration
define('UPLOAD_DIR', realpath(__DIR__.'/../student').'/uploads/');
define('ALLOWED_MIME_TYPES', [
    'application/pdf' => '.pdf',
    'image/jpeg' => '.jpg',
    'image/png' => '.png'
]);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Create and secure upload directory
if (!file_exists(UPLOAD_DIR)) {
    $oldUmask = umask(0); // Set permissions correctly
    if (!mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
        error_log("Failed to create directory: " . UPLOAD_DIR);
        die("System configuration error. Please contact administrator.");
    }
    umask($oldUmask);
    
    // Security files
    $securityFiles = [
        'index.html' => '',
        '.htaccess' => "Deny from all\n<FilesMatch \"\.(pdf|jpe?g|png)$\">\n    Allow from 127.0.0.1\n</FilesMatch>",
        'web.config' => "<configuration>\n<system.webServer>\n<handlers>\n<clear />\n</handlers>\n</system.webServer>\n</configuration>"
    ];
    
    foreach ($securityFiles as $file => $content) {
        file_put_contents(UPLOAD_DIR.$file, $content);
    }
}

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        error_log("CSRF token generation failed: " . $e->getMessage());
        die("Security system error");
    }
}

// Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>