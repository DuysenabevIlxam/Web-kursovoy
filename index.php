<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fitness Club</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('assets/images/gym-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      height: 100vh;
      color: white;
      background-color: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 20px;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }
    .hero p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }
    .btn-custom {
      padding: 12px 24px;
      font-size: 18px;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <div class="hero">
    <div>
      <h1>Fitness Clubqa Xosh Keldińiz</h1>
      <p>Salamat ómirge birinshi qádemdi biz benen baslań!</p>

      <?php if ($logged_in): ?>
        <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php' ?>" class="btn btn-light btn-custom me-2">Kabinet</a>
        <a href="logout.php" class="btn btn-outline-light btn-custom">Shıǵıw</a>
      <?php else: ?>
        <a href="register.php" class="btn btn-primary btn-custom me-2">Dizimnen ótiw</a>
        <a href="login.php" class="btn btn-outline-light btn-custom">Kiriw</a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>