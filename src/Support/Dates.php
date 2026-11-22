<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use DateTimeInterface;

final class Dates
{
    public static function format(DateTimeInterface|string $date, string $format = DATE_ATOM): string
    {
        return ($date instanceof DateTimeInterface ? DateTimeImmutable::createFromInterface($date) : new DateTimeImmutable($date))->format($format);
    }
}
