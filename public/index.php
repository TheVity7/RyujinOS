<?php

declare(strict_types=1);

use App\Core\Flash;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

require dirname(__DIR__) . '/bootstrap.php';

Session::start();

$request = new Request();
$router = new Router();
(require base_path('src/routes.php'))($router);

try {
    $output = $router->dispatch($request);
} catch (Throwable $e) {
    error_log((string) $e);
    if (config('app.debug')) {
        http_response_code(500);
        echo '<pre style="padding:20px;font-family:monospace">';
        echo e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
        echo '</pre>';
        exit;
    }
    http_response_code(500);
    $output = View::render('errors/500', ['title' => 'Sunucu hatası']);
}

// Clear one-shot old input once the response has been built.
Flash::clearOld();

echo $output;
