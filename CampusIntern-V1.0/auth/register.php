<?php
require_once "../includes/config.php";

// Start session early (needed for CSRF)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make PDO throw exceptions (in case config.php didn't)
if (isset($db)) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

// Security headers BEFORE any output
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net");

// Initialize variables
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'department' => '',
    'year' => ''
];
$registrationSuccess = false;

// Generate CSRF token if not exists (so it's ready for the first GET)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get current admin count (safe if table exists)
try {
    $adminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
} catch (PDOException $e) {
    $adminCount = 0; // don't block the form if this fails
}

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = "Security error: Invalid form submission";
    } else {
        // Validate and sanitize inputs
        $formData['name']       = trim($_POST['name'] ?? '');
        $formData['email']      = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password               = $_POST['password'] ?? '';
        $formData['department'] = trim($_POST['department'] ?? '');
        $formData['year']       = trim($_POST['year'] ?? '');
        $role                   = in_array($_POST['role'] ?? '', ['student', 'admin'], true) ? $_POST['role'] : 'student';

        // Admin registration limit check
        if ($role === 'admin' && $adminCount >= 3) {
            $errors[] = "Maximum number of admin accounts (3) already exists";
            $role = 'student';
        }

        // Validation checks
        if ($formData['name'] === '') {
            $errors[] = "Full name is required";
        } elseif (mb_strlen($formData['name']) > 100) {
            $errors[] = "Name cannot exceed 100 characters";
        }

        if ($formData['email'] === '') {
            $errors[] = "Email is required";
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (mb_strlen($formData['email']) > 255) {
            $errors[] = "Email cannot exceed 255 characters";
        }

        if ($password === '') {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }

        // Check if email exists
        if (empty($errors)) {
            try {
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$formData['email']]);
                if ($stmt->fetch()) {
                    $errors[] = "Email already registered";
                }
            } catch (PDOException $e) {
                error_log("Database error (email check): " . $e->getMessage());
                $errors[] = "System error. Please try again later.";
            }
        }

        // Proceed with registration if no errors
        if (empty($errors)) {
            try {
                $db->beginTransaction();

                // Portable timestamp (works for SQLite/MySQL)
                $createdAt = date('Y-m-d H:i:s');
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $db->prepare("
                    INSERT INTO users 
                        (name, email, password, role, department, year, created_at)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $formData['name'],
                    $formData['email'],
                    $hashedPassword,
                    $role,
                    $formData['department'],
                    $formData['year'],
                    $createdAt
                ]);

                $db->commit();
                $registrationSuccess = true;

                if ($role === 'admin') {
                    $adminCount++;
                }

                error_log("New user registered: " . $formData['email']);
            } catch (PDOException $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log("Registration failed: " . $e->getMessage());
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}

// From this point we can safely output HTML
include_once "../includes/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Create Account</h3>
                </div>

                <div class="card-body">
                    <?php if ($registrationSuccess): ?>
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Registration Successful!</h4>
                            <p>Your account has been created. You can now <a href="login.php" class="alert-link">login here</a>.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5 class="alert-heading">Please fix these errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               required maxlength="100"
                                               value="<?= htmlspecialchars($formData['name']) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               required maxlength="255"
                                               value="<?= htmlspecialchars($formData['email']) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                               required minlength="8" autocomplete="new-password">
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Account Type</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="student" <?= (isset($_POST['role']) && $_POST['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                                            <?php if ($adminCount < 3): ?>
                                                <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                                            <?php endif; ?>
                                        </select>
                                        <?php if ($adminCount >= 3): ?>
                                            <small class="text-muted">Admin registration closed (maximum 3 admins reached)</small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <input type="text" class="form-control" id="department" name="department"
                                               value="<?= htmlspecialchars($formData['department']) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Year</label>
                                        <input type="text" class="form-control" id="year" name="year"
                                               value="<?= htmlspecialchars($formData['year']) ?>">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-person-plus"></i> Register
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>
