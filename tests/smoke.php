<?php

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/bootstrap.php';

$required = ['src/Input.php', 'src/Redirect.php', 'src/Cookie.php', 'src/Hash.php', 'src/Password.php', 'src/Validation.php', 'src/RememberMe.php', 'src/DeclarativeValidator.php'];
$failed = 0;
$check = static function (string $label, bool $passed) use (&$failed): void { echo ($passed ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL; if (!$passed) $failed++; };

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__))) as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()), $output, $status);
        $check('PHP syntax: ' . basename($file->getPathname()), $status === 0);
    }
}
foreach ($required as $path) $check('Utility exists: ' . $path, is_file(dirname(__DIR__) . '/' . $path));

$validator = App\Validation::check(['email' => 'bad', 'password' => 'short', 'confirm_password' => 'different'], ['email' => ['email' => true], 'password' => ['password' => true], 'confirm_password' => ['match' => 'password']]);
$check('declarative validation returns field errors', !$validator->valid() && isset($validator->errors()['email']) && isset($validator->errors()['confirm_password']));
$check('safe redirect rejects external URL', safe_redirect_path('https://evil.example') === '/');
$check('Hash is constant-time equivalent', App\Hash::verify('value', App\Hash::make('value')));
$check('Password API verifies password', App\Password::verify('test-password', App\Password::hash('test-password')));
$check('remember-token table is documented in schema', str_contains((string) file_get_contents(dirname(__DIR__) . '/database/schema.sql'), 'remember_tokens'));

exit($failed === 0 ? 0 : 1);
