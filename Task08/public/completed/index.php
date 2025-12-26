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
    SELECT cs.*
    FROM completed_services cs
    WHERE cs.employee_id = ?
    ORDER BY cs.completion_date DESC
");
$stmt->execute([$employee_id]);
$works = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выполненные работы: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></title>
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
    <h1>Выполненные работы: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Услуга</th>
                <th>Стоимость</th>
                <th>Примечания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($works)): ?>
                <tr><td colspan="5">Работ пока нет.</td></tr>
            <?php else: ?>
                <?php foreach ($works as $w): ?>
                    <tr>
                        <td><?= htmlspecialchars($w['completion_date']) ?></td>
                        <td><?= htmlspecialchars($w['service_name']) ?></td>
                        <td><?= number_format($w['price'], 0, ',', ' ') ?> ₽</td>
                        <td class="notes"><?= htmlspecialchars($w['notes'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $w['completed_service_id'] ?>&employee_id=<?= $employee_id ?>" class="btn btn-edit">✏️</a>
                            <a href="delete.php?id=<?= $w['completed_service_id'] ?>&employee_id=<?= $employee_id ?>" 
                               class="btn btn-del" onclick="return confirm('Удалить работу?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p>
        <a href="create.php?employee_id=<?= $employee_id ?>" class="btn">➕ Добавить работу</a>
        <a href="../index.php">← К мастерам</a>
    </p>
</body>
</html>