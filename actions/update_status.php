<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $goal_id = $_POST['goal_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    if (empty($status)) {
        $stmt = $pdo->prepare("DELETE FROM records WHERE goal_id = ? AND date = ?");
        $stmt->execute([$goal_id, $date]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO records (goal_id, date, status) VALUES (?, ?, ?)
            ON CONFLICT(goal_id, date) DO UPDATE SET status = excluded.status
        ");
        $stmt->execute([$goal_id, $date, $status]);
    }
}
header('Location: ../index.php');