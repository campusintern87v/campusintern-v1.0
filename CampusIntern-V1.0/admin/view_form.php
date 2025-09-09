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

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die("Invalid Request");
}

$stmt = $db->prepare("SELECT * FROM internships WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Form not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Internship Form</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
    <style>
        body {
            background: 
                url('jhclogo-1.png') no-repeat center center fixed,
                linear-gradient(#f8f9fa, #e9ecef);
            background-size: 35%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 40px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.96);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            width: 90%;
            max-width: 800px;
        }

        .card-header {
            border-radius: 15px 15px 0 0;
        }

        table th {
            background-color: #f1f3f5;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="h4 mb-0">📋 Internship Form Data</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <?php foreach ($data as $field => $value): ?>
                    <tr>
                        <th><?= ucfirst(str_replace("_", " ", $field)) ?></th>
                        <td><?= htmlspecialchars($value) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="text-end">
                <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
