# NewsHub - Новостной портал

Веб-приложение для публикации и управления новостями с системой категорий, пользователей и подписок.

## Возможности

- Просмотр новостей с фильтрацией по категориям
- Поиск по новостям
- Регистрация и авторизация пользователей
- Личный кабинет пользователя
- Подписка на категории новостей
- Административная панель для управления новостями и пользователями

## Технологии

- PHP 7.4+
- MySQL
- Bootstrap 5
- Bootstrap Icons

## Структура проекта

```
├── admin/              # Административная панель
│   ├── admin_news.php  # Управление новостями
│   ├── admin_users.php # Управление пользователями
│   ├── add_news.php    # Добавление новости
│   ├── edit.php        # Редактирование новости
│   └── delete.php      # Удаление новости
├── assets/
│   └── img/            # Изображения новостей
├── includes/
│   ├── db.php          # Подключение к БД
│   ├── header.php      # Шапка сайта
│   └── footer.php      # Подвал сайта
├── index.php           # Главная страница
├── article.php         # Страница статьи
├── login.php           # Авторизация
├── register.php        # Регистрация
├── profile.php         # Личный кабинет
└── subscribe.php       # Подписка на категории
```

## Установка

1. Клонируйте репозиторий:
```bash
git clone <repository-url>
cd <project-folder>
```

2. Создайте базу данных MySQL:
```sql
CREATE DATABASE news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Импортируйте структуру БД (создайте таблицы):
```sql
-- Таблица пользователей
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица категорий
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    icon_class VARCHAR(50)
);

-- Таблица новостей
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    short_text TEXT,
    full_text TEXT,
    image_path VARCHAR(255),
    category_id INT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

4. Настройте подключение к БД в файле `includes/db.php`:
```php
$host = 'localhost';
$db   = 'news';
$user = 'your_username';
$pass = 'your_password';
```

5. Настройте веб-сервер (Apache/Nginx) для работы с PHP

6. Откройте проект в браузере

## Использование

### Для пользователей:
- Зарегистрируйтесь через форму регистрации
- Войдите в систему
- Просматривайте новости, используйте поиск и фильтры
- Подписывайтесь на интересующие категории

### Для администраторов:
- Войдите с правами администратора (role_id = 1)
- Перейдите в административную панель
- Управляйте новостями и пользователями

## Требования

- PHP 7.4 или выше
- MySQL 5.7 или выше
- Веб-сервер (Apache/Nginx)
- Расширения PHP: PDO, pdo_mysql

## Безопасность

- Пароли хранятся в виде хэшей (password_hash)
- Используются подготовленные запросы (prepared statements) для защиты от SQL-инъекций
- Экранирование HTML для защиты от XSS

## Лицензия

Проект создан в образовательных целях.
