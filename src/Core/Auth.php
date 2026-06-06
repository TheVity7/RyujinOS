<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Services\AuthMeService;

/**
 * Authentication manager.
 *
 * Credentials live in the single unified "Accounts" table, shared between the
 * game (AuthMe-style) and the website. Passwords are stored in an AuthMe
 * compatible hash (config auth.hash) so the same username + password works
 * both in-game and on the site.
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

    /**
     * Attempt to authenticate the given credentials against the unified
     * Accounts table. Returns the User on success or null on failure.
     */
    public static function attempt(string $username, string $password, string $ip): ?User
    {
        $user = User::findByUsername($username);
        if (!$user instanceof User || !$user->verifyPassword($password)) {
            return null;
        }
        self::touchLogin($user, $ip);
        return $user;
    }

    /**
     * Register a new account in the unified Accounts table. The password is
     * stored in an AuthMe-compatible hash so it also works in-game.
     * Returns [User|null, errorMessage].
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

        $user = User::create([
            'username'      => $username,
            'realname'      => $username,
            'email'         => $email !== '' ? $email : null,
            'password'      => AuthMeService::hash($password),
            'creationIP'    => $ip,
            'last_login_ip' => $ip,
        ]);

        self::touchLogin($user, $ip);
        return [$user, null];
    }

    private static function touchLogin(User $user, string $ip): void
    {
        $user->last_login_ip = $ip;
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->save();
    }
}
