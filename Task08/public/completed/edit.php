<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$id = (int)($_GET['id'] ?? 0);
$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$id || !$employee_id) { die('ID и employee_id обязательны'); }

$stmt = $pdo->prepare("SELECT * FROM completed_services WHERE completed_service_id = ?");
$stmt->execute([$id]);
$work = $stmt->fetch();
if (!$work) { die('Работа не найдена'); }

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
            UPDATE completed_services
            SET service_name = ?, completion_date = ?, price = ?, notes = ?
            WHERE completed_service_id = ?
        ");
        $stmt->execute([$service_name, $completion_date, $price, $notes, $id]);
        header("Location: index.php?employee_id=$employee_id");
        exit;
    } else {
        $message = 'Проверьте обязательные поля';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактировать работу</title></head>
<body>
    <h1>Редактировать работу — <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>
    <?php if ($message): ?>
        <p style="color: red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Название услуги: <input type="text" name="service_name" value="<?= htmlspecialchars($work['service_name']) ?>" required></label>
        </p>
        <p>
            <label>Дата: <input type="date" name="completion_date" value="<?= $work['completion_date'] ?>" required></label>
        </p>
        <p>
            <label>Стоимость: <input type="number" step="0.01" min="0" name="price" value="<?= $work['price'] ?>" required></label>
        </p>
        <p>
            <label>Примечания: <textarea name="notes" rows="3" style="width:100%"><?= htmlspecialchars($work['notes'] ?? '') ?></textarea></label>
        </p>
        <p>
            <button type="submit">Сохранить</button>
            <a href="index.php?employee_id=<?= $employee_id ?>">Отмена</a>
        </p>
    </form>
</body>
</html>