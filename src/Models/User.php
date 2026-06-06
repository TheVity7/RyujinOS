<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Website user account. When AuthMe/Velocity integration is enabled the
 * credentials live in the plugin table, while this row stores website data
 * (credits, role, profile) keyed by the in-game username.
 */
final class User
{
    public int $id = 0;
    public string $username = '';
    public ?string $uuid = null;
    public ?string $email = null;
    public ?string $password = null;
    public string $role = 'member';        // member | admin
    public float $balance = 0.0;           // credits
    public ?string $avatar = null;
    public bool $two_factor = false;
    public ?string $last_login_ip = null;
    public ?string $last_login_at = null;
    public string $created_at = '';
    public ?string $updated_at = null;

    public static function fromRow(array $row): self
    {
        $user = new self();
        $user->id = (int) ($row['id'] ?? 0);
        $user->username = (string) ($row['username'] ?? '');
        $user->uuid = $row['uuid'] ?? null;
        $user->email = $row['email'] ?? null;
        $user->password = $row['password'] ?? null;
        $user->role = (string) ($row['role'] ?? 'member');
        $user->balance = (float) ($row['balance'] ?? 0);
        $user->avatar = $row['avatar'] ?? null;
        $user->two_factor = (bool) ($row['two_factor'] ?? false);
        $user->last_login_ip = $row['last_login_ip'] ?? null;
        $user->last_login_at = $row['last_login_at'] ?? null;
        $user->created_at = (string) ($row['created_at'] ?? '');
        $user->updated_at = $row['updated_at'] ?? null;
        return $user;
    }

    public static function find(int $id): ?self
    {
        $row = Database::selectOne('SELECT * FROM users WHERE id = ?', [$id]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findByUsername(string $username): ?self
    {
        $row = Database::selectOne('SELECT * FROM users WHERE LOWER(username) = LOWER(?)', [$username]);
        return $row ? self::fromRow($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $row = Database::selectOne('SELECT * FROM users WHERE LOWER(email) = LOWER(?)', [$email]);
        return $row ? self::fromRow($row) : null;
    }

    public static function create(array $data): self
    {
        $id = Database::insert(
            'INSERT INTO users (username, uuid, email, password, role, balance, avatar, last_login_ip, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['username'],
                $data['uuid'] ?? null,
                $data['email'] ?? null,
                $data['password'] ?? null,
                $data['role'] ?? 'member',
                $data['balance'] ?? 0,
                $data['avatar'] ?? null,
                $data['last_login_ip'] ?? null,
                date('Y-m-d H:i:s'),
            ]
        );
        return self::find($id) ?? throw new \RuntimeException('Kullanıcı oluşturulamadı.');
    }

    public function save(): void
    {
        Database::run(
            'UPDATE users SET email = ?, password = ?, role = ?, balance = ?, avatar = ?,
             two_factor = ?, last_login_ip = ?, last_login_at = ?, updated_at = ? WHERE id = ?',
            [
                $this->email,
                $this->password,
                $this->role,
                $this->balance,
                $this->avatar,
                $this->two_factor ? 1 : 0,
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
            Database::run('UPDATE users SET balance = ?, updated_at = ? WHERE id = ?', [
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
        return (int) Database::scalar('SELECT COUNT(*) FROM users');
    }

    /**
     * Verify a plain password against the stored credential. Reads from the
     * AuthMe/Velocity plugin table when integration is enabled, otherwise the
     * native users.password column.
     */
    public function verifyPassword(string $password): bool
    {
        $integration = (string) config('auth.integration', 'native');
        if (in_array($integration, ['authme', 'velocity'], true)) {
            $table = (string) config('auth.table');
            $cols = config('auth.columns');
            $hash = (string) Database::scalar(
                "SELECT {$cols['password']} FROM {$table} WHERE LOWER({$cols['name']}) = LOWER(?)",
                [$this->username]
            );
            return $hash !== '' && \App\Services\AuthMeService::verify($password, $hash);
        }
        return $this->password !== null && password_verify($password, $this->password);
    }

    /**
     * Change the password, writing to whichever store backs authentication.
     */
    public function changePassword(string $password): void
    {
        $integration = (string) config('auth.integration', 'native');
        if (in_array($integration, ['authme', 'velocity'], true)) {
            $table = (string) config('auth.table');
            $cols = config('auth.columns');
            Database::run(
                "UPDATE {$table} SET {$cols['password']} = ? WHERE LOWER({$cols['name']}) = LOWER(?)",
                [\App\Services\AuthMeService::hash($password), $this->username]
            );
            return;
        }
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->save();
    }

    /** @return list<array{id:int,username:string,email:?string,role:string,balance:float,avatar:?string,created_at:string}> */
    public static function all(int $limit = 200, string $search = ''): array
    {
        if ($search !== '') {
            return Database::select(
                'SELECT * FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ' . max(1, $limit),
                ['%' . $search . '%', '%' . $search . '%']
            );
        }
        return Database::select('SELECT * FROM users ORDER BY id DESC LIMIT ' . max(1, $limit));
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
                JOIN users u ON u.id = ct.user_id
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
