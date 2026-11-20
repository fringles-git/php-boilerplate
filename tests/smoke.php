<?php

declare(strict_types=1);

namespace {
    require dirname(__DIR__) . '/config/app.php';
    require dirname(__DIR__) . '/src/Support.php';

    $checks = [
        'PHP runtime is 8.5.12 or newer' => PHP_VERSION_ID >= 80512,
        'PostgreSQL PDO extension is installed' => extension_loaded('pdo_pgsql'),
        'Supabase schema is present' => is_file(dirname(__DIR__) . '/database/schema.sql'),
        'Public document root is present' => is_file(dirname(__DIR__) . '/public/index.php'),
        'Tailwind configuration is present' => is_file(dirname(__DIR__) . '/tailwind.config.js'),
        'Output escaping works' => esc('<script>') === '&lt;script&gt;',
    ];
    $failed = false;
    foreach ($checks as $name => $passed) {
        echo ($passed ? '[PASS] ' : '[FAIL] ') . $name . PHP_EOL;
        $failed = $failed || !$passed;
    }
    exit($failed ? 1 : 0);
}
