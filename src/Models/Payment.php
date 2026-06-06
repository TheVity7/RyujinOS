<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * A credit top-up payment processed through a provider (Shopier).
 * status: pending | paid | failed
 */
final class Payment
{
    public int $id = 0;
    public int $user_id = 0;
    public string $order_ref = '';
    public float $amount = 0.0;   // money charged (TL)
    public float $credits = 0.0;  // credits granted on success
    public string $provider = 'shopier';
    public string $status = 'pending';
    public ?string $payment_id = null;
    public string $created_at = '';

    public static function fromRow(array $row): self
    {
        $p = new self();
        $p->id = (int) $row['id'];
        $p->user_id = (int) $row['user_id'];
        $p->order_ref = (string) $row['order_ref'];
        $p->amount = (float) $row['amount'];
        $p->credits = (float) $row['credits'];
        $p->provider = (string) ($row['provider'] ?? 'shopier');
        $p->status = (string) ($row['status'] ?? 'pending');
        $p->payment_id = $row['payment_id'] ?? null;
        $p->created_at = (string) ($row['created_at'] ?? '');
        return $p;
    }

    public static function create(array $data): self
    {
        $id = Database::insert(
            'INSERT INTO payments (user_id, order_ref, amount, credits, provider, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'], $data['order_ref'], $data['amount'], $data['credits'],
                $data['provider'] ?? 'shopier', $data['status'] ?? 'pending', date('Y-m-d H:i:s'),
            ]
        );
        return self::find($id) ?? throw new \RuntimeException('Ödeme kaydı oluşturulamadı.');
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM payments WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findByRef(string $ref): ?self
    {
        $row = Database::selectOne('SELECT * FROM payments WHERE order_ref = ?', [$ref]);
        return $row ? self::fromRow($row) : null;
    }

    public function markPaid(?string $paymentId = null): void
    {
        $this->status = 'paid';
        $this->payment_id = $paymentId;
        Database::run('UPDATE payments SET status = ?, payment_id = ? WHERE id = ?', ['paid', $paymentId, $this->id]);
    }

    public function markFailed(): void
    {
        $this->status = 'failed';
        Database::run('UPDATE payments SET status = ? WHERE id = ?', ['failed', $this->id]);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
