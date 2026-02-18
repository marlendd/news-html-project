<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка прав (админ=1, модератор=2)
// Если сессия пустая или роль слишком низкая — блокируем доступ
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] > 2) {
    header("Location: ../index.php");
    exit();
}

// Логика удаления
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt_img = $pdo->prepare("SELECT image_path FROM news WHERE id = ?");
    $stmt_img->execute([$id]);
    $news_item = $stmt_img->fetch();
    
    if ($news_item) {
        // Удаляем запись из базы
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
        
        // Если у новости был локальный файл картинки — удаляем его с диска
        if (!empty($news_item['image_path']) && !filter_var($news_item['image_path'], FILTER_VALIDATE_URL)) {
            $file_to_delete = '../assets/img/' . $news_item['image_path'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }
    }
}

header("Location: admin_news.php");
exit();