<?php 
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';

// 1. Получаем параметры фильтрации
$cat_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// 2. Формируем SQL запрос динамически
$sql = "SELECT n.*, c.title as cat_title, c.icon_class 
        FROM news n 
        JOIN categories c ON n.category_id = c.id 
        WHERE n.status = 'published'";

$params = [];

if ($cat_id > 0) {
    $sql .= " AND n.category_id = ?";
    $params[] = $cat_id;
}

if (!empty($search_query)) {
    $sql .= " AND (n.title LIKE ? OR n.short_text LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY n.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// 3. Получаем список всех категорий для сайдбара
$categories = $pdo->query("SELECT * FROM categories ORDER BY title ASC")->fetchAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <?php 
                        if ($search_query) {
                            echo "Результаты поиска: " . htmlspecialchars($search_query);
                        } elseif ($cat_id && !empty($articles)) {
                            echo "Категория: " . htmlspecialchars($articles[0]['cat_title']);
                        } elseif ($cat_id && empty($articles)) {
                            echo "В этой категории пока нет новостей";
                        } else {
                            echo "Последние новости";
                        }
                    ?>
                </h2>
                <?php if($cat_id || $search_query): ?>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill">Сбросить всё</a>
                <?php endif; ?>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach($articles as $article): 
                    $img_path = $article['image_path'];
                    if (empty($img_path)) {
                        $img_url = 'https://via.placeholder.com/400x200?text=NewsHub';
                    } elseif (filter_var($img_path, FILTER_VALIDATE_URL)) {
                        $img_url = $img_path;
                    } else {
                        $img_url = 'assets/img/' . $img_path;
                    }
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 news-card">
                        <div class="position-relative">
                            <img src="<?= $img_url ?>" class="card-img-top" alt="News Image" style="height: 200px; object-fit: cover; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                            <span class="position-absolute top-0 start-0 m-2 badge rounded-pill bg-primary">
                                <i class="bi <?= $article['icon_class'] ?>"></i> <?= htmlspecialchars($article['cat_title']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($article['title']) ?></h5>
                            <p class="card-text text-muted small">
                                <?= mb_strimwidth(strip_tags($article['short_text']), 0, 120, "...") ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pb-3">
                            <a href="article.php?id=<?= $article['id'] ?>" class="btn btn-primary btn-sm px-3 rounded-pill">Читать далее</a>
                            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d.m.y', strtotime($article['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($articles)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search-heart display-1 text-muted"></i>
                        <p class="mt-3 lead">Ничего не найдено</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold">Поиск по сайту</h5>
                    <form action="index.php" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Что ищем?" value="<?= htmlspecialchars($search_query) ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold">Рубрики</h5>
                    <div class="list-group list-group-flush">
                        <?php foreach($categories as $cat): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <a href="index.php?category=<?= $cat['id'] ?>" class="text-decoration-none text-dark d-flex align-items-center">
                                <i class="bi <?= $cat['icon_class'] ?> me-2 text-primary"></i>
                                <?= htmlspecialchars($cat['title']) ?>
                            </a>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="subscribe.php?cat_id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary border-0" title="Подписаться">
                                    <i class="bi bi-bell"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if(!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-primary border-0 shadow-sm p-4 rounded-3">
                <div class="d-flex mb-2">
                    <i class="bi bi-pencil-square fs-3 me-3"></i>
                    <h5 class="alert-heading fw-bold mb-0">Станьте автором!</h5>
                </div>
                <p class="small mb-3">Зарегистрируйтесь и предложите свою новость нашему модератору.</p>
                <a href="register.php" class="btn btn-primary btn-sm rounded-pill px-4">Зарегистрироваться</a>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>