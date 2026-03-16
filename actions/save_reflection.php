<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $week_start_date = $_POST['week_start_date'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!empty($week_start_date)) {
        $stmt = $pdo->prepare("SELECT id FROM reflections WHERE user_id = ? AND week_start_date = ?");
        $stmt->execute([$user_id, $week_start_date]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $pdo->prepare("UPDATE reflections SET content = ? WHERE user_id = ? AND week_start_date = ?");
            $stmt->execute([$content, $user_id, $week_start_date]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO reflections (user_id, week_start_date, content) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $week_start_date, $content]);
        }
    }
}

header('Location: ../index.php');
exit;