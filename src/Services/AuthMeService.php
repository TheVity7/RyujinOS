<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Password hashing / verification compatible with the most common Minecraft
 * authentication plugins (AuthMe, Velocity LimboAuth, nLogin ...).
 *
 * The algorithm is selected through the AUTHME_HASH config value so the same
 * credentials work both in-game and on the website.
 */
final class AuthMeService
{
    public static function algorithm(): string
    {
        return strtoupper((string) config('auth.hash', 'SHA256'));
    }

    /**
     * Hash a plaintext password using the configured algorithm.
     */
    public static function hash(string $password): string
    {
        return match (self::algorithm()) {
            'BCRYPT'    => password_hash($password, PASSWORD_BCRYPT),
            'ARGON2'    => password_hash($password, PASSWORD_ARGON2ID),
            'SHA512'    => hash('sha512', $password),
            'PLAINTEXT' => $password,
            default     => self::hashAuthMeSha256($password),
        };
    }

    /**
     * Verify a plaintext password against a stored hash.
     */
    public static function verify(string $password, string $hash): bool
    {
        if ($hash === '') {
            return false;
        }

        // AuthMe SHA256 format: $SHA$<salt>$<sha256(sha256(pass).salt)>
        if (str_starts_with($hash, '$SHA$')) {
            return self::verifyAuthMeSha256($password, $hash);
        }

        // BCrypt (LimboAuth / AuthMe BCRYPT).
        if (preg_match('/^\$2[aby]\$/', $hash) === 1) {
            return password_verify($password, $hash);
        }

        // Argon2.
        if (str_starts_with($hash, '$argon2')) {
            return password_verify($password, $hash);
        }

        return match (self::algorithm()) {
            'SHA512'    => hash_equals($hash, hash('sha512', $password)),
            'PLAINTEXT' => hash_equals($hash, $password),
            default     => hash_equals(strtolower($hash), strtolower(hash('sha256', $password))),
        };
    }

    private static function hashAuthMeSha256(string $password): string
    {
        $salt = substr(bin2hex(random_bytes(8)), 0, 16);
        $hash = hash('sha256', hash('sha256', $password) . $salt);
        return '$SHA$' . $salt . '$' . $hash;
    }

    private static function verifyAuthMeSha256(string $password, string $stored): bool
    {
        $parts = explode('$', $stored); // ['', 'SHA', salt, hash]
        if (count($parts) !== 4) {
            return false;
        }
        $salt = $parts[2];
        $expected = $parts[3];
        $computed = hash('sha256', hash('sha256', $password) . $salt);
        return hash_equals($expected, $computed);
    }
}
