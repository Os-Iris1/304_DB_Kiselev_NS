<?php
$dbFile = __DIR__ . '/database.db';

if (!file_exists($dbFile)) {
    die("Ошибка: файл БД '$dbFile' не найден.\n");
}

try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage() . "\n");
}

$stmt = $pdo->query("
    SELECT employee_id, first_name, last_name
    FROM employees
    ORDER BY last_name, first_name
");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($employees)) {
    die("В базе нет сотрудников.\n");
}

echo "Все сотрудники в базе:\n";
foreach ($employees as $e) {
    $fullName = $e['last_name'] . ' ' . $e['first_name'];
    echo "  {$e['employee_id']}: {$fullName}\n";
}
echo "\nВведите номер сотрудника или нажмите Enter для вывода всех услуг: ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

$selectedEmployeeId = null;
if ($input !== '') {
    if (!ctype_digit($input)) {
        die("Ошибка: введите корректный номер сотрудника (целое число) или оставьте пустым.\n");
    }
    $selectedEmployeeId = (int)$input;

    $exists = $pdo->prepare("SELECT 1 FROM employees WHERE employee_id = ?");
    $exists->execute([$selectedEmployeeId]);
    if (!$exists->fetch()) {
        die("Ошибка: сотрудник с ID $selectedEmployeeId не найден в базе.\n");
    }
}

$sql = "
    SELECT
        e.employee_id AS employee_id,
        e.last_name || ' ' || e.first_name AS full_name,
        cs.completion_date || ' ' || cs.completion_time AS work_date,
        s.name AS service_name,
        cs.price AS price
    FROM completed_services cs
    JOIN employees e ON cs.employee_id = e.employee_id
    JOIN services s ON cs.service_id = s.service_id
";

$params = [];
if ($selectedEmployeeId !== null) {
    $sql .= " WHERE cs.employee_id = ?";
    $params[] = $selectedEmployeeId;
}

$sql .= " ORDER BY e.last_name, e.first_name, cs.completion_date, cs.completion_time";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    if ($selectedEmployeeId !== null) {
        echo "\nУ сотрудника с ID $selectedEmployeeId нет выполненных услуг.\n";
    } else {
        echo "\nВ базе нет выполненных услуг.\n";
    }
    exit;
}

$headers = ['ID сотрудника', 'ФИО', 'Дата работы', 'Услуга', 'Стоимость'];
$data = array_map(function($row) {
    return [
        $row['employee_id'],
        $row['full_name'],
        $row['work_date'],
        $row['service_name'],
        sprintf('%.2f', $row['price'])
    ];
}, $rows);

$colWidths = array_map('strlen', $headers);
foreach ($data as $row) {
    foreach ($row as $i => $cell) {
        $colWidths[$i] = max($colWidths[$i], iconv_strlen($cell, 'UTF-8'));
    }
}

function str_pad_mb($str, $len, $pad = ' ') {
    return $str . str_repeat($pad, max(0, $len - iconv_strlen($str, 'UTF-8')));
}

$top = '+' . implode('+', array_map(fn($w) => str_repeat('-', $w + 2), $colWidths)) . '+';
echo "\n" . $top . "\n";

echo '| ';
foreach ($headers as $i => $h) {
    echo str_pad_mb($h, $colWidths[$i]) . ' | ';
}
echo "\n" . $top . "\n";

foreach ($data as $row) {
    echo '| ';
    foreach ($row as $i => $cell) {
        echo str_pad_mb($cell, $colWidths[$i]) . ' | ';
    }
    echo "\n";
}
echo $top . "\n";
?>