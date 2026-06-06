<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Key/value site settings editable from the admin panel (site name, socials,
 * announcement bar, Discord invite, gaming-night date ...).
 */
final class Setting
{
    /** @var array<string,string>|null */
    private static ?array $cache = null;

    private static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::select('SELECT `key`, `value` FROM settings') as $row) {
                self::$cache[(string) $row['key']] = (string) $row['value'];
            }
        }
        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::load();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        $driver = Database::driver();
        if ($driver === 'sqlite') {
            Database::run(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                 ON CONFLICT(`key`) DO UPDATE SET `value` = excluded.value',
                [$key, $value]
            );
        } else {
            Database::run(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$key, $value]
            );
        }
        self::$cache = null;
    }

    /** @return array<string,string> */
    public static function all(): array
    {
        return self::load();
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
