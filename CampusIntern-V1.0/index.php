<?php
// Strict security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Permanent redirect (301) to login page
header("Location: auth/login.php", true, 301);
exit;