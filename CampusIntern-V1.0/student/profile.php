<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../includes/config.php";

// Allow only student access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

// Get student user data from session
$student = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile | CampusIntern</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background: linear-gradient(135deg, #1cc88a, #198754);
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .card-header h4 {
            font-weight: 600;
        }
        .card-body p {
            font-size: 1.1rem;
        }
        .btn-outline-secondary {
            transition: all 0.3s;
        }
        .btn-outline-secondary:hover {
            background-color: #198754;
            color: white;
            border-color: #198754;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-white text-center py-4">
                        <h4><i class="bi bi-person-badge me-2"></i>Student Profile</h4>
                    </div>
                    <div class="card-body px-5 py-4">
                        <p><i class="bi bi-person-fill me-2 text-primary"></i><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                        <p><i class="bi bi-envelope-at-fill me-2 text-success"></i><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                        <p><i class="bi bi-mortarboard-fill me-2 text-warning"></i><strong>Role:</strong> <?= htmlspecialchars(ucfirst($student['role'])) ?></p>

                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left-circle me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 text-muted">
                    <small>
                        <i class="bi bi-c-circle"></i> <?= date('Y') ?> CampusIntern | Student Panel
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
