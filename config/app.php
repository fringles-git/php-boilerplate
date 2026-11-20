<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

/** Load a small .env file without external dependencies. Process env takes precedence. */
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile) && is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (preg_match('/\A[A-Z][A-Z0-9_]*\z/', $key) && getenv($key) === false) {
            if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function config(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function config_flag(string $key, bool $default = false): bool
{
    $value = strtolower(trim(config($key, $default ? 'true' : 'false')));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
}
