<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$id = (int)($_GET['id'] ?? 0);
$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$id || !$employee_id) { die('ID и employee_id обязательны'); }

if ($_POST && $_POST['confirm'] === 'yes') {
    $pdo->prepare("DELETE FROM working_hours WHERE schedule_id = ?")->execute([$id]);
    header("Location: index.php?employee_id=$employee_id");
    exit;
}

$stmt = $pdo->prepare("
    SELECT wh.day_of_week, wh.start_time, e.first_name, e.last_name
    FROM working_hours wh
    JOIN employees e ON wh.employee_id = e.employee_id
    WHERE wh.schedule_id = ?
");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { die('Запись не найдена'); }

$days = ['1' => 'Пн', '2' => 'Вт', '3' => 'Ср', '4' => 'Чт', '5' => 'Пт', '6' => 'Сб', '7' => 'Вс'];
$day_str = $days[$row['day_of_week']] ?? $row['day_of_week'];
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Удалить смену</title></head>
<body>
    <h1>Удалить смену?</h1>
    <p>
        Мастер: <strong><?= htmlspecialchars($row['last_name'] . ' ' . $row['first_name']) ?></strong><br>
        День: <strong><?= $day_str ?></strong>, время: <strong><?= htmlspecialchars($row['start_time']) ?></strong>
    </p>
    <form method="post">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" style="background: #dc3545; color: white; padding: 6px 12px;">Да, удалить</button>
        <a href="index.php?employee_id=<?= $employee_id ?>">Отмена</a>
    </form>
</body>
</html>