<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../src/db.php';

$stmt = $pdo->query("SELECT * FROM employees ORDER BY last_name, first_name");
$masters = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мастера парикмахерской</title>
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
    <h1>Мастера парикмахерской</h1>
    <table>
        <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Специализация</th>
                <th>Активен</th>
                <th>Дата приёма</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($masters as $master): ?>
                <tr>
                    <td><?= htmlspecialchars($master['last_name']) ?></td>
                    <td><?= htmlspecialchars($master['first_name']) ?></td>
                    <td>
                        <?php 
                            $spec_names = ['male' => 'Мужской мастер', 'female' => 'Женский мастер', 'universal' => 'Универсал'];
                            echo htmlspecialchars($spec_names[$master['specialization']] ?? $master['specialization']);
                        ?>
                    </td>
                    <td><?= $master['is_active'] ? '✔️' : '❌' ?></td>
                    <td><?= $master['hire_date'] ?></td>
                    <td class="actions">
                        <a href="employees/edit.php?id=<?= $master['employee_id'] ?>" class="btn">Редактировать</a>
                        <a href="employees/delete.php?id=<?= $master['employee_id'] ?>" class="btn btn-delete" onclick="return confirm('Удалить мастера?')">Удалить</a>
                        <a href="schedule/index.php?employee_id=<?= $master['employee_id'] ?>" class="btn">График</a>
                        <a href="completed/index.php?employee_id=<?= $master['employee_id'] ?>" class="btn">Выполненные работы</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="employees/create.php" class="btn">➕ Добавить мастера</a></p>
</body>
</html>