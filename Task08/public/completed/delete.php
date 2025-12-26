<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$id = (int)($_GET['id'] ?? 0);
$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$id || !$employee_id) { die('ID и employee_id обязательны'); }

if ($_POST && $_POST['confirm'] === 'yes') {
    $pdo->prepare("DELETE FROM completed_services WHERE completed_service_id = ?")->execute([$id]);
    header("Location: index.php?employee_id=$employee_id");
    exit;
}

$stmt = $pdo->prepare("
    SELECT cs.*, e.first_name, e.last_name
    FROM completed_services cs
    JOIN employees e ON cs.employee_id = e.employee_id
    WHERE cs.completed_service_id = ?
");
$stmt->execute([$id]);
$work = $stmt->fetch();
if (!$work) { die('Работа не найдена'); }
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Удалить работу</title></head>
<body>
    <h1>Удалить работу?</h1>
    <p>
        Мастер: <strong><?= htmlspecialchars($work['last_name'] . ' ' . $work['first_name']) ?></strong><br>
        Услуга: <strong><?= htmlspecialchars($work['service_name']) ?></strong><br>
        Дата: <strong><?= $work['completion_date'] ?></strong><br>
        Сумма: <strong><?= number_format($work['price'], 0, ',', ' ') ?> ₽</strong>
    </p>
    <form method="post">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" style="background: #dc3545; color: white; padding: 6px 12px;">Да, удалить</button>
        <a href="index.php?employee_id=<?= $employee_id ?>">Отмена</a>
    </form>
</body>
</html>