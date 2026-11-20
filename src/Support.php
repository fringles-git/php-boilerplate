<?php

declare(strict_types=1);

function esc(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(mixed $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function verify_csrf(): void
{
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        render('error', [
            'errors' => ['Your form session expired. Please try again.'],
            'authEnabled' => config_flag('AUTH_ENABLED', false),
            'user' => null,
        ]);
        exit;
    }
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['text' => $message, 'type' => $type];
}

function flash_all(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function url(string $path = '/'): string
{
    $base = rtrim(config('APP_URL', 'http://127.0.0.1:8000'), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit;
}

function render(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $appName = config('APP_NAME', 'PHP Boilerplate');
    $title = $title ?? $appName;
    $templatePath = dirname(__DIR__) . '/templates/' . $template . '.php';
    if (!is_file($templatePath)) {
        throw new RuntimeException('Template not found.');
    }
    $view = $templatePath;
    $authEnabled = $authEnabled ?? config_flag('AUTH_ENABLED', false);
    $user = $user ?? null;
    require dirname(__DIR__) . '/templates/layout.php';
}

function require_login(?array $user): void
{
    if ($user === null) {
        flash('Please log in to continue.', 'error');
        redirect('/login');
    }
}
