<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Route middleware: auth, guest and admin gates.
 */
final class Middleware
{
    public static function handle(string $name, Request $request): void
    {
        match ($name) {
            'auth'  => self::auth(),
            'guest' => self::guest(),
            'admin' => self::admin(),
            default => null,
        };
    }

    private static function auth(): void
    {
        if (!Auth::check()) {
            Flash::error('Bu sayfayı görüntülemek için giriş yapmalısınız.');
            redirect('giris');
        }
    }

    private static function guest(): void
    {
        if (Auth::check()) {
            redirect('profil');
        }
    }

    private static function admin(): void
    {
        $user = Auth::user();
        if (!$user instanceof User || !$user->isAdmin()) {
            Flash::error('Bu alana erişim yetkiniz yok.');
            redirect('/');
        }
    }
}
