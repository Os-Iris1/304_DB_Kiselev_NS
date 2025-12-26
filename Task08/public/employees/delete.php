<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { die('ID не указан'); }

if ($_POST && $_POST['confirm'] === 'yes') {
    // Удаляем связанные данные в правильном порядке
    $pdo->prepare("DELETE FROM completed_services WHERE employee_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM working_hours WHERE employee_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM employees WHERE employee_id = ?")->execute([$id]);
    
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE employee_id = ?");
$stmt->execute([$id]);
$master = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Удалить мастера</title></head>
<body>
    <h1>Подтверждение удаления</h1>
    <p>Вы уверены, что хотите удалить мастера <strong><?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></strong>?</p>
    <p>Все связанные данные (график работы и выполненные услуги) также будут удалены.</p>

    <form method="post">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" style="background: #dc3545; color: white; padding: 6px 12px;">Да, удалить</button>
        <a href="../index.php">Отмена</a>
    </form>
</body>
</html>