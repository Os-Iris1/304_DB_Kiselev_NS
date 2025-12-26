<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../src/db.php';

$message = '';

if ($_POST) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $specialization = $_POST['specialization'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $hire_date = $_POST['hire_date'] ?? date('Y-m-d');

    if ($first_name && $last_name && $specialization) {
        $stmt = $pdo->prepare("
            INSERT INTO employees (first_name, last_name, specialization, is_active, hire_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$first_name, $last_name, $specialization, $is_active, $hire_date]);
        header('Location: ../index.php');
        exit;
    } else {
        $message = 'Заполните обязательные поля!';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Добавить мастера</title></head>
<body>
    <h1>Добавить мастера</h1>
    <?php if ($message): ?>
        <p style="color: red"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Имя: <input type="text" name="first_name" required></label>
        </p>
        <p>
            <label>Фамилия: <input type="text" name="last_name" required></label>
        </p>
        <p>
            <label>Специализация: 
                <select name="specialization" required>
                    <option value="male">Мужской мастер</option>
                    <option value="female">Женский мастер</option>
                    <option value="universal">Универсал</option>
                </select>
            </label>
        </p>
        <p>
            <label><input type="checkbox" name="is_active" checked> Активен</label>
        </p>
        <p>
            <label>Дата приёма: <input type="date" name="hire_date" value="<?= date('Y-m-d') ?>"></label>
        </p>
        <p>
            <button type="submit">Сохранить</button>
            <a href="../index.php">← Назад</a>
        </p>
    </form>
</body>
</html>