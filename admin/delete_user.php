<?php
require_once '../includes/db.php';

// Проверка: только админ
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { 
    die("Доступ запрещен"); 
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Запрещаем удалять самого себя
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: admin_users.php");
exit();