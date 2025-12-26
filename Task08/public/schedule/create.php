<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$employee_id = (int)($_GET['employee_id'] ?? 0);
if (!$employee_id) { die('employee_id обязателен'); }

$stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$master = $stmt->fetch();
if (!$master) { die('Мастер не найден'); }

$message = '';
if ($_POST) {
    $day = (int)($_POST['day_of_week'] ?? 0);
    $start = $_POST['start_time'] ?? '';
    $end = $_POST['end_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (in_array($day, range(1, 7)) && $start && $end && $end > $start) {
        $stmt = $pdo->prepare("
            INSERT INTO working_hours (employee_id, day_of_week, start_time, end_time, notes)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$employee_id, $day, $start, $end, $notes]);
        header("Location: index.php?employee_id=$employee_id");
        exit;
    } else {
        $message = 'Проверьте корректность данных: день 1–7, время в формате ЧЧ:ММ, конец > начала.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Новая смена — <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></title></head>
<body>
    <h1>Добавить смену для: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>
    <?php if ($message): ?>
        <p style="color: red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>
                День недели:
                <select name="day_of_week" required>
                    <option value="">—</option>
                    <?php $days = ['1' => 'Понедельник', '2' => 'Вторник', '3' => 'Среда', '4' => 'Четверг', '5' => 'Пятница', '6' => 'Суббота', '7' => 'Воскресенье']; ?>
                    <?php foreach ($days as $k => $v): ?>
                        <option value="<?= $k ?>"><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
        <p>
            <label>Начало: <input type="time" name="start_time" required></label>
        </p>
        <p>
            <label>Конец: <input type="time" name="end_time" required></label>
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