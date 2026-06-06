<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Session;

/**
 * Read an environment variable with an optional default.
 */
function env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }
    $value = (string) $value;
    // Strip surrounding quotes.
    if (strlen($value) >= 2) {
        $first = $value[0];
        $last = $value[strlen($value) - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $value = substr($value, 1, -1);
        }
    }
    return $value;
}

/**
 * Read a boolean-ish environment variable.
 */
function env_bool(string $key, bool $default = false): bool
{
    $value = env($key);
    if ($value === null || $value === '') {
        return $default;
    }
    return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
}

/**
 * Fetch a config value using dot notation, e.g. config('app.name').
 */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/**
 * Base path helper.
 */
function base_path(string $path = ''): string
{
    return rtrim(BASE_PATH, '/\\') . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

/**
 * Build an absolute URL for the application.
 */
function url(string $path = ''): string
{
    return config('app.url') . '/' . ltrim($path, '/');
}

/**
 * Build a URL to a public asset.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Escape a string for safe HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Render a view and return the HTML string.
 */
function view(string $name, array $data = []): string
{
    return App\Core\View::render($name, $data);
}

/**
 * Issue an HTTP redirect and stop execution.
 */
function redirect(string $path): never
{
    $location = str_starts_with($path, 'http') ? $path : url($path);
    header('Location: ' . $location);
    exit;
}

/**
 * Old input / flash helpers.
 */
function old(string $key, string $default = ''): string
{
    $old = Session::get('_old', []);
    return isset($old[$key]) ? e((string) $old[$key]) : $default;
}

/**
 * Generate a CSRF hidden input field.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(App\Core\Csrf::token()) . '">';
}

/**
 * Format a credit / money amount with the configured currency.
 */
function money(float|int|string $amount, ?string $currency = null): string
{
    $currency = $currency ?? config('app.currency', 'TL');
    return number_format((float) $amount, 2, ',', '.') . ' ' . $currency;
}

/**
 * Format a credit amount (suffixed with "Kredi").
 */
function credits(float|int|string $amount): string
{
    return number_format((float) $amount, 2, ',', '.') . ' Kredi';
}

/**
 * Convert a string into a URL-friendly slug (Turkish characters aware).
 */
function slugify(string $text): string
{
    $map = ['ç'=>'c','ğ'=>'g','ı'=>'i','İ'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','Ç'=>'c','Ğ'=>'g','Ö'=>'o','Ş'=>'s','Ü'=>'u'];
    $text = strtr($text, $map);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'oge-' . substr((string) time(), -5);
}

/**
 * Render a Minecraft head avatar URL for a username.
 */
function mc_avatar(string $username, int $size = 64): string
{
    return 'https://mc-heads.net/avatar/' . urlencode($username) . '/' . $size;
}

/**
 * Human friendly "time ago" string (Turkish).
 */
function time_ago(string|int $datetime): string
{
    $ts = is_numeric($datetime) ? (int) $datetime : strtotime((string) $datetime);
    $diff = time() - $ts;
    if ($diff < 60) {
        return 'az önce';
    }
    $units = [
        31536000 => 'yıl',
        2592000  => 'ay',
        86400    => 'gün',
        3600     => 'saat',
        60       => 'dakika',
    ];
    foreach ($units as $secs => $label) {
        if ($diff >= $secs) {
            $count = (int) floor($diff / $secs);
            return $count . ' ' . $label . ' önce';
        }
    }
    return 'az önce';
}
