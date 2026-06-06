<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Services\AuthMeService;

/**
 * Authentication manager.
 *
 * Supports three integration modes (config auth.integration):
 *   - native   : credentials stored in the website "users" table.
 *   - authme   : credentials stored in the AuthMe plugin table.
 *   - velocity : credentials stored in a Velocity auth plugin table.
 *
 * In the plugin modes the plugin table is the source of truth for the
 * password, while the "users" table mirrors each account so a player who
 * registered in-game automatically has a website profile.
 */
final class Auth
{
    private static ?User $user = null;

    public static function check(): bool
    {
        return self::user() instanceof User;
    }

    public static function user(): ?User
    {
        if (self::$user instanceof User) {
            return self::$user;
        }
        $id = Session::get('user_id');
        if ($id !== null) {
            $user = User::find((int) $id);
            if ($user instanceof User) {
                self::$user = $user;
                return $user;
            }
            Session::forget('user_id');
        }
        return null;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user?->id;
    }

    public static function login(User $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user->id);
        self::$user = $user;
    }

    public static function logout(): void
    {
        Session::forget('user_id');
        self::$user = null;
        Session::regenerate();
    }

    private static function integration(): string
    {
        return (string) config('auth.integration', 'native');
    }

    private static function usesPlugin(): bool
    {
        return in_array(self::integration(), ['authme', 'velocity'], true);
    }

    /**
     * Attempt to authenticate the given credentials.
     * Returns the User on success or null on failure.
     */
    public static function attempt(string $username, string $password, string $ip): ?User
    {
        if (self::usesPlugin()) {
            return self::attemptPlugin($username, $password, $ip);
        }
        return self::attemptNative($username, $password, $ip);
    }

    private static function attemptNative(string $username, string $password, string $ip): ?User
    {
        $user = User::findByUsername($username);
        if (!$user instanceof User || $user->password === null) {
            return null;
        }
        if (!password_verify($password, $user->password)) {
            return null;
        }
        self::touchLogin($user, $ip);
        return $user;
    }

    private static function attemptPlugin(string $username, string $password, string $ip): ?User
    {
        $table = (string) config('auth.table');
        $cols = config('auth.columns');
        $row = Database::selectOne(
            "SELECT * FROM {$table} WHERE LOWER({$cols['name']}) = LOWER(?)",
            [$username]
        );
        if ($row === null) {
            return null;
        }
        $hash = (string) ($row[$cols['password']] ?? '');
        if (!AuthMeService::verify($password, $hash)) {
            return null;
        }

        // Ensure a mirrored website account exists.
        $user = User::findByUsername($username);
        if (!$user instanceof User) {
            $user = User::create([
                'username' => (string) ($row[$cols['name']] ?? $username),
                'email'    => $row[$cols['email']] ?? null,
                'last_login_ip' => $ip,
            ]);
        }
        self::touchLogin($user, $ip);
        return $user;
    }

    /**
     * Register a new account. Returns [User|null, errorMessage].
     *
     * @return array{0: ?User, 1: ?string}
     */
    public static function register(string $username, string $email, string $password, string $ip): array
    {
        if (User::findByUsername($username) instanceof User) {
            return [null, 'Bu kullanıcı adı zaten kayıtlı.'];
        }
        if ($email !== '' && User::findByEmail($email) instanceof User) {
            return [null, 'Bu e-posta adresi zaten kayıtlı.'];
        }

        if (self::usesPlugin()) {
            self::createPluginAccount($username, $email, $password, $ip);
            $user = User::create([
                'username' => $username,
                'email'    => $email !== '' ? $email : null,
                'last_login_ip' => $ip,
            ]);
        } else {
            $user = User::create([
                'username' => $username,
                'email'    => $email !== '' ? $email : null,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'last_login_ip' => $ip,
            ]);
        }

        self::touchLogin($user, $ip);
        return [$user, null];
    }

    private static function createPluginAccount(string $username, string $email, string $password, string $ip): void
    {
        $table = (string) config('auth.table');
        $cols = config('auth.columns');
        $hash = AuthMeService::hash($password);

        // Build a portable insert covering the common AuthMe columns.
        $now = time();
        $columns = [
            $cols['name']      => strtolower($username),
            $cols['password']  => $hash,
            $cols['email']     => $email !== '' ? $email : 'your@email.com',
            $cols['ip']        => $ip,
            $cols['lastlogin'] => $now * 1000,
            $cols['regdate']   => $now * 1000,
        ];

        $names = array_keys($columns);
        $placeholders = implode(', ', array_fill(0, count($names), '?'));
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $names),
            $placeholders
        );

        try {
            Database::run($sql, array_values($columns));
        } catch (\Throwable $e) {
            // Some plugin schemas have extra NOT NULL columns; fall back to
            // the minimal name+password insert so registration still works.
            Database::run(
                sprintf('INSERT INTO %s (%s, %s) VALUES (?, ?)', $table, $cols['name'], $cols['password']),
                [strtolower($username), $hash]
            );
        }
    }

    private static function touchLogin(User $user, string $ip): void
    {
        $user->last_login_ip = $ip;
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->save();
    }
}
