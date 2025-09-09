<?php
session_start();
require_once "../includes/config.php";
include_once "../includes/header.php";

// Validate session and role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security error: Invalid CSRF token");
    }

    // Validate inputs
    $required = ['company', 'position', 'duration', 'mode', 'interview_mode', 'stipend'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: $field is required");
        }
    }

    // Process file uploads
    $uploadDir = "../student/uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $offerLetterPath = null;
    $certificatePath = null;

    try {
        // Process offer letter
        if (isset($_FILES['offer_letter'])) {
            $offerLetter = $_FILES['offer_letter'];
            if ($offerLetter['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($offerLetter['name'], PATHINFO_EXTENSION);
                if (strtolower($extension) !== 'pdf') {
                    die("Error: Offer letter must be a PDF file");
                }
                $offerLetterPath = $uploadDir . uniqid('offer_') . '.pdf';
                move_uploaded_file($offerLetter['tmp_name'], $offerLetterPath);
            }
        }

        // Process certificate
        if (isset($_FILES['completion_certificate']) && $_FILES['completion_certificate']['error'] === UPLOAD_ERR_OK) {
            $certificate = $_FILES['completion_certificate'];
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($certificate['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedExtensions)) {
                die("Error: Certificate must be PDF or image");
            }
            $certificatePath = $uploadDir . uniqid('cert_') . '.' . $extension;
            move_uploaded_file($certificate['tmp_name'], $certificatePath);
        }

        // Insert into database
        $db->beginTransaction();
        
        $stmt = $db->prepare("
            INSERT INTO internships (
                student_id, company, position, duration, mode, 
                interview_mode, interview_questions, stipend, 
                skills, company_website, offer_letter, completion_certificate
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user']['id'],
            htmlspecialchars($_POST['company']),
            htmlspecialchars($_POST['position']),
            htmlspecialchars($_POST['duration']),
            $_POST['mode'],
            $_POST['interview_mode'],
            htmlspecialchars($_POST['interview_questions'] ?? null),
            $_POST['stipend'],
            htmlspecialchars($_POST['skills'] ?? null),
            filter_var($_POST['company_website'] ?? null, FILTER_SANITIZE_URL),
            $offerLetterPath,
            $certificatePath
        ]);
        
        $db->commit();
        
        $_SESSION['flash_message'] = "✅ Internship submitted successfully!";
        header("Location: dashboard.php");
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        // Clean up uploaded files if transaction failed
        if ($offerLetterPath && file_exists($offerLetterPath)) unlink($offerLetterPath);
        if ($certificatePath && file_exists($certificatePath)) unlink($certificatePath);
        
        error_log("Internship submission error: " . $e->getMessage());
        die("An error occurred while submitting your internship");
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0">Submit Internship Details</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="submit_internship.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="row g-3">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company" name="company" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Position/Role</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 2 months" required>
                        </div>
                    </div>
                    
                    <!-- Mode Selection -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mode" class="form-label">Internship Mode</label>
                            <select class="form-select" id="mode" name="mode" required>
                                <option value="">Select Mode</option>
                                <option value="Work from Home">Work from Home</option>
                                <option value="Work from Office">Work from Office</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="interview_mode" class="form-label">Interview Mode</label>
                            <select class="form-select" id="interview_mode" name="interview_mode" required>
                                <option value="">Select Mode</option>
                                <option value="Online">Online</option>
                                <option value="Offline">Offline</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stipend" class="form-label">Stipend Provided?</label>
                            <select class="form-select" id="stipend" name="stipend" required>
                                <option value="">Select Option</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="interview_questions" class="form-label">Interview Questions</label>
                            <textarea class="form-control" id="interview_questions" name="interview_questions" rows="3" placeholder="What questions were asked in the interview?"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills Required</label>
                            <input type="text" class="form-control" id="skills" name="skills" placeholder="Comma-separated list (e.g., Python, SQL, React)">
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_website" class="form-label">Company Website</label>
                            <input type="url" class="form-control" id="company_website" name="company_website" placeholder="https://example.com">
                        </div>
                    </div>
                    
                    <!-- File Uploads -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="offer_letter" class="form-label">Offer Letter (PDF only)</label>
                            <input type="file" class="form-control" id="offer_letter" name="offer_letter" accept=".pdf" required>
                            <div class="form-text">Max size: 5MB</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="completion_certificate" class="form-label">Completion Certificate</label>
                            <input type="file" class="form-control" id="completion_certificate" name="completion_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF or image (max 5MB)</div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill"></i> Submit Internship
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>