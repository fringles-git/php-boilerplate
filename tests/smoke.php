<?php

use App\Http\Response;
use App\Http\Request;

$root = dirname(__DIR__);
$failed = 0;
$check = static function (string $label, bool $passed) use (&$failed): void {
    echo ($passed ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$passed) $failed++;
};

$phpFiles = [];
foreach ([$root . '/config', $root . '/src', $root . '/public', $root . '/templates', $root . '/tests', $root . '/bin'] as $directory) {
    if (!is_dir($directory)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) if ($file->isFile() && $file->getExtension() === 'php') $phpFiles[] = $file->getPathname();
}
foreach ($phpFiles as $phpFile) {
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($phpFile), $output, $status);
    $check('PHP syntax: ' . substr($phpFile, strlen($root) + 1), $status === 0);
}
foreach (['src/bootstrap.php', 'src/Http/Request.php', 'src/Http/Response.php', 'src/Validator.php', 'src/Models/User.php', 'src/Support/Logger.php', 'src/Support/Pagination.php', 'bin/console', 'public/index.php'] as $path) {
    $check('File exists: ' . $path, is_file($root . '/' . $path));
}
require $root . '/config/app.php';
require $root . '/src/bootstrap.php';
$check('open redirect target is rejected', safe_redirect_path('https://evil.example') === '/');
$check('protocol-relative redirect is rejected', safe_redirect_path('//evil.example') === '/');
$check('local redirect is retained', safe_redirect_path('/profile') === '/profile');
$meta = App\Support\Pagination::metadata(51, 20, 10);
$check('pagination metadata is calculated', $meta['page'] === 3 && $meta['pages'] === 6 && $meta['has_next']);
$check('health route is registered in front controller', str_contains((string) file_get_contents($root . '/public/index.php'), "'/health'"));
$check('compiled CSS exists', is_file($root . '/public/assets/app.css') && filesize($root . '/public/assets/app.css') > 0);
exit($failed === 0 ? 0 : 1);
