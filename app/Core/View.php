<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        $file = BASE_PATH . "/app/Views/{$view}.php";

        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        $data = array_merge(self::$shared, $data);

        $user = Session::get('user') ?? null;
        if ($user !== null) {
            $data['user'] = $user;
        }

        $data['appName'] = (string) config('app.name', 'The New Age Marketplace');

        $content = self::renderFile($file, $data);

        if ($layout !== null) {
            $layoutFile = BASE_PATH . "/app/Views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                $data['content'] = $content;
                return self::renderFile($layoutFile, $data);
            }
        }

        return $content;
    }

    private static function renderFile(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
