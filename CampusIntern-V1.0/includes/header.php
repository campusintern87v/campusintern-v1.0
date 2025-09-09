<?php
/**
 * Site Header Template
 * Includes meta tags, CSS, and opening body content
 */

// Security headers (add these early)
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Determine current section - initialize variables first
$isAdmin = false;
$isStudent = false;

// Check the current directory path
$currentDir = basename(dirname(__FILE__, 2));
if (strpos(__DIR__, 'admin') !== false) {
    $isAdmin = true;
} elseif (strpos(__DIR__, 'student') !== false) {
    $isStudent = true;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CampusIntern - Student Internship Management System">
    
    <title><?= htmlspecialchars($isAdmin ? 'Admin Portal' : ($isStudent ? 'Student Portal' : 'CampusIntern')) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #f8f9fa;
            --dark-color: #343a40;
            --accent-color: #ff6b00;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
            color: var(--accent-color) !important;
        }
        
        /* Added hover effects for navigation links */
        .nav-link {
            transition: all 0.3s ease !important;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }
        
        /* Optional: Add underline effect on hover */
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--accent-color);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-item.active {
            font-weight: 500;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .main-container {
            flex: 1;
            padding-top: 20px;
            padding-bottom: 40px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
        <div class="container-fluid">
            <!-- Left: Logo -->
            <a class="navbar-brand d-flex align-items-center" href="#" style="cursor: pointer;">
                <i class="bi bi-briefcase-fill me-2"></i>
                <strong>CampusIntern</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="internships.php"><i class="bi bi-list-check me-1"></i> Internships</a>
                        </li>
                    <?php elseif ($isStudent): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door me-1"></i> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="submit_internship.php"><i class="bi bi-upload me-1"></i> Submit Internship</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Right side navigation with hover effects -->
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <a class="nav-link text-white" href="about.php">
                            <i class="bi bi-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-white" href="placed_students.php">
                            <i class="bi bi-people-fill"></i> Our Placed Students
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container main-container">