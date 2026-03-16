<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$week_start_str = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$start_of_week = new DateTime($week_start_str);

$hour = date('H');
if ($hour < 11) {
    $greeting = 'おはようございます、';
} elseif ($hour < 18) {
    $greeting = 'こんにちは、';
} else {
    $greeting = 'こんばんは、';
}

$month = $start_of_week->format('n');
$first_day = (clone $start_of_week)->setDate($start_of_week->format('Y'), $start_of_week->format('n'), 1);
$first_w = $first_day->format('w');
$day = $start_of_week->format('j');
$week_number = ceil(($day + $first_w) / 7);
$pdf_title_date = $month . '月第' . $week_number . '週';

$week_dates = [];
for ($i = 0; $i < 5; $i++) $week_dates[] = (clone $start_of_week)->modify("+$i days");
$week_start_str = $start_of_week->format('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY id");
$stmt->execute([$user_id]);
$goals = $stmt->fetchAll();

$records_data = [];
if ($goals) {
    $goal_ids = array_column($goals, 'id');
    $placeholders = implode(',', array_fill(0, count($goal_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM records WHERE goal_id IN ($placeholders)");
    $stmt->execute($goal_ids);
    foreach ($stmt->fetchAll() as $r) $records_data[$r['goal_id']][$r['date']] = $r['status'];
}

$stmt = $pdo->prepare("SELECT content FROM reflections WHERE user_id = ? AND week_start_date = ?");
$stmt->execute([$user_id, $week_start_str]);
$reflection = $stmt->fetchColumn() ?: '';

include 'includes/header.php';
?>

<h2><?= $pdf_title_date ?>のスーパーノート</h2>
<table>
    <thead>
        <tr>
            <th>項目</th>
            <?php foreach ($week_dates as $d): ?>
                <th><?= ['月','火','水','木','金'][$d->format('N')-1] ?><br><small><?= $d->format('m/d') ?></small></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($goals as $goal): ?>
        <tr>
            <td><?= htmlspecialchars($goal['content']) ?></td>
            <?php foreach ($week_dates as $d): ?>
                <?php $d_str = $d->format('Y-m-d'); $current = $records_data[$goal['id']][$d_str] ?? ''; ?>
                <td>
                    <form action="actions/update_status.php" method="post">
                        <input type="hidden" name="goal_id" value="<?= $goal['id'] ?>">
                        <input type="hidden" name="date" value="<?= $d_str ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">-</option>
                            <?php foreach (['◎', '◯', '△', '✕'] as $s): ?>
                                <option value="<?= $s ?>" <?= $current === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3 style="margin-top:40px;">今週の反省</h3>
<form action="actions/save_reflection.php" method="post">
    <input type="hidden" name="week_start_date" value="<?= $week_start_str ?>">
    <textarea name="content" rows="6" placeholder="今週の反省や成果を記入しましょう..."><?= htmlspecialchars($reflection) ?></textarea>
    <div style="margin-top:15px; display:flex; justify-content: space-between;">
        <a href="download_pdf.php?week=<?= $week_start_str ?>" class="btn">PDFで保存</a>
        <button type="submit" class="btn btn-save">反省を保存</button>
    </div>
</form>
<?php include 'includes/footer.php'; ?>