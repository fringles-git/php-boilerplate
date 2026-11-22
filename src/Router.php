<?php

declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;

final class Router
{
    /** @var array<string, array<string, callable(Request): Response>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $path = '/' . trim($request->path, '/');
        if ($path === '//') {
            $path = '/';
        }
        $handler = $this->routes[$request->method][$path] ?? null;
        return $handler === null
            ? Response::html(render('404', ['authEnabled' => config_flag('AUTH_ENABLED', false), 'user' => null]), 404)
            : $handler($request);
    }
}
