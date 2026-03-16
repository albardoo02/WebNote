<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal_id = $_POST['goal_id'] ?? 0;

    $stmt = $pdo->prepare("DELETE FROM goals WHERE id = ? AND user_id = ?");
    $stmt->execute([$goal_id, $_SESSION['user_id']]);
}

header('Location: ../settings.php');
exit;