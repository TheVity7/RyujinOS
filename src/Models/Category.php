<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Store category (e.g. VIP Paketleri, Anahtarlar, Spawnerlar).
 */
final class Category
{
    public int $id = 0;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?string $icon = null;
    public int $sort_order = 0;

    public static function fromRow(array $row): self
    {
        $c = new self();
        $c->id = (int) $row['id'];
        $c->name = (string) $row['name'];
        $c->slug = (string) $row['slug'];
        $c->description = $row['description'] ?? null;
        $c->icon = $row['icon'] ?? null;
        $c->sort_order = (int) ($row['sort_order'] ?? 0);
        return $c;
    }

    /** @return list<self> */
    public static function all(): array
    {
        $rows = Database::select('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
        return array_map([self::class, 'fromRow'], $rows);
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM categories WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = Database::selectOne('SELECT * FROM categories WHERE slug = ?', [$slug]);
        return $row ? self::fromRow($row) : null;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO categories (name, slug, description, icon, sort_order) VALUES (?, ?, ?, ?, ?)',
            [$data['name'], $data['slug'], $data['description'] ?? null, $data['icon'] ?? null, $data['sort_order'] ?? 0]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?',
            [$data['name'], $data['slug'], $data['description'] ?? null, $data['icon'] ?? null, $data['sort_order'] ?? 0, $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM categories WHERE id = ?', [$id]);
    }
}
