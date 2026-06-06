<?php

declare(strict_types=1);

/**
 * Application bootstrap: environment, autoloading, config and error handling.
 * Included by public/index.php and cli.php.
 */

define('BASE_PATH', __DIR__);

// ---------------------------------------------------------------------------
// 1. Load the .env file (very small dotenv parser, no dependencies).
// ---------------------------------------------------------------------------
$envFile = BASE_PATH . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        // Strip inline comments that are not inside quotes.
        if ($value !== '' && $value[0] !== '"' && $value[0] !== "'") {
            $hash = strpos($value, ' #');
            if ($hash !== false) {
                $value = rtrim(substr($value, 0, $hash));
            }
        }
        if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
            $value = substr($value, 1, -1);
        }
        if (getenv($name) === false) {
            putenv("$name=$value");
        }
        $_ENV[$name] = $value;
    }
}

// ---------------------------------------------------------------------------
// 2. PSR-4 style autoloader for the App\ namespace mapped to src/.
// ---------------------------------------------------------------------------
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

// ---------------------------------------------------------------------------
// 3. Helpers + configuration.
// ---------------------------------------------------------------------------
require BASE_PATH . '/src/Core/helpers.php';

App\Core\Config::load(require BASE_PATH . '/config/config.php');

date_default_timezone_set((string) config('app.timezone', 'UTC'));

// ---------------------------------------------------------------------------
// 4. Error handling.
// ---------------------------------------------------------------------------
$debug = (bool) config('app.debug', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');

return true;
