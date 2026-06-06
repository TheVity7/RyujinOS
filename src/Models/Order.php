<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * A completed (or pending) store purchase.
 * status: pending | completed | delivered | failed | refunded
 */
final class Order
{
    public int $id = 0;
    public int $user_id = 0;
    public int $product_id = 0;
    public string $product_name = '';
    public int $quantity = 1;
    public float $total = 0.0;
    public string $status = 'pending';
    public string $delivery = 'pending';   // pending | delivered | failed
    public ?string $created_at = null;

    public static function fromRow(array $row): self
    {
        $o = new self();
        $o->id = (int) $row['id'];
        $o->user_id = (int) $row['user_id'];
        $o->product_id = (int) ($row['product_id'] ?? 0);
        $o->product_name = (string) ($row['product_name'] ?? '');
        $o->quantity = (int) ($row['quantity'] ?? 1);
        $o->total = (float) ($row['total'] ?? 0);
        $o->status = (string) ($row['status'] ?? 'pending');
        $o->delivery = (string) ($row['delivery'] ?? 'pending');
        $o->created_at = $row['created_at'] ?? null;
        return $o;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO orders (user_id, product_id, product_name, quantity, total, status, delivery, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'], $data['product_id'], $data['product_name'], $data['quantity'] ?? 1,
                $data['total'], $data['status'] ?? 'completed', $data['delivery'] ?? 'pending',
                date('Y-m-d H:i:s'),
            ]
        );
    }

    public static function setDelivery(int $id, string $delivery): void
    {
        Database::run('UPDATE orders SET delivery = ? WHERE id = ?', [$delivery, $id]);
    }

    public static function forUser(int $userId, int $limit = 50): array
    {
        return Database::select(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT ' . max(1, $limit),
            [$userId]
        );
    }

    public static function recent(int $limit = 5): array
    {
        return Database::select(
            "SELECT o.product_name, o.created_at, u.username, u.avatar, c.name AS category
             FROM orders o
             JOIN users u ON u.id = o.user_id
             LEFT JOIN products p ON p.id = o.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE o.status IN ('completed','delivered')
             ORDER BY o.id DESC LIMIT " . max(1, $limit)
        );
    }

    public static function count(): int
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM orders');
    }

    public static function revenue(): float
    {
        return (float) Database::scalar("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('completed','delivered')");
    }

    public static function all(int $limit = 100): array
    {
        return Database::select(
            "SELECT o.*, u.username FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.id DESC LIMIT " . max(1, $limit)
        );
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'   => 'Beklemede',
            'completed' => 'Tamamlandı',
            'delivered' => 'Teslim Edildi',
            'failed'    => 'Başarısız',
            'refunded'  => 'İade Edildi',
            default     => ucfirst($status),
        };
    }
}
