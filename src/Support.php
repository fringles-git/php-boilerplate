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
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function verify_csrf(): void
{
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo render('error', ['errors' => ['Your form session expired. Please try again.'], 'authEnabled' => config_flag('AUTH_ENABLED', false), 'user' => null]);
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

function take_validation_errors(): array
{
    $errors = $_SESSION['_validation_errors'] ?? [];
    unset($_SESSION['_validation_errors']);
    return is_array($errors) ? $errors : [];
}

function take_old_input(): array
{
    $old = $_SESSION['_old_input'] ?? [];
    unset($_SESSION['_old_input']);
    return is_array($old) ? $old : [];
}

function url(string $path = '/'): string
{
    $base = rtrim(config('APP_URL', 'http://127.0.0.1:8000'), '/');
    return $base . '/' . ltrim($path, '/');
}

/** Redirect to a local application path only; absolute URLs and protocol-relative paths are rejected. */
function safe_redirect_path(string $target, string $fallback = '/'): string
{
    if ($target === '' || $target[0] !== '/' || str_starts_with($target, '//') || str_contains($target, "\\") || preg_match('/[\r\n]/', $target)) {
        return $fallback;
    }
    $parts = parse_url($target);
    return $parts === false || isset($parts['scheme']) || isset($parts['host']) ? $fallback : $target;
}

function redirect(string $path): never
{
    header('Location: ' . url(safe_redirect_path($path)), true, 303);
    exit;
}

function render(string $template, array $data = []): string
{
    if (!preg_match('/\A[a-zA-Z0-9_-]+\z/', $template)) {
        throw new RuntimeException('Invalid template name.');
    }
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
    ob_start();
    require dirname(__DIR__) . '/templates/layout.php';
    return (string) ob_get_clean();
}

function require_login(?\App\Models\User $user): void
{
    if ($user === null) {
        flash('Please log in to continue.', 'error');
        redirect('/login');
    }
}
