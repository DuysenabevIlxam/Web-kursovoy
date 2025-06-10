<?php
session_start();
require 'includes/db.php';
require 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->query("SELECT capacity FROM capacity LIMIT 1");
$gym = $stmt->fetch();
$capacity = $gym ? $gym['capacity'] : 20;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $today = date('Y-m-d');

    if ($date < $today) {
        $error = "Siz búgingi kúnnen keyingi sánelerge ǵana jazılıwıńız múmkin.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND DATE(booking_time) = ?");
        $stmt->execute([$user_id, $date]);
        $count = $stmt->fetchColumn();

        if ($count >= 1) {
            $error = "Siz bul kúnde aldın jazılǵansız. Basqa kúndi saylań.";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE DATE(booking_time) = ? AND HOUR(booking_time) = ?");
            $stmt->execute([$date, $time_start]);
            $count = $stmt->fetchColumn();

            if ($count >= $capacity) {
                $error = "Bul waqıtta zal tolıq. Basqa waqıttı saylań.";
            } else {
                $booking_time_start = $date . ' ' . $time_start;
                $booking_time_end = $date . ' ' . $time_end;

                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_time) VALUES (?, ?)");
                $stmt->execute([$user_id, $booking_time_start]);

                $pdo->prepare("UPDATE users SET current_visits = current_visits + 1 WHERE id = ?")->execute([$user_id]);

                $success = "Siz tabıslı jazıldıńız: $date $time_start-$time_end";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Zalǵa jazılıw</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h3>Zalǵa jazılıw</h3>
  <a href="dashboard.php" class="btn btn-sm btn-secondary mb-3">← Artqa</a>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Sáne:</label>
      <input type="date" name="date" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Baslanıw waqtı:</label>
      <select name="time_start" class="form-select" required>
        <?php for ($i = 6; $i <= 21; $i++): ?>
          <option value="<?= sprintf('%02d:00', $i) ?>"><?= sprintf('%02d:00', $i) ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Tamam bolıw waqtı:</label>
      <input type="text" class="form-control" name="time_end" readonly id="time_end">
    </div>

    <button class="btn btn-primary">Jazılıw</button>
  </form>
</div>

<script>
  const startSelect = document.querySelector('[name="time_start"]');
  const endInput = document.getElementById('time_end');

  function updateEndTime() {
    const start = parseInt(startSelect.value.split(':')[0]);
    const endHour = start + 1;
    endInput.value = `${String(endHour).padStart(2, '0')}:00`;
  }

  startSelect.addEventListener('change', updateEndTime);
  updateEndTime();
</script>

</body>
</html>