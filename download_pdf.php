<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) die('Unauthorized');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$week_start_str = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$start_of_week = new DateTime($week_start_str);

$month = $start_of_week->format('n');
$first_day = (clone $start_of_week)->setDate($start_of_week->format('Y'), $start_of_week->format('n'), 1);
$first_w = $first_day->format('w');
$day = $start_of_week->format('j');
$week_number = ceil(($day + $first_w) / 7);
$pdf_title_date = $month . '月第' . $week_number . '週';

$hour = (int)date('H');
$greeting = ($hour >= 5 && $hour < 11) ? 'おはようございます' : (($hour >= 11 && $hour < 18) ? 'こんにちは' : 'こんばんは');

$week_dates = [];
for ($i = 0; $i < 5; $i++) $week_dates[] = (clone $start_of_week)->modify("+$i days");

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

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "ipaexg", sans-serif; color: #333; }
        h1 { text-align: center; font-size: 18pt; margin-bottom: 20px; }
        .info { text-align: right; margin-bottom: 15px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        .reflection-title { margin-top: 25px; font-weight: bold; border-bottom: 1px solid #333; }
        .reflection-content { margin-top: 10px; line-height: 1.6; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>' . $pdf_title_date . ' のスーパーノート</h1>
    <div class="info">作成者: ' . htmlspecialchars($username) . '</div>

    <table>
        <thead>
            <tr>
                <th style="width:30%;">項目</th>';
                foreach ($week_dates as $date) {
                    $html .= '<th>' . ['月','火','水','木','金'][$date->format('N')-1] . '<br>' . $date->format('m/d') . '</th>';
                }
$html .= '  </tr>
        </thead>
        <tbody>';
            foreach ($goals as $goal) {
                $html .= '<tr><td>' . htmlspecialchars($goal['content']) . '</td>';
                foreach ($week_dates as $date) {
                    $status = $records_data[$goal['id']][$date->format('Y-m-d')] ?? '';
                    $html .= '<td>' . htmlspecialchars($status) . '</td>';
                }
                $html .= '</tr>';
            }
$html .= '
        </tbody>
    </table>
    <div class="reflection-title">【今週の反省】</div>
    <div class="reflection-content">' . nl2br(htmlspecialchars($reflection)) . '</div>
</body>
</html>';

try {
    $fontPath = __DIR__ . '/fonts';
    $mpdf = new \Mpdf\Mpdf([
        'fontDir' => array_merge((new Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [$fontPath]),
        'fontdata' => (new Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
            'ipaexg' => ['R' => 'ipaexg.ttf']
        ],
        'default_font' => 'ipaexg',
        'mode' => 'ja-JP',
        'format' => 'A4-L'
    ]);

    $mpdf->WriteHTML($html);
    $mpdf->Output('supernote_' . $username . '_' . $pdf_title_date. '.pdf', 'D');
} catch (\Exception $e) {
    die('PDFエラー: ' . $e->getMessage());
}