<?php
session_start();
require 'includes/db.php';
require 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];

$current_month = date('m');
$current_year = date('Y');

$selected_month = isset($_GET['month']) ? $_GET['month'] : $current_month;
$selected_year = isset($_GET['year']) ? $_GET['year'] : $current_year;

$stmt = $pdo->prepare("SELECT b.id, b.booking_time
                       FROM bookings b
                       WHERE b.user_id = ? AND YEAR(b.booking_time) = ? AND MONTH(b.booking_time) = ?
                       ORDER BY b.booking_time DESC");
$stmt->execute([$user_id, $selected_year, $selected_month]);
$bookings = $stmt->fetchAll();

$months = [
    '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel', 
    '05' => 'May', '06' => 'Iyun', '07' => 'Iyul', '08' => 'Avgust',
    '09' => 'Sentabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr'
];
$years = range(2020, $current_year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meniń jazılıwlarım</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Meniń jazılıwlarım</h3>
    <a href="dashboard.php" class="btn btn-sm btn-secondary mb-3">← Artqa</a>
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($months as $key => $month): ?>
                        <option value="<?= $key ?>" <?= $key == $selected_month ? 'selected' : '' ?>><?= $month ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="year" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= $year == $selected_year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <h4>Jazılıwlar</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Jazılıw waqtı</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['id']) ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($booking['booking_time']))) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">Jazılıwlar tabılmadı.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>