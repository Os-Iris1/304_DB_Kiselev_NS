<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { die('ID не указан'); }

$stmt = $pdo->prepare("SELECT * FROM employees WHERE employee_id = ?");
$stmt->execute([$id]);
$master = $stmt->fetch();

if (!$master) { die('Мастер не найден'); }

$message = '';

if ($_POST) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $specialization = $_POST['specialization'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $hire_date = $_POST['hire_date'];

    if ($first_name && $last_name && $specialization) {
        $stmt = $pdo->prepare("
            UPDATE employees
            SET first_name = ?, last_name = ?, specialization = ?, 
         	is_active = ?, hire_date = ?
            WHERE employee_id = ?
        ");
        $stmt->execute([$first_name, $last_name, $specialization, 
                       $is_active, $hire_date, $id]);
        header('Location: ../index.php');
        exit;
    } else {
        $message = 'Заполните обязательные поля!';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактировать мастера</title></head>
<body>
    <h1>Редактировать: <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name']) ?></h1>
    <?php if ($message): ?>
        <p style="color: red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Имя: <input type="text" name="first_name" value="<?= htmlspecialchars($master['first_name']) ?>" required></label>
        </p>
        <p>
            <label>Фамилия: <input type="text" name="last_name" value="<?= htmlspecialchars($master['last_name']) ?>" required></label>
        </p>
        <p>
            <label>Специализация: 
                <select name="specialization" required>
                    <option value="male" <?= $master['specialization'] == 'male' ? 'selected' : '' ?>>Мужской мастер</option>
                    <option value="female" <?= $master['specialization'] == 'female' ? 'selected' : '' ?>>Женский мастер</option>
                    <option value="universal" <?= $master['specialization'] == 'universal' ? 'selected' : '' ?>>Универсал</option>
                </select>
            </label>
        </p>
        <p>
            <label><input type="checkbox" name="is_active" <?= $master['is_active'] ? 'checked' : '' ?>> Активен</label>
        </p>
        <p>
            <label>Дата приёма: <input type="date" name="hire_date" value="<?= $master['hire_date'] ?>"></label>
        </p>
        <p>
            <button type="submit">Сохранить</button>
            <a href="../index.php">← Отмена</a>
        </p>
    </form>
</body>
</html>