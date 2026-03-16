<?php
$hour = (int)date('H');
if ($hour >= 5 && $hour < 11) {
    $greeting = 'おはようございます';
} elseif ($hour >= 11 && $hour < 18) {
    $greeting = 'こんにちは';
} else {
    $greeting = 'こんばんは';
}
$username = $_SESSION['username'] ?? 'ゲスト';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スーパーノート</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; margin-bottom:20px;">
    <h1 style="font-size:1.5em; color:var(--primary);"><a href="index.php" style="text-decoration:none; color:inherit;">WebNote</a></h1>
    <nav style="display: flex; align-items: center; gap: 15px;">
        <span style="font-size:0.9em;">
            <?= htmlspecialchars($username) ?>さん、<?= $greeting ?>
        </span>
        <a href="settings.php" style="color:#64748b; text-decoration:none;">設定</a>
        <a href="logout.php" style="color:#ef4444; text-decoration:none;">ログアウト</a>
    </nav>
</header>
<main>