<?php
require_once '../includes/db.php';

// Проверка сессии 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { 
    die("Доступ запрещен. Эта страница только для главного администратора."); 
}

// Логика быстрого обновления роли из таблицы
if (isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = (int)$_POST['new_role'];
    
    // Защита: нельзя менять роль самому себе, чтобы не потерять доступ
    if ($user_id == $_SESSION['user_id']) {
        $error = "Вы не можете изменить роль самому себе!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        header("Location: admin_users.php?success=1");
        exit;
    }
}

// 4. Получаем всех пользователей и их роли
$users = $pdo->query("
    SELECT u.*, r.name AS role_display_name 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    ORDER BY u.id ASC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container py-4">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link text-dark" href="admin_news.php">Управление новостями</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="admin_users.php">Пользователи</a>
        </li>
    </ul>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill me-2"></i>Список пользователей</h2>
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success py-1 px-3 mb-0">Изменения сохранены!</div>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Логин / Email</th>
                        <th>Текущая роль</th>
                        <th>Сменить роль</th>
                        <th class="text-end pe-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $u['id'] ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars((string)$u['login']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars((string)$u['email']) ?></small>
                        </td>
                        <td>
                            <?php 
                                $badge_class = 'bg-info';
                                if ($u['role_id'] == 1) $badge_class = 'bg-danger';
                                if ($u['role_id'] == 2) $badge_class = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?= $badge_class ?>">
                                <?= htmlspecialchars((string)$u['role_display_name']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="new_role" class="form-select form-select-sm" style="max-width: 150px;">
                                    <option value="1" <?= $u['role_id'] == 1 ? 'selected' : '' ?>>Админ</option>
                                    <option value="2" <?= $u['role_id'] == 2 ? 'selected' : '' ?>>Модератор</option>
                                    <option value="3" <?= $u['role_id'] == 3 ? 'selected' : '' ?>>Читатель</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-sm btn-primary">OK</button>
                            </form>
                        </td>
                        <td class="text-end pe-3">
                            <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?= $u['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger ms-1" 
                                   onclick="return confirm('Вы уверены, что хотите удалить пользователя?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>