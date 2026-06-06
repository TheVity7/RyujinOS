<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Creates the database schema. Generates driver-appropriate SQL so the same
 * definitions work on MySQL/MariaDB (production) and SQLite (local testing).
 */
final class Migrator
{
    public static function migrate(): void
    {
        $sqlite = Database::driver() === 'sqlite';
        $pk = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY';
        $bool = $sqlite ? 'INTEGER NOT NULL DEFAULT 0' : 'TINYINT(1) NOT NULL DEFAULT 0';
        $money = $sqlite ? 'REAL NOT NULL DEFAULT 0' : 'DECIMAL(12,2) NOT NULL DEFAULT 0';
        $engine = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $tables = [];

        $tables['users'] = "CREATE TABLE IF NOT EXISTS users (
            id {$pk},
            username VARCHAR(64) NOT NULL,
            uuid VARCHAR(64) NULL,
            email VARCHAR(191) NULL,
            password VARCHAR(255) NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'member',
            balance {$money},
            avatar VARCHAR(255) NULL,
            two_factor {$bool},
            last_login_ip VARCHAR(64) NULL,
            last_login_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL
        ){$engine}";

        $tables['categories'] = "CREATE TABLE IF NOT EXISTS categories (
            id {$pk},
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(120) NOT NULL,
            description TEXT NULL,
            icon VARCHAR(100) NULL,
            sort_order INT NOT NULL DEFAULT 0
        ){$engine}";

        $tables['products'] = "CREATE TABLE IF NOT EXISTS products (
            id {$pk},
            category_id INT NOT NULL DEFAULT 0,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(170) NOT NULL,
            description TEXT NULL,
            image VARCHAR(255) NULL,
            price {$money},
            sale_price {$money},
            commands TEXT NULL,
            stock INT NOT NULL DEFAULT -1,
            is_active {$bool},
            featured {$bool},
            sort_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['orders'] = "CREATE TABLE IF NOT EXISTS orders (
            id {$pk},
            user_id INT NOT NULL,
            product_id INT NOT NULL DEFAULT 0,
            product_name VARCHAR(150) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            total {$money},
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            delivery VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['credit_transactions'] = "CREATE TABLE IF NOT EXISTS credit_transactions (
            id {$pk},
            user_id INT NOT NULL,
            amount {$money},
            type VARCHAR(20) NOT NULL,
            description VARCHAR(255) NULL,
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['payments'] = "CREATE TABLE IF NOT EXISTS payments (
            id {$pk},
            user_id INT NOT NULL,
            order_ref VARCHAR(64) NOT NULL,
            amount {$money},
            credits {$money},
            provider VARCHAR(30) NOT NULL DEFAULT 'shopier',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            payment_id VARCHAR(120) NULL,
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['posts'] = "CREATE TABLE IF NOT EXISTS posts (
            id {$pk},
            title VARCHAR(200) NOT NULL,
            slug VARCHAR(220) NOT NULL,
            excerpt VARCHAR(300) NULL,
            body TEXT NULL,
            image VARCHAR(255) NULL,
            author VARCHAR(64) NULL,
            views INT NOT NULL DEFAULT 0,
            comments INT NOT NULL DEFAULT 0,
            published {$bool},
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['support_tickets'] = "CREATE TABLE IF NOT EXISTS support_tickets (
            id {$pk},
            user_id INT NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'open',
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['support_replies'] = "CREATE TABLE IF NOT EXISTS support_replies (
            id {$pk},
            ticket_id INT NOT NULL,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            is_staff {$bool},
            created_at DATETIME NOT NULL
        ){$engine}";

        $tables['settings'] = "CREATE TABLE IF NOT EXISTS settings (
            " . ($sqlite ? '`key` VARCHAR(100) PRIMARY KEY' : '`key` VARCHAR(100) NOT NULL PRIMARY KEY') . ",
            `value` TEXT NULL
        ){$engine}";

        foreach ($tables as $sql) {
            Database::run($sql);
        }
    }

    public static function dropAll(): void
    {
        $tables = ['support_replies', 'support_tickets', 'posts', 'payments', 'credit_transactions', 'orders', 'products', 'categories', 'settings', 'users'];
        foreach ($tables as $table) {
            Database::run('DROP TABLE IF EXISTS ' . $table);
        }
    }
}
