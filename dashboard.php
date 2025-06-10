<?php
session_start();
require 'includes/db.php';
require 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT u.name, t.name AS tariff_name, t.max_visits, u.current_visits, u.subscription_status 
                       FROM users u
                       LEFT JOIN tariffs t ON u.tariff_id = t.id
                       WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Paydalanıwshı tabılmadı.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_room'])) {
        $stmt = $pdo->prepare("INSERT INTO booking_requests (user_id) VALUES (?)");
        if ($stmt->execute([$user_id])) {
            echo '<div class="alert alert-success">Zalǵa jazılıw arzańız jiberildi.</div>';
        } else {
            echo '<div class="alert alert-danger">Zalǵa jazılıwda qátelik júz berdi.</div>';
        }
    }

    if (isset($_POST['change_tariff'])) {
        $tariff_id = $_POST['tariff_id'];
        $stmt = $pdo->prepare("INSERT INTO subscription_requests (user_id, tariff_id) VALUES (?, ?)");
        if ($stmt->execute([$user_id, $tariff_id])) {
            echo '<div class="alert alert-success">Tarifti ózgertiw arzańız jiberildi.</div>';
        } else {
            echo '<div class="alert alert-danger">Tarifti ózgertiwde qátelik júz berdi.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kabinet - <?= htmlspecialchars($user['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f3f3f3;
      font-family: 'Segoe UI', sans-serif;
    }
    .dashboard {
      max-width: 800px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    .progress-bar {
      background-color: #198754;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h2 class="mb-4">Sálem, <?= htmlspecialchars($user['name']) ?>!</h2>

  <p><strong>Tarif:</strong> <?= $user['tariff_name'] ?? 'Belgilenbegen' ?></p>
  <p><strong>Tarif jaǵdayı:</strong> 
    <?php
      if ($user['subscription_status'] === 'active') echo '<span class="text-success">Aktiv</span>';
      else echo '<span class="text-danger">Aktiv emes</span>';
    ?>
  </p>

  <?php if ($user['tariff_name']): ?>
    <p><strong>Qalǵan kiriwler:</strong> <?= $user['max_visits'] - $user['current_visits'] ?> / <?= $user['max_visits'] ?></p>
    <div class="progress mb-3">
      <div class="progress-bar" role="progressbar" style="width: <?= ($user['current_visits'] / $user['max_visits']) * 100 ?>%;" aria-valuenow="<?= $user['current_visits'] ?>" aria-valuemin="0" aria-valuemax="<?= $user['max_visits'] ?>"></div>
    </div>
  <?php endif; ?>

  <h4>Arza jiberiw</h4>
  <?php if ($user['subscription_status'] === 'active'): ?>
    <a href="bookings.php" class="btn btn-primary">Zalǵa jazılıw</a> <a href="my_bookings.php" class="btn btn-warning">Menin jazılıwlarım</a>
  <?php else: ?>
    <div class="alert alert-warning">Sizdiń tarifińiz aktiv emes, zalǵa jazılıw ámelge asırılmaydı.</div>
  <?php endif; ?>

  <h5>Tarifti ózgertiw</h5>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="tariff_id" class="form-label">Jańa tarif saylaw</label>
        <select name="tariff_id" id="tariff_id" class="form-select" required>
          <option value="">-- Saylań --</option>
          <?php
          $stmt = $pdo->query("SELECT * FROM tariffs ORDER BY price ASC");
          $tariffs = $stmt->fetchAll();
          foreach ($tariffs as $tariff) {
            echo '<option value="' . $tariff['id'] . '">' . htmlspecialchars($tariff['name']) . ' - ' . htmlspecialchars($tariff['price']) . ' som</option>';
          }
          ?>
        </select>
      </div>
      <button type="submit" name="change_tariff" class="btn btn-success">Tarifti ózgertiw</button>
    </form>

  <a href="logout.php" class="btn btn-outline-danger float-end">Shıǵıw</a>
</div>

</body>
</html>