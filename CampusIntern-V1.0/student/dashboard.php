<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../includes/config.php";

// Validate session
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user']['id'];

try {
    // Check if guidelines table exists
    $tableExists = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='guidelines'")->fetch();

    // Get notifications
    $stmt = $db->prepare("
        SELECT id, company, status, feedback, 
               CASE 
                   WHEN status = 'approved' AND notified = 0 THEN 'approval'
                   WHEN feedback IS NOT NULL AND feedback_notified = 1 THEN 'feedback'
               END AS notification_type
        FROM internships 
        WHERE student_id = ? 
          AND ((status = 'approved' AND notified = 0) 
               OR (feedback IS NOT NULL AND feedback_notified = 1))
    ");
    $stmt->execute([$student_id]);
    $all_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Categorize notifications
    $approved_notifications = array_filter($all_notifications, fn($n) => $n['notification_type'] === 'approval');
    $feedback_notifications = array_filter($all_notifications, fn($n) => $n['notification_type'] === 'feedback');
    $total_notifications = count($all_notifications);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred while fetching data.");
}

include_once "../includes/header.php";
?>
<style>
    /* Add this to your existing CSS */
    body {
        background: 
            url('jhclogo-1.png') no-repeat center center,
            linear-gradient(#f8f9fa, #e9ecef);
        background-size: 40%;
        background-attachment: fixed;
        min-height: 100vh;
    }
    
    /* Make cards slightly transparent */
    .card {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(2px);
    }
    
    /* Ensure content remains readable */
    .card-body, .list-group-item {
        background-color: rgba(255, 255, 255, 0.85);
    }
    
    /* Adjust for mobile */
    @media (max-width: 768px) {
        body {
            background-size: 80%;
        }
    }
</style>
<div class="container mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
        <div class="position-relative">
            <button class="btn btn-outline-primary position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#notificationsCollapse">
                <i class="bi bi-bell-fill"></i>
                <?php if ($total_notifications > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $total_notifications ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
    </div>

    <!-- Internship Submission Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="h5 mb-0">Submit New Internship</h3>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-plus display-4 text-success mb-3"></i>
                        <h4>Start New Submission</h4>
                        <p class="text-muted">Submit your internship details and documents</p>
                        <a href="internship_form.php" class="btn btn-success btn-lg">
                            <i class="bi bi-plus-circle"></i> Create Submission
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Guidelines Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">Internship Guidelines</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($tableExists)): ?>
                        <?php
                        $stmt = $db->query("SELECT * FROM guidelines ORDER BY uploaded_at DESC LIMIT 1");
                        $latestGuidelines = $stmt->fetch();
                        
                        if ($latestGuidelines): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <a href="<?= htmlspecialchars($latestGuidelines['file_path']) ?>" 
                                   target="_blank" 
                                   class="alert-link">
                                   Download Guidelines (<?= date('M d, Y', strtotime($latestGuidelines['uploaded_at'])) ?>)
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-circle"></i>
                                No guidelines available yet.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Guidelines system not configured.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    <?php if ($total_notifications > 0): ?>
        <div class="collapse show" id="notificationsCollapse">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">Notifications</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($approved_notifications as $n): ?>
                            <div class="list-group-item list-group-item-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Your internship at <strong><?= htmlspecialchars($n['company']) ?></strong> has been approved!
                            </div>
                        <?php endforeach; ?>
                        
                        <?php foreach ($feedback_notifications as $n): ?>
                            <div class="list-group-item list-group-item-info">
                                <i class="bi bi-chat-left-text-fill me-2"></i>
                                New feedback on <strong><?= htmlspecialchars($n['company']) ?></strong>:
                                <div class="mt-2 p-2 bg-light rounded"><?= htmlspecialchars($n['feedback']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Mark notifications as read
if ($total_notifications > 0) {
    try {
        $db->beginTransaction();
        
        if (!empty($approved_notifications)) {
            $ids = array_column($approved_notifications, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE internships SET notified = 1 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
        
        if (!empty($feedback_notifications)) {
            $ids = array_column($feedback_notifications, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE internships SET feedback_notified = 0 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
        
        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Notification update failed: " . $e->getMessage());
    }
}

include_once "../includes/footer.php";
?>