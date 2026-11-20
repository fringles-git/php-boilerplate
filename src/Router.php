<?php

declare(strict_types=1);

namespace App;

final class Router
{
    private array $routes = [];

    public function __construct(private readonly string $method, private readonly string $path)
    {
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(): never
    {
        $normalized = '/' . trim($this->path, '/');
        if ($normalized === '//') {
            $normalized = '/';
        }
        $handler = $this->routes[$this->method][$normalized] ?? null;
        if ($handler !== null) {
            $handler();
            exit;
        }
        http_response_code(404);
        render('404', [
            'authEnabled' => config_flag('AUTH_ENABLED', false),
            'user' => null,
        ]);
        exit;
    }
}
