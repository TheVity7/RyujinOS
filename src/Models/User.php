<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\AuthMeService;

/**
 * Unified account. In-game (AuthMe-style) credentials and website data
 * (credit, role, profile) live in the same `Accounts` row, keyed by the
 * username, so a player who registers in-game automatically has a website
 * account and vice-versa.
 */
final class User
{
    public int $id = 0;
    public string $username = '';
    public string $realname = '';
    public ?string $uuid = null;
    public ?string $email = null;
    public ?string $password = null;
    public string $role = 'member';        // member | admin
    public float $balance = 0.0;           // credit column
    public ?string $avatar = null;
    public bool $two_factor = false;
    public bool $isVerified = true;
    public string $creationIP = '127.0.0.1';
    public ?string $last_login_ip = null;
    public ?string $last_login_at = null;
    public string $created_at = '';        // creationDate column
    public ?string $updated_at = null;

    public static function fromRow(array $row): self
    {
        $user = new self();
        $user->id = (int) ($row['id'] ?? 0);
        $user->username = (string) ($row['username'] ?? '');
        $user->realname = (string) ($row['realname'] ?? ($row['username'] ?? ''));
        $user->uuid = $row['uuid'] ?? null;
        $user->email = $row['email'] ?? null;
        $user->password = $row['password'] ?? null;
        $user->role = (string) ($row['role'] ?? 'member');
        $user->balance = (float) ($row['credit'] ?? $row['balance'] ?? 0);
        $user->avatar = $row['avatar'] ?? null;
        $user->two_factor = (bool) ($row['two_factor'] ?? false);
        $user->isVerified = (string) ($row['isVerified'] ?? '1') !== '0';
        $user->creationIP = (string) ($row['creationIP'] ?? '127.0.0.1');
        $user->last_login_ip = $row['last_login_ip'] ?? null;
        $user->last_login_at = $row['last_login_at'] ?? null;
        $user->created_at = (string) ($row['creationDate'] ?? $row['created_at'] ?? '');
        $user->updated_at = $row['updated_at'] ?? null;
        return $user;
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM Accounts WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findByUsername(string $username): ?self
    {
        $row = Database::selectOne('SELECT * FROM Accounts WHERE LOWER(username) = LOWER(?)', [$username]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $row = Database::selectOne('SELECT * FROM Accounts WHERE LOWER(email) = LOWER(?)', [$email]);
        return $row ? self::fromRow($row) : null;
    }

    public static function create(array $data): self
    {
        $username = (string) $data['username'];
        $ip = $data['creationIP'] ?? $data['last_login_ip'] ?? '127.0.0.1';
        $id = Database::insert(
            'INSERT INTO Accounts (username, realname, uuid, email, password, credit, role, avatar, isVerified, creationIP, last_login_ip, creationDate)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $username,
                $data['realname'] ?? $username,
                $data['uuid'] ?? null,
                $data['email'] ?? null,
                $data['password'] ?? null,
                $data['balance'] ?? $data['credit'] ?? 0,
                $data['role'] ?? 'member',
                $data['avatar'] ?? null,
                isset($data['isVerified']) && !$data['isVerified'] ? '0' : '1',
                $ip,
                $data['last_login_ip'] ?? null,
                date('Y-m-d H:i:s'),
            ]
        );
        return self::find($id) ?? throw new \RuntimeException('Kullanıcı oluşturulamadı.');
    }

    public function save(): void
    {
        Database::run(
            'UPDATE Accounts SET email = ?, password = ?, role = ?, credit = ?, avatar = ?,
             two_factor = ?, isVerified = ?, last_login_ip = ?, last_login_at = ?, updated_at = ? WHERE id = ?',
            [
                $this->email,
                $this->password,
                $this->role,
                $this->balance,
                $this->avatar,
                $this->two_factor ? 1 : 0,
                $this->isVerified ? '1' : '0',
                $this->last_login_ip,
                $this->last_login_at,
                date('Y-m-d H:i:s'),
                $this->id,
            ]
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'Yönetici' : 'Oyuncu';
    }

    public function avatarUrl(int $size = 64): string
    {
        return $this->avatar ?: mc_avatar($this->username, $size);
    }

    /**
     * Adjust balance and write a credit transaction record.
     */
    public function addBalance(float $amount, string $type, string $description): void
    {
        Database::beginTransaction();
        try {
            $this->balance = round($this->balance + $amount, 2);
            Database::run('UPDATE Accounts SET credit = ?, updated_at = ? WHERE id = ?', [
                $this->balance,
                date('Y-m-d H:i:s'),
                $this->id,
            ]);
            CreditTransaction::record($this->id, $amount, $type, $description);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public static function count(): int
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM Accounts');
    }

    /**
     * Verify a plain password against the stored credential. Passwords live in
     * the unified Accounts table in an AuthMe-compatible format so the same
     * credential works both in-game and on the website.
     */
    public function verifyPassword(string $password): bool
    {
        return $this->password !== null
            && $this->password !== ''
            && AuthMeService::verify($password, $this->password);
    }

    /**
     * Change the password, storing it in the configured AuthMe hash format.
     */
    public function changePassword(string $password): void
    {
        $this->password = AuthMeService::hash($password);
        $this->save();
    }

    /** @return list<array{id:int,username:string,email:?string,role:string,balance:float,avatar:?string,created_at:string}> */
    public static function all(int $limit = 200, string $search = ''): array
    {
        $select = 'SELECT *, credit AS balance, creationDate AS created_at FROM Accounts';
        if ($search !== '') {
            return Database::select(
                $select . ' WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ' . max(1, $limit),
                ['%' . $search . '%', '%' . $search . '%']
            );
        }
        return Database::select($select . ' ORDER BY id DESC LIMIT ' . max(1, $limit));
    }

    /** Top credit loaders (by total positive transactions) within a period. */
    public static function topCreditLoaders(int $limit = 5, ?string $since = null): array
    {
        $params = [];
        $where = "ct.type IN ('purchase','admin','bonus') AND ct.amount > 0";
        if ($since !== null) {
            $where .= ' AND ct.created_at >= ?';
            $params[] = $since;
        }
        $sql = "SELECT u.id, u.username, u.avatar, SUM(ct.amount) AS total
                FROM credit_transactions ct
                JOIN Accounts u ON u.id = ct.user_id
                WHERE {$where}
                GROUP BY u.id, u.username, u.avatar
                ORDER BY total DESC";
        $sql = self::limit($sql, $limit);
        return Database::select($sql, $params);
    }

    private static function limit(string $sql, int $limit): string
    {
        return $sql . ' LIMIT ' . max(1, $limit);
    }
}
