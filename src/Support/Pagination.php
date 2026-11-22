<?php

declare(strict_types=1);

namespace App\Support;

final class Pagination
{
    public static function metadata(int $total, int $offset, int $limit): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);
        return ['total' => max(0, $total), 'offset' => $offset, 'limit' => $limit, 'page' => intdiv($offset, $limit) + 1, 'pages' => (int) ceil(max(0, $total) / $limit), 'has_previous' => $offset > 0, 'has_next' => $offset + $limit < $total];
    }
}
