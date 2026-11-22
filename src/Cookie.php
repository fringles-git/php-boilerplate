<?php

declare(strict_types=1);

namespace App;

final class Cookie
{
    /** @param array<string, bool|string|int> $options */
    public static function put(string $name, string $value, int $expires = 0, array $options = []): bool
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (config_flag('TRUST_PROXY_HTTPS', false) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $params = [
            'expires' => $expires,
            'path' => (string) ($options['path'] ?? '/'),
            'domain' => (string) ($options['domain'] ?? ''),
            'secure' => (bool) ($options['secure'] ?? $https),
            'httponly' => (bool) ($options['httponly'] ?? true),
            'samesite' => (string) ($options['samesite'] ?? 'Lax'),
        ];
        $result = setcookie($name, $value, $params);
        if ($result) {
            $_COOKIE[$name] = $value;
        }
        return $result;
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        return $_COOKIE[$name] ?? $default;
    }

    public static function exists(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }

    public static function delete(string $name, array $options = []): bool
    {
        if (!self::exists($name)) {
            return true;
        }
        $result = self::put($name, '', time() - 42000, $options);
        unset($_COOKIE[$name]);
        return $result;
    }
}
