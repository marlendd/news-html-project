<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['login'] = $user['login'];
        header("Location: profile.php");
        exit;
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center">Вход</h3>
                    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>