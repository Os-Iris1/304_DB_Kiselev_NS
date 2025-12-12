<?php
$dbFile = __DIR__ . '/database.db';

if (!file_exists($dbFile)) {
    die('Ошибка: БД не найдена.');
}

try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к БД: ' . $e->getMessage());
}

$employees = $pdo->query("
    SELECT employee_id, first_name, last_name
    FROM employees
    ORDER BY last_name, first_name
")->fetchAll(PDO::FETCH_ASSOC);

$selectedEmployeeId = isset($_GET['employee_id']) && is_numeric($_GET['employee_id'])
    ? (int)$_GET['employee_id']
    : null;

$sql = "
    SELECT
        e.employee_id,
        e.last_name || ' ' || e.first_name AS full_name,
        cs.completion_date || ' ' || cs.completion_time AS work_date,
        s.name AS service_name,
        cs.price AS price
    FROM completed_services cs
    JOIN employees e ON cs.employee_id = e.employee_id
    JOIN services s ON cs.service_id = s.service_id
";

$params = [];
if ($selectedEmployeeId) {
    $sql .= " WHERE cs.employee_id = ?";
    $params = [$selectedEmployeeId];
}

$sql .= " ORDER BY e.last_name, e.first_name, cs.completion_date, cs.completion_time";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выполненные услуги парикмахерской</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        select, button { padding: 6px 12px; font-size: 1rem; }
        .container { max-width: 1200px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Выполненные услуги парикмахерской</h1>

        <form method="GET">
            <label for="employee_id">Сотрудник:</label>
            <select name="employee_id" id="employee_id">
                <option value="">— Все сотрудники —</option>
                <?php foreach ($employees as $emp): ?>
                    <?php $fullName = $emp['last_name'] . ' ' . $emp['first_name']; ?>
                    <option value="<?= htmlspecialchars($emp['employee_id']) ?>" 
                        <?= $selectedEmployeeId == $emp['employee_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fullName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Применить</button>
        </form>

        <?php if (!empty($services)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID сотрудника</th>
                        <th>ФИО</th>
                        <th>Дата выполнения</th>
                        <th>Услуга</th>
                        <th>Стоимость (₽)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['employee_id']) ?></td>
                            <td><?= htmlspecialchars($service['full_name']) ?></td>
                            <td><?= htmlspecialchars($service['work_date']) ?></td>
                            <td><?= htmlspecialchars($service['service_name']) ?></td>
                            <td><?= number_format($service['price'], 2, ',', ' ') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>
                <?php if ($selectedEmployeeId): ?>
                    У сотрудника с ID <?= $selectedEmployeeId ?> нет выполненных услуг.
                <?php else: ?>
                    В базе нет выполненных услуг.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>