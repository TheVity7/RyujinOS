<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * A movement on a user's credit balance.
 * type: purchase (credit bought) | spend (store purchase) | admin | bonus | refund
 */
final class CreditTransaction
{
    public static function record(int $userId, float $amount, string $type, string $description): int
    {
        return Database::insert(
            'INSERT INTO credit_transactions (user_id, amount, type, description, created_at)
             VALUES (?, ?, ?, ?, ?)',
            [$userId, $amount, $type, $description, date('Y-m-d H:i:s')]
        );
    }

    public static function forUser(int $userId, int $limit = 50): array
    {
        return Database::select(
            'SELECT * FROM credit_transactions WHERE user_id = ? ORDER BY id DESC LIMIT ' . max(1, $limit),
            [$userId]
        );
    }

    /** Recent credit loads across all users (for the homepage sidebar). */
    public static function recentLoads(int $limit = 5): array
    {
        return Database::select(
            "SELECT ct.amount, ct.created_at, u.username, u.avatar
             FROM credit_transactions ct
             JOIN users u ON u.id = ct.user_id
             WHERE ct.type IN ('purchase','admin','bonus') AND ct.amount > 0
             ORDER BY ct.id DESC LIMIT " . max(1, $limit)
        );
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'purchase' => 'Kredi Yükleme',
            'spend'    => 'Mağaza Harcaması',
            'admin'    => 'Yönetici İşlemi',
            'bonus'    => 'Bonus',
            'refund'   => 'İade',
            default    => ucfirst($type),
        };
    }
}
