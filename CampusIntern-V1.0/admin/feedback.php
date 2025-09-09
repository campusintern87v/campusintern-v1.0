<?php
require_once "../includes/config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin access check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Get internship ID
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die("Invalid internship ID.");
}

// Get status from URL (optional)
$status = $_GET['status'] ?? '';
$preFilledFeedback = '';
$student_name = '';

// Fetch internship + student name
try {
    $stmt = $db->prepare("
        SELECT internships.company, internships.position, users.name AS student_name 
        FROM internships 
        JOIN users ON internships.student_id = users.id 
        WHERE internships.id = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Internship record not found.");
    }

    $student_name = $data['student_name'];

    // Pre-fill message if status is present
    if ($status === 'approved') {
        $preFilledFeedback = "Dear $student_name, your internship has been approved. Congratulations!";
    } elseif ($status === 'rejected') {
        $preFilledFeedback = "Dear $student_name, your internship has been rejected. Please review your submission and try again.";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $feedback = trim($_POST['feedback'] ?? '');
    if (empty($feedback)) {
        die("Feedback cannot be empty.");
    }

    try {
        $stmt = $db->prepare("UPDATE internships SET feedback = ?, feedback_notified = 1 WHERE id = ?");
        $stmt->execute([$feedback, $id]);

        $_SESSION['flash_message'] = "✅ Feedback submitted successfully!";
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Give Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
        }
        .card {
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Give Feedback to <strong><?= htmlspecialchars($data['company']) ?></strong>
                        <small class="text-light">(<?= htmlspecialchars($data['position']) ?>)</small>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label for="feedback" class="form-label">Your Feedback:</label>
                            <textarea 
                                name="feedback" 
                                id="feedback" 
                                class="form-control" 
                                rows="6" 
                                minlength="10" 
                                required
                            ><?= htmlspecialchars($preFilledFeedback) ?></textarea>
                            <small class="text-muted">Minimum 10 characters required.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
