<?php 
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT n.*, c.title as cat_title FROM news n JOIN categories c ON n.category_id = c.id WHERE n.id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) { die("<div class='container'>Новость не найдена</div>"); }

$img = $article['image_path'];
if (empty($img)) $img = 'https://via.placeholder.com/800x400';
elseif (strpos($img, 'http') === false) $img = 'assets/img/' . $img;

$pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$id]);
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <img src="<?= $img ?>" class="img-fluid rounded shadow-sm mb-4 w-100" style="max-height: 450px; object-fit: cover;">
            <span class="badge bg-primary mb-2"><?= $article['cat_title'] ?></span>
            <h1 class="display-5 fw-bold"><?= htmlspecialchars($article['title']) ?></h1>
            <p class="text-muted small">Просмотров: <?= $article['views'] ?> | Дата: <?= date('d.m.Y', strtotime($article['created_at'])) ?></p>
            <hr>
            <div class="lead" style="white-space: pre-line;">
                <?= htmlspecialchars($article['content_text']) ?>
            </div>
            <a href="index.php" class="btn btn-link mt-4 px-0"><i class="bi bi-arrow-left"></i> Назад к ленте</a>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>