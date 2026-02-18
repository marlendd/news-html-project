<?php 
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
чrequire_once 'includes/header.php';

$user_id = $_SESSION['user_id'];

// 1. Получаем данные пользователя
$user_stmt = $pdo->prepare("
    SELECT u.id, u.login, u.email, u.role_id, r.name 
    FROM users u 
    LEFT JOIN roles r ON u.role_id = r.id 
    WHERE u.id = ?
");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

if (!$user) {
    die("Ошибка: пользователь не найден.");
}

// 2. Получаем СПИСОК ПОДПИСОК
$sub_cats_stmt = $pdo->prepare("
    SELECT c.* FROM categories c 
    JOIN subscriptions s ON c.id = s.category_id 
    WHERE s.user_id = ?
");
$sub_cats_stmt->execute([$user_id]);
$my_subs = $sub_cats_stmt->fetchAll();

// 3. Получаем ПЕРСОНАЛЬНУЮ ЛЕНТУ
$feed_stmt = $pdo->prepare("
    SELECT n.*, c.title as cat_title, c.icon_class 
    FROM news n 
    JOIN categories c ON n.category_id = c.id 
    JOIN subscriptions s ON s.category_id = n.category_id
    WHERE s.user_id = ? AND n.status = 'published'
    ORDER BY n.created_at DESC
");
$feed_stmt->execute([$user_id]);
$feed_articles = $feed_stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 text-center p-3">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-person-circle display-1 text-primary"></i>
                    </div>
                    <h3 class="fw-bold"><?= htmlspecialchars($user['login']) ?></h3>
                    <span class="badge bg-light text-dark border mb-3">
                        <?= htmlspecialchars($user['role_name'] ?? 'Участник') ?>
                    </span>
                    <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
                    <hr>
                    <div class="d-grid">
                        <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Выйти из системы</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Мои подписки</h5>
                    <?php if(empty($my_subs)): ?>
                        <p class="small text-muted">Вы еще не подписаны на категории.</p>
                        <a href="index.php" class="btn btn-sm btn-link p-0">Перейти к выбору</a>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($my_subs as $sub): ?>
                                <span class="badge bg-primary rounded-pill">
                                    <i class="bi <?= $sub['icon_class'] ?>"></i> <?= htmlspecialchars($sub['title']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h4 class="fw-bold mb-4"><i class="bi bi-lightning-charge text-warning"></i> Моя лента обновлений</h4>
            
            <?php if(empty($feed_articles)): ?>
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-mailbox2 fs-1 text-muted"></i>
                        <h5 class="mt-3">Лента пока пуста</h5>
                        <p class="text-muted">Подпишитесь на «Спорт» или «Технологии», чтобы видеть здесь новости.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 g-3">
                    <?php foreach($feed_articles as $article): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-primary small fw-bold">
                                            <i class="bi <?= $article['icon_class'] ?>"></i> <?= htmlspecialchars($article['cat_title']) ?>
                                        </span>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($article['created_at'])) ?></small>
                                    </div>
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($article['title']) ?></h5>
                                    <p class="card-text text-muted small">
                                        <?= mb_strimwidth(strip_tags($article['short_text']), 0, 120, "...") ?>
                                    </p>
                                    <a href="article.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Читать далее</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>