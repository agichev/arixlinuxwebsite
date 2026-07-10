CREATE DATABASE IF NOT EXISTS arix_forum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arix_forum;

CREATE TABLE boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO boards (name, description, slug, sort_order) VALUES
('Общие вопросы', 'Вопросы по Arix Linux и обсуждения общего характера', 'general', 1),
('Установка системы', 'Помощь с установкой Arix Linux', 'installation', 2),
('Настройка и конфигурация', 'Настройка системы, конфиги, тюнинг', 'configuration', 3),
('Программы и пакеты', 'Обсуждение ПО, пакетов, репозиториев', 'software', 4),
('Железо и драйверы', 'Совместимость оборудования, драйверы, ядро', 'hardware', 5),
('Сети и безопасность', 'Настройка сетей, фаерволы, безопасность', 'network', 6),
('Скрипты и терминал', 'Bash-скрипты, автоматизация, консольные утилиты', 'scripts', 7),
('Юмор и оффтоп', 'Всё остальное, что не вошло в другие разделы', 'offtopic', 8);
