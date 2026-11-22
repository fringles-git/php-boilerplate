<?php

declare(strict_types=1);

namespace App;

final class Input
{
    public static function exists(string|array $source = 'post'): bool
    {
        $source = is_array($source) ? $source : match (strtolower($source)) {
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            default => $_REQUEST,
        };
        return is_array($source) && $source !== [];
    }

    public static function get(string $key, mixed $default = null, string|array $source = 'post'): mixed
    {
        $values = is_array($source) ? $source : match (strtolower($source)) {
            'get' => $_GET,
            'post' => $_POST,
            'cookie' => $_COOKIE,
            default => $_REQUEST,
        };
        return $values[$key] ?? $default;
    }

    public static function string(string $key, string $default = '', string|array $source = 'post'): string
    {
        $value = self::get($key, $default, $source);
        return is_string($value) || is_numeric($value) ? trim((string) $value) : $default;
    }

    public static function int(string $key, ?int $default = null, string|array $source = 'post'): ?int
    {
        $value = filter_var(self::get($key, null, $source), FILTER_VALIDATE_INT);
        return $value === false ? $default : $value;
    }
}
