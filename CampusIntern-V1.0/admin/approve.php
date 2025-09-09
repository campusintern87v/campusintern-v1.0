<?php 
session_start();
require_once "../includes/config.php";

// Check admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'], $_GET['status'])) {
    $internship_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $allowed_statuses = ['approved', 'rejected'];
    $status = in_array($_GET['status'], $allowed_statuses) ? $_GET['status'] : null;

    if ($internship_id && $status) {
        try {
            // Update internship status
            $stmt = $db->prepare("UPDATE internships SET status = ? WHERE id = ?");
            $stmt->execute([$status, $internship_id]);

            // Get student_id from internship
            $stmt = $db->prepare("SELECT student_id FROM internships WHERE id = ?");
            $stmt->execute([$internship_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['flash_message'] = "Status updated to $status successfully.";

            // Redirect to feedback page with internship ID and status
            if ($student && isset($student['student_id'])) {
                header("Location: feedback.php?id=$internship_id&status=$status");
                exit;
            }

        } catch (PDOException $e) {
            error_log("DB error: " . $e->getMessage());
            $_SESSION['flash_message'] = "❌ Error updating status.";
        }
    }
}

// Fallback redirect
header("Location: dashboard.php");
exit;
