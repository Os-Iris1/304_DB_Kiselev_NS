<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$employee_id) { die('employee_id обязателен'); }

$stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$master = $stmt->fetch();
if (!$master) { die('Мастер не найден'); }

$stmt = $pdo->prepare("
    SELECT * FROM working_hours 
    WHERE employee_id = ? 
    ORDER BY day_of_week, start_time
");
$stmt->execute([$employee_id]);
$schedules = $stmt->fetchAll();

$days = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>График: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></title>
 <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 20px; }
        h1 { border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 1.5em 0; }
        th, td { border: 1px solid #ccc; padding: 10px 12px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: 600; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .actions a { margin: 0 4px; text-decoration: none; }
        .btn { padding: 6px 12px; background: #6c757d; color: white; border-radius: 3px; border: 1px solid #dee2e6; }
        .btn:hover { background: #5a6268; }
        .btn-delete { background: #868e96; }
        .btn-delete:hover { background: #727b84; }
        .form-group { margin-bottom: 15px; }
</style>
</head>
<body>
    <h1>График работы: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>

    <table>
        <thead>
            <tr>
                <th>День</th>
                <th>Начало</th>
                <th>Конец</th>
                <th>Примечания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($schedules)): ?>
                <tr><td colspan="5">График не задан.</td></tr>
            <?php else: ?>
                <?php foreach ($schedules as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($days[$s['day_of_week']] ?? $s['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($s['start_time']) ?></td>
                        <td><?= htmlspecialchars($s['end_time']) ?></td>
                        <td><?= htmlspecialchars($s['notes'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $s['schedule_id'] ?>&employee_id=<?= $employee_id ?>" class="btn">✏️</a>
                            <a href="delete.php?id=<?= $s['schedule_id'] ?>&employee_id=<?= $employee_id ?>" 
                               class="btn btn-del" onclick="return confirm('Удалить запись графика?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p>
        <a href="create.php?employee_id=<?= $employee_id ?>" class="btn">➕ Добавить смену</a>
        <a href="../index.php">← К списку мастеров</a>
    </p>
</body>
</html>