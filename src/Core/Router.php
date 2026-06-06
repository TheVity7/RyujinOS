<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal regex based router with route parameters and middleware.
 */
final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:array, middleware:list<string>}> */
    private array $routes = [];

    /** @var list<string> */
    private array $groupMiddleware = [];

    public function get(string $pattern, array $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    /**
     * Group routes under shared middleware.
     */
    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    private function add(string $method, string $pattern, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $pattern,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    /**
     * Dispatch the request, returning the response body.
     */
    public function dispatch(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $regex = $this->compile($route['pattern']);
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $middleware) {
                    Middleware::handle($middleware, $request);
                }

                [$class, $action] = $route['handler'];
                $controller = new $class();
                $result = $controller->$action($request, ...array_values($params));
                return is_string($result) ? $result : '';
            }
        }

        http_response_code(404);
        return View::render('errors/404', ['title' => 'Sayfa bulunamadı']);
    }

    private function compile(string $pattern): string
    {
        $regex = preg_replace_callback('/\{([a-zA-Z_]+)\}/', static function (array $m): string {
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $pattern);
        return '#^' . $regex . '$#';
    }
}
