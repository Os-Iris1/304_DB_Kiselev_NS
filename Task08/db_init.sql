DROP TABLE IF EXISTS completed_services;
DROP TABLE IF EXISTS working_hours;
DROP TABLE IF EXISTS employees;


CREATE TABLE employees (
    employee_id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    specialization TEXT NOT NULL,
    hire_date DATE NOT NULL DEFAULT CURRENT_DATE,
    is_active BOOLEAN NOT NULL DEFAULT 1
);

CREATE TABLE working_hours (
    schedule_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    day_of_week INTEGER NOT NULL CHECK(day_of_week BETWEEN 1 AND 7),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    CHECK (end_time > start_time)
);

CREATE TABLE completed_services (
    completed_service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    service_name TEXT NOT NULL,
    completion_date DATE NOT NULL DEFAULT CURRENT_DATE,
    price DECIMAL(10,2) NOT NULL CHECK(price >= 0),
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

INSERT INTO employees (first_name, last_name, specialization, hire_date, is_active) VALUES
('Иван', 'Петров', 'Мужской мастер', '2023-01-15', 1),
('Мария', 'Сидорова', 'Женский мастер', '2023-02-20', 1),
('Анна', 'Козлова', 'Универсал', '2023-03-10', 1),
('Сергей', 'Иванов', 'Мужской мастер', '2022-11-05', 0),
('Ольга', 'Николаева', 'Женский мастер', '2024-01-10', 1);

INSERT INTO working_hours (employee_id, day_of_week, start_time, end_time, notes) VALUES
(1, 1, '09:00', '18:00', 'Понедельник'),
(1, 2, '09:00', '18:00', 'Вторник'),
(1, 3, '09:00', '18:00', 'Среда'),
(1, 4, '09:00', '18:00', 'Четверг'),
(1, 5, '09:00', '17:00', 'Пятница'),
(2, 2, '10:00', '19:00', 'Вторник'),
(2, 3, '10:00', '19:00', 'Среда'),
(2, 4, '10:00', '19:00', 'Четверг'),
(2, 5, '10:00', '19:00', 'Пятница'),
(2, 6, '10:00', '16:00', 'Суббота'),
(3, 1, '09:30', '18:30', 'Понедельник'),
(3, 3, '09:30', '18:30', 'Среда'),
(3, 5, '09:30', '18:30', 'Пятница'),
(4, 1, '08:00', '16:00', 'Понедельник'),
(4, 2, '08:00', '16:00', 'Вторник'),
(4, 3, '08:00', '16:00', 'Среда'),
(5, 1, '08:30', '17:30', 'Понедельник'),
(5, 2, '08:30', '17:30', 'Вторник'),
(5, 3, '08:30', '17:30', 'Среда'),
(5, 4, '08:30', '17:30', 'Четверг'),
(5, 5, '08:30', '16:30', 'Пятница');

INSERT INTO completed_services (employee_id, service_name, completion_date, price, notes) VALUES
(1, 'Мужская стрижка', '2024-12-20', 800.00, 'Классическая стрижка'),
(1, 'Детская стрижка', '2024-12-19', 600.00, 'Мальчик 7 лет'),
(2, 'Женская стрижка', '2024-12-20', 1500.00, 'Каре'),
(2, 'Окрашивание волос', '2024-12-19', 2500.00, 'В блонд'),
(3, 'Стрижка и укладка', '2024-12-20', 1200.00, 'Стрижка + укладка феном'),
(3, 'Тонирование', '2024-12-21', 1800.00, 'В шатуш'),
(4, 'Бритье', '2024-12-21', 500.00, 'Королевское бритье'),
(4, 'Коррекция бороды', '2024-12-22', 400.00, ''),
(5, 'Женская стрижка', '2024-12-21', 1500.00, 'Стрижка лесенкой'),
(5, 'Укладка', '2024-12-24', 1000.00, 'На свадьбу'),
(5, 'Мелирование', '2024-12-23', 3200.00, 'На длинные волосы');