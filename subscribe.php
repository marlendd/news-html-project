<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

if ($cat_id > 0) {
    // Проверяем, существует ли уже такая подписка
    $check = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND category_id = ?");
    $check->execute([$user_id, $cat_id]);
    
    if ($check->rowCount() > 0) {
        // Если уже подписан — отписываем (тоггл)
        $delete = $pdo->prepare("DELETE FROM subscriptions WHERE user_id = ? AND category_id = ?");
        $delete->execute([$user_id, $cat_id]);
    } else {
        // Если не подписан — подписываем
        $insert = $pdo->prepare("INSERT INTO subscriptions (user_id, category_id) VALUES (?, ?)");
        $insert->execute([$user_id, $cat_id]);
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;