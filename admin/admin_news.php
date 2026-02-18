<?php
require_once '../includes/db.php';

// Проверка прав (только админ и модератор)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) { 
    die("Доступ запрещен"); 
}

require_once '../includes/header.php';

$news = $pdo->query("SELECT n.*, c.title as cat_title 
                     FROM news n 
                     LEFT JOIN categories c ON n.category_id = c.id 
                     ORDER BY n.created_at DESC")->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-gear-fill me-2"></i>Управление новостями</h2>
                    <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="admin_news.php">Новости</a>
            </li>
            <?php if($_SESSION['role_id'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link" href="admin_users.php">Пользователи</a>
            </li>
            <?php endif; ?>
            </ul>
        <a href="add_news.php" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Добавить новость
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Заголовок</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th class="text-end pe-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($news)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Новостей пока нет</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach($news as $item): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $item['id'] ?></td>
                        <td class="fw-medium"><?= htmlspecialchars($item['title']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $item['cat_title'] ?: 'Без категории' ?></span></td>
                        <td>
                            <?php if($item['status'] == 'published'): ?>
                                <span class="badge bg-success-subtle text-success">Опубликовано</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning">Черновик</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Вы уверены, что хотите удалить эту новость?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
require_once '../includes/footer.php'; 
?>