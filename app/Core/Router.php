<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $named = [];

    public static function get(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('GET', $pattern, $handler, $name);
    }

    public static function post(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('POST', $pattern, $handler, $name);
    }

    public static function put(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('PUT', $pattern, $handler, $name);
    }

    public static function patch(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('PATCH', $pattern, $handler, $name);
    }

    public static function delete(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('DELETE', $pattern, $handler, $name);
    }

    public static function any(string $pattern, $handler, ?string $name = null): Route
    {
        return self::add('ANY', $pattern, $handler, $name);
    }

    private static function add(string $method, string $pattern, $handler, ?string $name = null): Route
    {
        $pattern = '/' . ltrim($pattern, '/');
        $regex = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        $route = new Route($method, $pattern, $regex, $handler, $name);
        self::$routes[] = $route;

        if ($name !== null) {
            self::$named[$name] = $route;
        }

        return $route;
    }

    public static function registerName(string $name, Route $route): void
    {
        self::$named[$name] = $route;
    }

    public static function dispatch(): string
    {
        $uri = Request::uri();
        $method = Request::method();

        foreach (self::$routes as $route) {
            if ($route->method !== 'ANY' && $route->method !== $method) {
                continue;
            }

            if (!preg_match($route->regex, $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            foreach ($route->middlewares as $middleware) {
                self::runMiddleware($middleware);
            }

            return self::callHandler($route->handler, $params);
        }

        throw new HttpException('Page not found.', 404);
    }

    private static function runMiddleware(string $middleware): void
    {
        $class = 'App\\Middleware\\' . ucfirst($middleware);

        if (!class_exists($class)) {
            throw new \RuntimeException("Middleware not found: {$middleware}");
        }

        $instance = new $class();
        $instance->handle();
    }

    private static function callHandler($handler, array $params): string
    {
        if (is_array($handler) && count($handler) === 2) {
            $controller = new $handler[0]();
            $method = $handler[1];
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Controller method not found: {$handler[0]}::{$method}");
            }
            return (string) $controller->{$method}(...$params);
        }

        if (is_callable($handler)) {
            return (string) $handler(...$params);
        }

        throw new \RuntimeException('Invalid route handler.');
    }

    public static function route(string $name, array $params = []): string
    {
        if (!isset(self::$named[$name])) {
            throw new \RuntimeException("Route not found: {$name}");
        }

        $route = self::$named[$name];
        $url = $route->pattern;

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', (string) $value, $url);
        }

        return url($url);
    }
}
