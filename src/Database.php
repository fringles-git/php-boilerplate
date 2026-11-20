<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'] as $required) {
            if (config($required) === '') {
                throw new PDOException("Missing required environment value: {$required}");
            }
        }
        if (!extension_loaded('pdo_pgsql')) {
            throw new PDOException('The PDO PostgreSQL extension (pdo_pgsql) is required.');
        }

        $host = config('DB_HOST');
        $port = config('DB_PORT', '5432');
        $database = config('DB_NAME', 'postgres');
        $sslmode = config('DB_SSLMODE', 'require');
        if (!preg_match('/\A[a-zA-Z0-9.-]+\z/', $host) || !ctype_digit($port)) {
            throw new PDOException('Invalid PostgreSQL connection host or port.');
        }
        if (!in_array($sslmode, ['require', 'verify-ca', 'verify-full'], true)) {
            throw new PDOException('DB_SSLMODE must be require, verify-ca, or verify-full.');
        }

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=%s', $host, $port, $database, $sslmode);
        self::$pdo = new PDO($dsn, config('DB_USER'), config('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return self::$pdo;
    }
}
