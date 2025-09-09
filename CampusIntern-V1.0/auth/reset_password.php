<?php
// Database connection (same as above)
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
$success = false;

// Check if email parameter exists
if (!isset($_GET['email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_GET['email'];

// Verify email exists (prevent URL manipulation)
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: forgot_password.php?error=invalid_email");
    exit();
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($password) || empty($confirm)) {
        $message = "Please fill all fields";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match";
    } else {
        // Update password in database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);
            
            $message = "Password updated successfully! <a href='login.php'>Login here</a>";
            $success = true;
        } catch (PDOException $e) {
            $message = "Error updating password: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reset Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $success ? 'success' : 'danger' ?>">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$success): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Password</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>