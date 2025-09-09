<?php
// Database connection (same as your original code)
$root = dirname(__DIR__);
$configPath = $root . '/includes/config.php';

if (file_exists($configPath)) {
    require_once $configPath;
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    if (isset($db) && $db instanceof PDO) {
        $pdo = $db;
    } elseif (isset($conn) && $conn instanceof PDO) {
        $pdo = $conn;
    }
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    try {
        $dbFile = $root . '/database/campusintern.sqlite';
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
    }
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Email exists - redirect to reset page with email
            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } else {
            $message = "This email is not registered. <a href='register.php'>Register here</a>";
        }
    } else {
        $message = "Please enter your email";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Forgot Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= strpos($message, 'not registered') ? 'danger' : 'warning' ?>">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Enter Your Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>