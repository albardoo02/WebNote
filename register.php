<?php
session_start();
require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user && $pass) {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$user, $hash]);
            header('Location: login.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error = "そのユーザー名は既に使用されています。";
        }
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>新規登録</title><link rel="stylesheet" href="./css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f1f5f9;">
<div style="background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); width:100%; max-width:400px;">
    <h2 style="margin-top:0;">新規登録</h2>
    <?php if($error): ?><p style="color:#ef4444;"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label style="display:block; margin-bottom:5px;">ユーザー名</label>
        <input type="text" name="username" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px;">
        <label style="display:block; margin-bottom:5px;">パスワード</label>
        <input type="password" name="password" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px;">
        <button type="submit" class="btn" style="width:100%;">登録する</button>
    </form>
    <p style="text-align:center; margin-top:20px;"><a href="login.php" style="color:#64748b;">ログインはこちら</a></p>
</div></body></html>