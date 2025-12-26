<?php

$database_path = __DIR__ . '/../data/database.db';
$pdo = new PDO("sqlite:$database_path");

$pdo->exec("PRAGMA encoding = 'UTF-8';");
$pdo->exec("PRAGMA foreign_keys = ON;");

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>