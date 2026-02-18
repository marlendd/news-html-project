<?php
require_once '../includes/db.php';

// Проверка прав (только админ и модератор)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) { 
    die("Доступ запрещен"); 
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем текущие данные новости
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    die("Новость не найдена");
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $short = $_POST['short_text'];
    $content = $_POST['content_text'];
    $status = $_POST['status'];
    
    $image_path = $news['image_path']; // По умолчанию оставляем старое фото

    // Если загружено новое фото
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../assets/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = $filename;
        }
    }

    $sql = "UPDATE news SET category_id = ?, title = ?, short_text = ?, content_text = ?, image_path = ?, status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cat_id, $title, $short, $content, $image_path, $status, $id]);
    
    header("Location: admin_news.php");
    exit;
}

require_once '../includes/header.php';
?>

<div class="container py-4">
    <div class="card shadow-sm border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Редактирование новости #<?= $id ?></h3>
            <a href="admin_news.php" class="btn btn-outline-secondary btn-sm">Назад</a>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Заголовок</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($news['title']) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Категория</label>
                    <select name="category_id" class="form-select">
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $news['category_id'] ? 'selected' : '' ?>>
                                <?= $c['title'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Статус</label>
                    <select name="status" class="form-select">
                        <option value="published" <?= $news['status'] == 'published' ? 'selected' : '' ?>>Опубликовано</option>
                        <option value="draft" <?= $news['status'] == 'draft' ? 'selected' : '' ?>>Черновик</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Заменить картинку</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Краткий текст</label>
                <textarea name="short_text" class="form-control" rows="2"><?= htmlspecialchars($news['short_text']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Полное содержание</label>
                <textarea name="content_text" class="form-control" rows="8" required><?= htmlspecialchars($news['content_text']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Сохранить изменения</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>