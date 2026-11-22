<?php

declare(strict_types=1);

namespace App;

final class Dto
{
    /** @param array<string, mixed> $data */
    public static function arrayValues(array $data): array
    {
        return array_map(static fn (mixed $value): mixed => is_object($value) && method_exists($value, 'toArray') ? $value->toArray() : $value, $data);
    }
}
