<?php
session_start();
require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $row = $stmt->fetch();

    if ($row && password_verify($pass, $row['password_hash'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = "ユーザー名またはパスワードが正しくありません。";
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>ログイン</title><link rel="stylesheet" href="./css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f1f5f9;">
<div style="background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); width:100%; max-width:400px;">
    <h2 style="margin-top:0;">ログイン</h2>
    <?php if(isset($_GET['success'])): ?><p style="color:#059669;">登録が完了しました。ログインしてください。</p><?php endif; ?>
    <?php if($error): ?><p style="color:#ef4444;"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label style="display:block; margin-bottom:5px;">ユーザー名</label>
        <input type="text" name="username" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px;">
        <label style="display:block; margin-bottom:5px;">パスワード</label>
        <input type="password" name="password" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:6px;">
        <button type="submit" class="btn" style="width:100%;">ログイン</button>
    </form>
    <p style="text-align:center; margin-top:20px;"><a href="register.php" style="color:#64748b;">新規登録はこちら</a></p>
</div></body></html>