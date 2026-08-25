<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private static int $statusCode = 200;

    public static function status(int $code): void
    {
        self::$statusCode = $code;
        http_response_code($code);
    }

    public static function header(string $name, string $value): void
    {
        header("{$name}: {$value}");
    }

    public static function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }

    public static function redirect(string $to, int $code = 302): never
    {
        $url = str_starts_with($to, 'http') ? $to : url($to);
        http_response_code($code);
        header("Location: {$url}");
        exit;
    }

    public static function json(array $data, int $code = 200): never
    {
        self::status($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function error(int $code, string $message = ''): string
    {
        self::status($code);

        if (empty($message)) {
            $message = match ($code) {
                403 => 'You do not have permission to access this page.',
                404 => 'The page you are looking for could not be found.',
                419 => 'Your session has expired. Please try again.',
                default => 'Something went wrong. Please try again.',
            };
        }

        $viewFile = BASE_PATH . "/app/Views/errors/{$code}.php";
        $view = file_exists($viewFile) ? "errors/{$code}" : 'errors/500';

        return View::render($view, ['code' => $code, 'message' => $message]);
    }
}
