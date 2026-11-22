<?php

declare(strict_types=1);

namespace App;

final class Hash
{
    public static function make(string $value): string
    {
        return hash('sha256', $value);
    }

    public static function random(int $bytes = 32): string
    {
        if ($bytes < 16) {
            throw new \InvalidArgumentException('Random values must use at least 16 bytes.');
        }
        return bin2hex(random_bytes($bytes));
    }

    public static function verify(string $value, string $hash): bool
    {
        return hash_equals(self::make($value), $hash);
    }
}
