<?php

declare(strict_types=1);

namespace App\Core;

/**
 * One-request flash messages (success / error / info) and old input.
 */
final class Flash
{
    public static function add(string $type, string $message): void
    {
        $messages = Session::get('_flash', []);
        $messages[$type][] = $message;
        Session::set('_flash', $messages);
    }

    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }

    /**
     * Return all flash messages and clear them.
     *
     * @return array<string, list<string>>
     */
    public static function pull(): array
    {
        $messages = Session::get('_flash', []);
        Session::forget('_flash');
        return $messages;
    }

    /**
     * Persist the current request input so a form can be re-populated.
     */
    public static function withInput(array $input): void
    {
        unset($input['password'], $input['password_confirmation'], $input['_token']);
        Session::set('_old', $input);
    }

    /**
     * Retrieve persisted old input.
     *
     * @return array<string,mixed>
     */
    public static function old(): array
    {
        return Session::get('_old', []);
    }

    public static function clearOld(): void
    {
        Session::forget('_old');
    }
}
