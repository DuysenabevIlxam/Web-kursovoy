<?php
require_once 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'admin') {
            header("Location: /admin/index.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Email jáne parol qáte.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kiriw</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('assets/images/gym-bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            background-color: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h3 class="text-center mb-4">Sistemaga kiriw</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email manzil</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Parol</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Kiriw</button>
        </form>
        <p class="text-center mt-3">Jańa paydalanıwshı? <a href="register.php">Dizimnen ótiw</a></p>
    </div>
</body>
</html>