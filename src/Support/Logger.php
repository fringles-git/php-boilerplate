<?php

declare(strict_types=1);

namespace App\Support;

final class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $record = ['time' => gmdate(DATE_ATOM), 'level' => $level, 'message' => $message, 'context' => $context];
        error_log(json_encode($record, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) ?: $message);
    }
}
