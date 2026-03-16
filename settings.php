<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY id");
$stmt->execute([$user_id]);
$goals = $stmt->fetchAll();

include 'includes/header.php';
?>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>項目の設定</h2>
    <a href="index.php" class="btn" style="background:#64748b;">戻る</a>
</div>

<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <form action="actions/add_goal.php" method="post" style="display:flex; gap:10px; margin-bottom:30px;">
        <input type="text" name="content" placeholder="新しい目標項目を入力..." required style="flex:1; padding:10px; border:1px solid #cbd5e1; border-radius:4px;">
        <button type="submit" class="btn">追加</button>
    </form>

    <h3>登録済みの項目</h3>
    <?php if (empty($goals)): ?>
        <p style="color:#64748b;">まだ項目がありません。</p>
    <?php else: ?>
        <ul style="list-style:none; padding:0;">
            <?php foreach ($goals as $goal): ?>
                <li style="display:flex; justify-content:space-between; padding:12px; border-bottom:1px solid #e2e8f0;">
                    <span><?= htmlspecialchars($goal['content']) ?></span>
                    <form action="actions/delete_goal.php" method="post" onsubmit="return confirm('本当に削除しますか？');">
                        <input type="hidden" name="goal_id" value="<?= $goal['id'] ?>">
                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:0.9em;">削除</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>