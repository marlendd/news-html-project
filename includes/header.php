<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];


$nav_cats = [];
try {
    $nav_cats = $pdo->query("SELECT * FROM categories ORDER BY title ASC")->fetchAll();
} catch (PDOException $e) {

}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewsHub — Агрегатор новостей</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; background-color: #f8f9fa; }
        .content-wrapper { flex: 1 0 auto; }
        footer { flex-shrink: 0; }
        
        .navbar-brand { font-weight: 800; letter-spacing: -1px; }
        .dropdown-menu { border-radius: 12px; }
    </style>
</head>
<body>

<div class="content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="<?= $base_url ?>/index.php">
                <i class="bi bi-lightning-fill text-warning"></i> NEWSHUB
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>/index.php">Главная</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Категории</a>
                        <ul class="dropdown-menu shadow border-0">
                            <?php foreach($nav_cats as $cat): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= $base_url ?>/index.php?category=<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm dropdown-toggle px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> 
                                <?= htmlspecialchars($_SESSION['login']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li>
                                    <a class="dropdown-item" href="<?= $base_url ?>/profile.php">
                                        <i class="bi bi-person me-2"></i>Мой профиль
                                    </a>
                                </li>
                                
                                <?php if($_SESSION['role_id'] <= 2): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Управление</h6></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/admin/admin_news.php">
                                            <i class="bi bi-newspaper me-2"></i>Новости
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if($_SESSION['role_id'] == 1): ?>
                                    <li>
                                        <a class="dropdown-item text-primary" href="<?= $base_url ?>/admin/admin_users.php">
                                            <i class="bi bi-people me-2"></i>Пользователи
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= $base_url ?>/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Выйти
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= $base_url ?>/login.php" class="btn btn-outline-light btn-sm me-2">Вход</a>
                        <a href="<?= $base_url ?>/register.php" class="btn btn-primary btn-sm rounded-pill px-3">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>