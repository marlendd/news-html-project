<?php
require_once '../includes/db.php';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) {
    die("Доступ запрещен. У вас недостаточно прав.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $short_text = trim($_POST['short_text']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];
    
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/img/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $error = "Ошибка при загрузке изображения на сервер.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO news (title, short_text, content_text, category_id, author_id, image_path, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $author_id = $_SESSION['user_id']; // ID того, кто сейчас авторизован
            
            $stmt->execute([
                $title, 
                $short_text, 
                $content, 
                $category_id, 
                $author_id, 
                $image_name, 
                $status
            ]);
            
            header("Location: admin_news.php");
            exit;
        } catch (PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY title ASC")->fetchAll();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white p-3">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Создание новости</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="add_news.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Заголовок</label>
                                    <input type="text" name="title" class="form-control form-control-lg" placeholder="Введите название новости..." required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Краткий анонс</label>
                                    <textarea name="short_text" class="form-control" rows="3" placeholder="Пару предложений для главной страницы..." required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Полный текст новости</label>
                                    <textarea name="content" class="form-control" rows="8" placeholder="Здесь напишите подробности..." required></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded mb-3">
                                    <label class="form-label fw-bold text-primary">Категория</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="" disabled selected>Выберите рубрику</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="bg-light p-3 rounded mb-3">
                                    <label class="form-label fw-bold text-primary">Статус</label>
                                    <select name="status" class="form-select">
                                        <option value="published">Опубликовать сразу</option>
                                        <option value="draft">В черновики</option>
                                    </select>
                                </div>

                                <div class="bg-light p-3 rounded mb-4">
                                    <label class="form-label fw-bold text-primary">Обложка</label>
                                    <input type="file" name="image" class="form-control">
                                    <div class="form-text mt-1 small text-muted">Рекомендуется 1200x600px</div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                        <i class="bi bi-check-lg"></i> Сохранить
                                    </button>
                                    <a href="admin_news.php" class="btn btn-outline-secondary">
                                        Отмена
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>