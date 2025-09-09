<?php
session_start();
require_once "../includes/config.php";
include_once "../includes/header.php";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Filters
$filterDepartment = $_GET['department'] ?? '';
$filterYear = $_GET['year'] ?? '';
$filterDate = $_GET['filter_date'] ?? '';

$where = [];
$params = [];

// Add filters to WHERE clause
if (!empty($filterDepartment)) {
    $where[] = "users.department = :department";
    $params[':department'] = $filterDepartment;
}

if (!empty($filterYear)) {
    $where[] = "users.year = :year";
    $params[':year'] = $filterYear;
}

if (!empty($filterDate)) {
    $where[] = "DATE(internships.created_at) = :filter_date";
    $params[':filter_date'] = $filterDate;
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Total count for pagination
$totalStmt = $db->prepare("
    SELECT COUNT(*) 
    FROM internships 
    JOIN users ON internships.student_id = users.id 
    $whereClause
");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();

// Main query
$stmt = $db->prepare("
    SELECT internships.*, users.name, users.department, users.year 
    FROM internships 
    JOIN users ON internships.student_id = users.id 
    $whereClause
    ORDER BY internships.id DESC 
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$internships = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Add this to your existing CSS */
    body {
        background: 
            url('../assets/images/jhclogo-1.png') no-repeat center center,
            linear-gradient(#f8f9fa, #e9ecef);
        background-size: 40%;
        background-attachment: fixed;
        min-height: 100vh;
    }
    
    .card {
        background-color: rgba(255, 255, 255, 0.95);
    }
    
    /* Make sure tables remain readable */
    .table {
        background-color: rgba(255, 255, 255, 0.9);
    }
    footer {
         opacity: 10px;
    }
    
</style>
<div class="container mt-4">
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- ✅ Internship Submissions Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">All Internship Submissions</h2>
        </div>
       
<!-- Filter Button and Dropdown -->
<div class="mb-3">
    <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
        🔍 Filter
    </button>

    <div class="collapse mt-3" id="filterSection">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="department" class="form-label">Department</label>
                <select name="department" id="department" class="form-select">
                    <option value="">All</option>
                    <option value="BSC-IT" <?= ($filterDepartment === 'BSC-IT') ? 'selected' : '' ?>>BSC-IT</option>
                    <option value="BVOCSD" <?= ($filterDepartment === 'BVOCSD') ? 'selected' : '' ?>>BVOCSD</option>
                    <option value="MSCBIGDATA" <?= ($filterDepartment === 'MSCBIGDATA') ? 'selected' : '' ?>>MSCBIGDATA</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="year" class="form-label">Year</label>
                <select name="year" id="year" class="form-select">
                    <option value="">All</option>
                    <option value="FY" <?= ($filterYear === 'FY') ? 'selected' : '' ?>>FY</option>
                    <option value="SY" <?= ($filterYear === 'SY') ? 'selected' : '' ?>>SY</option>
                    <option value="TY" <?= ($filterYear === 'TY') ? 'selected' : '' ?>>TY</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="/admin/dashboard.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>


            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Offer Letter</th>
                            <th>Completion Certificate</th>
                            <th>Feedback</th>
                            <th>View Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($internships as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['name']) ?></td>
                            <td><?= htmlspecialchars($i['department']) ?></td>
                            <td><?= htmlspecialchars($i['year']) ?></td>
                            <td><?= htmlspecialchars($i['company']) ?></td>
                            <td><?= htmlspecialchars($i['position']) ?></td>
                            <td>
                                <?php if ($i['status'] === 'pending'): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Pending
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item text-success" href="approve.php?id=<?= $i['id'] ?>&status=approved">Approve</a></li>
                                            <li><a class="dropdown-item text-danger" href="approve.php?id=<?= $i['id'] ?>&status=rejected">Reject</a></li>
                                        </ul>
                                    </div>
                                <?php elseif ($i['status'] === 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($i['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($i['status']) ?></span>
                                <?php endif; ?>
                            </td>
 
                            <td>
    <?php if (!empty($i['offer_letter'])): ?>
        <a href="/uploads/<?= htmlspecialchars($i['offer_letter']) ?>" class="btn btn-sm btn-outline-primary" target="_blank" download>Download</a>
    <?php else: ?>
        <span class="text-muted">Not uploaded</span>
    <?php endif; ?>
</td>
<td>
    <?php if (!empty($i['completion_certificate'])): ?>
        <a href="/uploads/<?= htmlspecialchars($i['completion_certificate']) ?>" class="btn btn-sm btn-outline-success" target="_blank">View Certificate</a>
    <?php else: ?>
        <span class="text-muted">Not uploaded</span>
    <?php endif; ?>
</td>
                            <td>
                                <a href="feedback.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-info">Give Feedback</a>
                            </td>
                            <td>
    <a href="view_form.php?id=<?= $i['id'] ?>" class="btn btn-primary btn-sm">View</a>
  
</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($p = 1; $p <= ceil($total / $perPage); $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- ✅ Upload Guidelines Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h3 class="h5 mb-0">Upload Guidelines (PDF Only)</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="upload_guidelines.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="guidelines" class="form-control" accept=".pdf" required>
                    <small class="text-muted">Max size: 5MB | Only PDF files allowed.</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload Guidelines</button>
            </form>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>
