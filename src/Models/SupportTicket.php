<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Support ticket and its replies.
 * status: open | answered | closed
 */
final class SupportTicket
{
    public int $id = 0;
    public int $user_id = 0;
    public string $subject = '';
    public string $message = '';
    public string $status = 'open';
    public string $created_at = '';

    public static function fromRow(array $row): self
    {
        $t = new self();
        $t->id = (int) $row['id'];
        $t->user_id = (int) $row['user_id'];
        $t->subject = (string) $row['subject'];
        $t->message = (string) $row['message'];
        $t->status = (string) ($row['status'] ?? 'open');
        $t->created_at = (string) ($row['created_at'] ?? '');
        return $t;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO support_tickets (user_id, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?)',
            [$data['user_id'], $data['subject'], $data['message'], 'open', date('Y-m-d H:i:s')]
        );
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM support_tickets WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function forUser(int $userId): array
    {
        return array_map(
            [self::class, 'fromRow'],
            Database::select('SELECT * FROM support_tickets WHERE user_id = ? ORDER BY id DESC', [$userId])
        );
    }

    public static function all(): array
    {
        return Database::select(
            'SELECT st.*, u.username FROM support_tickets st JOIN users u ON u.id = st.user_id ORDER BY st.id DESC'
        );
    }

    public static function replies(int $ticketId): array
    {
        return Database::select(
            'SELECT sr.*, u.username FROM support_replies sr JOIN users u ON u.id = sr.user_id WHERE ticket_id = ? ORDER BY sr.id ASC',
            [$ticketId]
        );
    }

    public static function addReply(int $ticketId, int $userId, string $message, bool $isStaff): int
    {
        $id = Database::insert(
            'INSERT INTO support_replies (ticket_id, user_id, message, is_staff, created_at) VALUES (?, ?, ?, ?, ?)',
            [$ticketId, $userId, $message, $isStaff ? 1 : 0, date('Y-m-d H:i:s')]
        );
        Database::run('UPDATE support_tickets SET status = ? WHERE id = ?', [$isStaff ? 'answered' : 'open', $ticketId]);
        return $id;
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::run('UPDATE support_tickets SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'open'     => 'Açık',
            'answered' => 'Yanıtlandı',
            'closed'   => 'Kapalı',
            default    => ucfirst($status),
        };
    }
}
