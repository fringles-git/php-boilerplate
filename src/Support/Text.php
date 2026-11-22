<?php

declare(strict_types=1);

namespace App\Support;

final class Text
{
    public static function length(string $value): int
    {
        return mb_strlen($value, 'UTF-8');
    }

    public static function slug(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $ascii === false ? $value : $ascii) ?? '', '-'));
        return $slug;
    }
}
