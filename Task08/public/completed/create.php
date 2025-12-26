<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$employee_id) { die('employee_id обязателен'); }

$stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$master = $stmt->fetch();

$message = '';
if ($_POST) {
    $service_name = trim($_POST['service_name'] ?? '');
    $completion_date = $_POST['completion_date'];
    $price = floatval($_POST['price']);
    $notes = trim($_POST['notes'] ?? '');

    if ($service_name && $completion_date && $price >= 0) {
        $stmt = $pdo->prepare("
            INSERT INTO completed_services (employee_id, service_name, completion_date, price, notes)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$employee_id, $service_name, $completion_date, $price, $notes]);
        header("Location: index.php?employee_id=$employee_id");
        exit;
    } else {
        $message = 'Проверьте обязательные поля';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Новая работа — <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></title>
</head>
<body>
    <h1>Добавить выполненную работу: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>
    <?php if ($message): ?>
        <p style="color: red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Название услуги: <input type="text" name="service_name" required></label>
        </p>
        <p>
            <label>Дата выполнения: <input type="date" name="completion_date" value="<?= date('Y-m-d') ?>" required></label>
        </p>
        <p>
            <label>Стоимость (₽): <input type="number" step="0.01" min="0" name="price" required></label>
        </p>
        <p>
            <label>Примечания: <textarea name="notes" rows="2" style="width:100%"></textarea></label>
        </p>
        <p>
            <button type="submit">Сохранить</button>
            <a href="index.php?employee_id=<?= $employee_id ?>">Отмена</a>
        </p>
    </form>
</body>
</html>