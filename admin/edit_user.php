<?php
require_once '../includes/db.php';

// Доступ только для админа (Role ID = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { 
    die("Доступ запрещен"); 
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Загружаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { die("Пользователь не найден"); }

// Загружаем список всех ролей для выпадающего списка
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

// Обработка сохранения
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $role_id = (int)$_POST['role_id'];
    $new_password = $_POST['password'];

    // Если пароль ввели — хешируем его, если нет — оставляем старый
    if (!empty($new_password)) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET login = ?, email = ?, role_id = ?, password_hash = ? WHERE id = ?";
        $params = [$login, $email, $role_id, $password_hash, $id];
    } else {
        $sql = "UPDATE users SET login = ?, email = ?, role_id = ? WHERE id = ?";
        $params = [$login, $email, $role_id, $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header("Location: admin_users.php");
    exit;
}

require_once '../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-person-gear me-2"></i>Редактировать профиль</h3>
                    <a href="admin_users.php" class="btn btn-outline-secondary btn-sm">Назад</a>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Логин</label>
                        <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($user['login']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Роль в системе</label>
                        <select name="role_id" class="form-select">
                            <?php foreach($roles as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= $r['id'] == $user['role_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Новый пароль (оставьте пустым, если не хотите менять)</label>
                        <input type="password" name="password" class="form-control" placeholder="Введите новый пароль">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>