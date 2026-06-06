<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * PDO database wrapper supporting MySQL (production) and SQLite (local tests).
 */
final class Database
{
    private static ?PDO $pdo = null;
    private static int $txLevel = 0;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $driver = (string) config('db.connection', 'mysql');

        try {
            if ($driver === 'sqlite') {
                $path = (string) config('db.sqlite');
                if (!str_starts_with($path, '/') && !preg_match('/^[A-Za-z]:/', $path)) {
                    $path = base_path($path);
                }
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $pdo = new PDO('sqlite:' . $path);
                $pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    config('db.host'),
                    config('db.port'),
                    config('db.database'),
                );
                $pdo = new PDO($dsn, (string) config('db.username'), (string) config('db.password'));
            }
        } catch (PDOException $e) {
            if (config('app.debug')) {
                throw $e;
            }
            http_response_code(500);
            exit('Veritabanına bağlanılamadı. Lütfen yapılandırmayı kontrol edin.');
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        self::$pdo = $pdo;
        return $pdo;
    }

    public static function driver(): string
    {
        return (string) config('db.connection', 'mysql');
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row or null. */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch all rows. */
    public static function select(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** Fetch a single scalar column. */
    public static function scalar(string $sql, array $params = []): mixed
    {
        return self::run($sql, $params)->fetchColumn();
    }

    /** Run an insert and return the new id. */
    public static function insert(string $sql, array $params = []): int
    {
        self::run($sql, $params);
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Nesting-aware transactions: only the outermost begin/commit touches the
     * driver, so service methods that manage their own transaction can be
     * safely composed inside a larger one.
     */
    public static function beginTransaction(): void
    {
        if (self::$txLevel === 0) {
            self::connection()->beginTransaction();
        }
        self::$txLevel++;
    }

    public static function commit(): void
    {
        if (self::$txLevel > 0) {
            self::$txLevel--;
        }
        if (self::$txLevel === 0 && self::connection()->inTransaction()) {
            self::connection()->commit();
        }
    }

    public static function rollBack(): void
    {
        self::$txLevel = 0;
        if (self::connection()->inTransaction()) {
            self::connection()->rollBack();
        }
    }

    /** Reset connection (used by CLI/tests). */
    public static function reset(): void
    {
        self::$pdo = null;
    }
}
