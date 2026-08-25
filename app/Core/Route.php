<?php

declare(strict_types=1);

namespace App\Core;

class Route
{
    public string $method;
    public string $pattern;
    public string $regex;
    public $handler;
    public ?string $name;
    public array $middlewares = [];

    public function __construct(string $method, string $pattern, string $regex, $handler, ?string $name = null)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->regex = $regex;
        $this->handler = $handler;
        $this->name = $name;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        Router::registerName($name, $this);
        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }
}
