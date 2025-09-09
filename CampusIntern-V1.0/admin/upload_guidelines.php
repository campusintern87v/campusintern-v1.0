<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";

// Only allow admins to upload
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['guidelines'])) {
    try {
        // Validate file
        $file = $_FILES['guidelines'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        if ($mime !== 'application/pdf') {
            throw new Exception("Only PDF files are allowed");
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size exceeds 5MB limit");
        }

        // Generate unique filename
        $fileName = 'guidelines_' . time() . '.pdf';
        $filePath = '../student/uploads/guidelines/' . $fileName;
        
        // Create guidelines directory if needed
        if (!file_exists('../student/uploads/guidelines')) {
            mkdir('../student/uploads/guidelines', 0755, true);
        }
        
        // Move file and record in database
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $db->prepare("INSERT INTO guidelines (file_name, file_path, uploaded_by) VALUES (?, ?, ?)");
            $stmt->execute([$fileName, $filePath, $_SESSION['user']['id']]);
            
            $_SESSION['success'] = "Guidelines uploaded successfully!";
        } else {
            throw new Exception("Failed to save file");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header("Location: dashboard.php");
    exit;
}
?>