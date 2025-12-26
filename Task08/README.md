# Лабораторная работа 8. Сценарии PHP. Разработка CRUD-приложения для работы с БД

## Требования
- PHP 8.0+
- Расширение `pdo_sqlite`
- SQLite3

## Установка и запуск

1. **Создание базы данных**:

```bash
sqlite3 data/database.db < db_init.sql
```

2. **Запуск локального веб-сервера**:

```bash
php -S localhost:3000 -t public
```

3. **Открыть в браузере**:
http://localhost:3000/

## Структура проекта

```
Task08/
├── public/
│   ├── index.php
│   ├── employees/
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── delete.php
│   ├── schedule/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── delete.php
│   └── completed/
│       ├── index.php
│       ├── create.php
│       ├── edit.php
│       └── delete.php
├── src/
│   └── db.php
├── data/
│   └── database.db
├── db_init.sql
└── README.md
```
