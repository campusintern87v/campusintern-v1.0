<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax'
    ]);
}

require_once "../includes/config.php";

// Validate session and role
if (!isset($_SESSION['user'])) {
    die('<script>window.location.href="../auth/login.php"</script>');
}
if ($_SESSION['user']['role'] !== 'student') {
    die('<script>window.location.href="../auth/unauthorized.php"</script>');
}

// Configuration
$baseDir = realpath(__DIR__.'/../student').'/';
$uploadDir = $baseDir.'uploads/';
$allowedOfferTypes = ['application/pdf' => '.pdf'];
$allowedCertTypes = [
    'application/pdf' => '.pdf',
    'image/jpeg' => '.jpg',
    'image/png' => '.png'
];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Create upload directory if not exists
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die("System error: Failed to create upload directory");
    }
}

// Initialize variables
$errors = [];
$filePaths = [
    'offer_letter' => null,
    'completion_certificate' => null
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Security error: Invalid form submission";
    }

    if (empty($errors)) {
        // Process file uploads
        foreach (['offer_letter', 'completion_certificate'] as $fileType) {
            if (!empty($_FILES[$fileType]['tmp_name'])) {
                $file = $_FILES[$fileType];
                $allowedTypes = ($fileType === 'offer_letter') ? $allowedOfferTypes : $allowedCertTypes;
                
                try {
                    // Validate file
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($file['tmp_name']);
                    
                    if (!isset($allowedTypes[$mime])) {
                        throw new Exception("$fileType must be ".implode(', ', array_values($allowedTypes)));
                    }
                    
                    if ($file['size'] > $maxFileSize) {
                        throw new Exception("$fileType exceeds 5MB limit");
                    }
                    
                    // Generate secure filename
                    $extension = $allowedTypes[$mime];
                    $filename = uniqid($fileType.'_').$extension;
                    $destination = $uploadDir.$filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        // Store relative path for database
                        $filePaths[$fileType] = 'uploads/'.$filename;
                    } else {
                        throw new Exception("Failed to save $fileType");
                    }
                    
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            } elseif ($fileType === 'offer_letter') {
                $errors[] = "Offer letter is required";
            }
        }

        // Validate required fields
        $required = ['company', 'position', 'duration', 'mode', 'interview_mode', 'stipend'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst($field)." is required";
            }
        }

        // If no errors, proceed with database operation
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                $stmt = $db->prepare("
                    INSERT INTO internships (
                        student_id, company, position, duration, mode,
                        interview_mode, interview_questions, stipend,
                        skills, company_website, offer_letter, completion_certificate, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                
                $stmt->execute([
                    $_SESSION['user']['id'],
                    htmlspecialchars(strip_tags($_POST['company'])),
                    htmlspecialchars(strip_tags($_POST['position'])),
                    htmlspecialchars(strip_tags($_POST['duration'])),
                    $_POST['mode'],
                    $_POST['interview_mode'],
                    !empty($_POST['interview_questions']) ? htmlspecialchars(strip_tags($_POST['interview_questions'])) : null,
                    $_POST['stipend'],
                    !empty($_POST['skills']) ? htmlspecialchars(strip_tags($_POST['skills'])) : null,
                    !empty($_POST['company_website']) ? filter_var($_POST['company_website'], FILTER_SANITIZE_URL) : null,
                    $filePaths['offer_letter'],
                    $filePaths['completion_certificate']
                ]);
                
                $db->commit();
                
                $_SESSION['flash_message'] = "✅ Internship submitted successfully!";
                header("Location: dashboard.php");
                exit;
                
            } catch (PDOException $e) {
                $db->rollBack();
                // Clean up uploaded files
                foreach ($filePaths as $filePath) {
                    if ($filePath && file_exists($baseDir.$filePath)) {
                        unlink($baseDir.$filePath);
                    }
                }
                error_log("Database error: ".$e->getMessage());
                $errors[] = "Failed to save internship details. Please try again.";
            }
        }
    }
}

// If we reached here, there were errors
$_SESSION['form_errors'] = $errors;
$_SESSION['form_data'] = $_POST;
header("Location: submit_internship.php");
exit;  