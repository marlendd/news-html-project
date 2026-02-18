<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Проверяем, нет ли такого пользователя
    $check = $pdo->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
    $check->execute([$login, $email]);
    
    if ($check->rowCount() > 0) {
        $error = "Логин или Email уже заняты!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (login, email, password_hash, role_id) VALUES (?, ?, ?, 3)");
        if ($stmt->execute([$login, $email, $password])) {
            header("Location: login.php");
            exit;
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center">Регистрация</h3>
                    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Создать аккаунт</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>