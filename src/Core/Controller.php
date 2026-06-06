<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller with view and redirect helpers.
 */
abstract class Controller
{
    protected function view(string $name, array $data = []): string
    {
        return View::render($name, $data);
    }

    protected function json(mixed $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        redirect($referer);
    }

    protected function notFound(): string
    {
        http_response_code(404);
        return View::render('errors/404', ['title' => 'Sayfa Bulunamadı']);
    }

    /**
     * Verify the CSRF token on a POST request; abort with 419 if invalid.
     */
    protected function verifyCsrf(Request $request): void
    {
        if ($request->isPost() && !Csrf::check((string) $request->input('_token'))) {
            http_response_code(419);
            Flash::error('Oturum süresi doldu, lütfen tekrar deneyin.');
            $this->back();
        }
    }
}
