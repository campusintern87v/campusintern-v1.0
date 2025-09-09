<?php
session_start();
require_once "../includes/config.php";

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: ../" . $_SESSION['user']['role'] . "/dashboard.php");
    exit;
}

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Security error: Invalid form submission";
    } else {
        // Sanitize email
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);
                    
                    // Store only necessary user data in session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    // Redirect to appropriate dashboard
                    header("Location: ../" . $user['role'] . "/dashboard.php");
                    exit;
                } else {
                    // Generic error message to prevent user enumeration
                    $error = "Invalid email or password";
                    // Log failed attempt
                    error_log("Failed login attempt for email: $email");
                }
            } catch (PDOException $e) {
                error_log("Database error during login: " . $e->getMessage());
                $error = "System error. Please try again later.";
            }
        }
    }
}

include_once "../includes/header.php";
?>
<style>
    /* Background styling */
    body {
        background: url('jhclogo-1.png') no-repeat center center;
        background-size: 40%;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    /* Main content container */
    .login-container {
        flex: 1;
        padding-bottom: 60px; /* Creates space before footer */
    }
    
    /* Card styling */
    .card {
        background-color: rgba(255, 255, 255, 0.95);
        margin-top: 5rem;
        margin-bottom: 3rem;
    }
    
    /* Full-width footer */
    footer {
        width: 100%;
        margin-top: auto; /* Pushes footer to bottom */
    }
    
</style>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title text-center mb-0">Login</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required autocomplete="email" autofocus
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required autocomplete="current-password">
                            <small class="text-muted">
                                <a href="forgot_password.php">Forgot password?</a>
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>