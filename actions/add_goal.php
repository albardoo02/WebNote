<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO goals (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
    }
}

header('Location: ../settings.php');
exit;