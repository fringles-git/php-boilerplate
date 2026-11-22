<?php

declare(strict_types=1);

use App\Support\Logger;

if (PHP_VERSION_ID < 80512) {
    http_response_code(500);
    exit('PHP 8.5.12 or newer is required.');
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $path = dirname(__DIR__) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});
require_once dirname(__DIR__) . '/src/Support.php';

ini_set('display_errors', config('APP_ENV', 'production') === 'production' ? '0' : '1');
error_reporting(E_ALL);

foreach ([
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'SAMEORIGIN',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
    'Content-Security-Policy' => "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; object-src 'none'; base-uri 'self'; frame-ancestors 'self'",
]) {
    header($ _ = $name . ': ' . $value);
}

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (config_flag('TRUST_PROXY_HTTPS', false) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('php_boilerplate_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

set_exception_handler(static function (Throwable $exception): void {
    Logger::error('Unhandled application exception', [
        'exception' => $exception::class,
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
    ]);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo render('error', ['errors' => ['An unexpected error occurred.'], 'authEnabled' => config_flag('AUTH_ENABLED', false), 'user' => null]);
});
