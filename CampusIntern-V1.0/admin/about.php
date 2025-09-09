<?php include "../includes/header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      background-image: url('jhclogo-1.png'); /* 🔁 change to your logo filename */
      background-repeat: no-repeat;
      background-position: center;
      background-size: 300px;
      background-attachment: fixed;
      opacity: 0.95;
    }

    .about-card {
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      animation: fadeInDown 1s ease-in-out;
      background-color: white;
    }

    .person-card {
      border-radius: 15px;
      text-align: center;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
      background: #ffffff;
    }

    .person-card:hover {
      transform: scale(1.05);
    }

    .person-img {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #0d6efd;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2.section-title {
      margin-top: 60px;
      margin-bottom: 40px;
      color: #0d6efd;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <!-- Platform Description -->
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card about-card p-4">
        <div class="d-flex flex-column flex-md-row align-items-center">
          <div>
            <h3 class="text-primary">About CampusIntern</h3>
            <p class="text-muted">
              Welcome to <strong>CampusIntern</strong> – your gateway to professional development! Our platform empowers students
              to manage internships efficiently while enabling administrators to monitor and verify activities with ease.
              And it is specially design for (NEP) New Education Policy batch fro there (O.J.T) On Job Training.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Team Section -->
  <h2 class="text-center section-title">Meet Our Team</h2>
  <div class="row justify-content-center g-4">
   
    <!-- HOD -->
    <div class="col-md-4">
      <div class="person-card">
        <img src="images/hod.jpg" class="person-img" alt="HOD">
        <h5 class="mt-3 text-primary">Mr. Wilson Rao</h5>
        <p class="text-muted mb-0">Head of Department </p>
      </div>
    </div>

    <!-- Placement Cell Head -->
    <div class="col-md-4">
      <div class="person-card">
        <img src="images/cell.jpg" class="person-img" alt="Placement Head">
        <h5 class="mt-3 text-primary">Ms. Sunita Jena</h5>
        <p class="text-muted mb-0">Placement Cell Head</p>
      </div>
    </div>

     <!-- Co-Founder -->
    <div class="col-md-4">
      <div class="person-card">
        <img src="images/logo1.jpg" class="person-img" alt="Co-Founder">
        <h5 class="mt-3 text-primary">Mr. Shivam Chauhan</h5>
        <p class="text-muted mb-0">Founder Of CampusIntern</p>
      </div>
    </div>

  </div>
</div>

<?php include "../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
