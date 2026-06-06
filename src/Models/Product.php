<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * A purchasable store product. On purchase the configured RCON commands are
 * executed on the Minecraft server to deliver the item (VIP rank, spawner...).
 */
final class Product
{
    public int $id = 0;
    public int $category_id = 0;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?string $image = null;
    public float $price = 0.0;          // in credits
    public float $sale_price = 0.0;     // 0 = no discount
    public string $commands = '';       // newline separated, {player} placeholder
    public int $stock = -1;             // -1 = unlimited
    public bool $is_active = true;
    public bool $featured = false;
    public int $sort_order = 0;
    public string $created_at = '';

    public static function fromRow(array $row): self
    {
        $p = new self();
        $p->id = (int) $row['id'];
        $p->category_id = (int) $row['category_id'];
        $p->name = (string) $row['name'];
        $p->slug = (string) $row['slug'];
        $p->description = $row['description'] ?? null;
        $p->image = $row['image'] ?? null;
        $p->price = (float) $row['price'];
        $p->sale_price = (float) ($row['sale_price'] ?? 0);
        $p->commands = (string) ($row['commands'] ?? '');
        $p->stock = (int) ($row['stock'] ?? -1);
        $p->is_active = (bool) ($row['is_active'] ?? 1);
        $p->featured = (bool) ($row['featured'] ?? 0);
        $p->sort_order = (int) ($row['sort_order'] ?? 0);
        $p->created_at = (string) ($row['created_at'] ?? '');
        return $p;
    }

    public function effectivePrice(): float
    {
        return $this->sale_price > 0 ? $this->sale_price : $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    public function inStock(): bool
    {
        return $this->stock === -1 || $this->stock > 0;
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : asset('uploads/' . $this->image);
        }
        return asset('img/product-placeholder.svg');
    }

    /** @return list<self> */
    public static function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM products';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY sort_order ASC, name ASC';
        return array_map([self::class, 'fromRow'], Database::select($sql));
    }

    /** @return list<self> */
    public static function byCategory(int $categoryId): array
    {
        $rows = Database::select(
            'SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY sort_order ASC, name ASC',
            [$categoryId]
        );
        return array_map([self::class, 'fromRow'], $rows);
    }

    /** @return list<self> */
    public static function featured(int $limit = 6): array
    {
        $rows = Database::select(
            'SELECT * FROM products WHERE is_active = 1 AND featured = 1 ORDER BY sort_order ASC LIMIT ' . max(1, $limit)
        );
        return array_map([self::class, 'fromRow'], $rows);
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM products WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = Database::selectOne('SELECT * FROM products WHERE slug = ?', [$slug]);
        return $row ? self::fromRow($row) : null;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO products (category_id, name, slug, description, image, price, sale_price, commands, stock, is_active, featured, sort_order, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['category_id'], $data['name'], $data['slug'], $data['description'] ?? null,
                $data['image'] ?? null, $data['price'], $data['sale_price'] ?? 0, $data['commands'] ?? '',
                $data['stock'] ?? -1, $data['is_active'] ?? 1, $data['featured'] ?? 0, $data['sort_order'] ?? 0,
                date('Y-m-d H:i:s'),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, image = ?, price = ?,
             sale_price = ?, commands = ?, stock = ?, is_active = ?, featured = ?, sort_order = ? WHERE id = ?',
            [
                $data['category_id'], $data['name'], $data['slug'], $data['description'] ?? null,
                $data['image'] ?? null, $data['price'], $data['sale_price'] ?? 0, $data['commands'] ?? '',
                $data['stock'] ?? -1, $data['is_active'] ?? 1, $data['featured'] ?? 0, $data['sort_order'] ?? 0, $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM products WHERE id = ?', [$id]);
    }

    public function decrementStock(int $qty = 1): void
    {
        if ($this->stock !== -1) {
            $this->stock = max(0, $this->stock - $qty);
            Database::run('UPDATE products SET stock = ? WHERE id = ?', [$this->stock, $this->id]);
        }
    }
}
