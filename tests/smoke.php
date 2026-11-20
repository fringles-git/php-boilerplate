<?php

declare(strict_types=1);

namespace {
    $root = dirname(__DIR__);
    $failed = 0;

    $check = static function (string $label, bool $passed) use (&$failed): void {
        echo ($passed ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
        if (!$passed) {
            $failed++;
        }
    };

    $check('PHP runtime is 8.5.12 or newer', PHP_VERSION_ID >= 80512);

    $phpFiles = [];
    $directories = [$root . '/config', $root . '/src', $root . '/public', $root . '/templates', $root . '/tests'];
    foreach ($directories as $directory) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $phpFiles[] = $file->getPathname();
            }
        }
    }

    foreach ($phpFiles as $phpFile) {
        $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($phpFile);
        exec($command, $output, $status);
        $check('PHP syntax: ' . substr($phpFile, strlen($root) + 1), $status === 0);
    }

    $requiredFiles = [
        'config/app.php', 'src/Support.php', 'src/Router.php', 'src/Database.php',
        'src/Auth.php', 'src/UserRepository.php', 'public/index.php',
        'templates/layout.php', 'public/manifest.webmanifest', 'public/service-worker.js',
        'public/robots.txt', 'public/sitemap.xml', 'public/icon.svg',
    ];
    foreach ($requiredFiles as $relativePath) {
        $check('File exists: ' . $relativePath, is_file($root . '/' . $relativePath));
    }

    $manifest = is_file($root . '/public/manifest.webmanifest')
        ? json_decode((string) file_get_contents($root . '/public/manifest.webmanifest'), true, 512, JSON_THROW_ON_ERROR)
        : null;
    $check('manifest.webmanifest is valid JSON', is_array($manifest));
    $check('manifest has name and start_url', is_array($manifest) && isset($manifest['name'], $manifest['start_url']));

    $layout = (string) file_get_contents($root . '/templates/layout.php');
    $check('layout renders $view', str_contains($layout, 'require $view'));
    $index = (string) file_get_contents($root . '/public/index.php');
    $check('index bootstraps config and Support', str_contains($index, 'config/app.php') && str_contains($index, 'src/Support.php'));

    $css = (string) file_get_contents($root . '/public/assets/app.css');
    $check('public CSS is compiled', !str_contains($css, '@tailwind') && !str_contains($css, '@import "tailwindcss"'));

    foreach ([
        $root . '/public/robots.txt',
        $root . '/public/sitemap.xml',
        $root . '/public/service-worker.js',
        $root . '/public/manifest.webmanifest',
    ] as $pwaFile) {
        $check('PWA/SEO file exists: ' . basename($pwaFile), is_file($pwaFile) && filesize($pwaFile) > 0);
    }

    $xml = file_get_contents($root . '/public/sitemap.xml');
    $check('sitemap.xml is present XML', is_string($xml) && str_starts_with(ltrim($xml), '<?xml'));
    if (class_exists('DOMDocument') && is_string($xml)) {
        $document = new DOMDocument();
        $validXml = @$document->loadXML($xml);
        $check('sitemap.xml is valid XML', $validXml !== false);
    }

    $dataDirectory = $root . '/data';
    if (is_dir($dataDirectory)) {
        foreach (glob($dataDirectory . '/*.json') ?: [] as $jsonFile) {
            try {
                json_decode((string) file_get_contents($jsonFile), true, 512, JSON_THROW_ON_ERROR);
                $validJson = true;
            } catch (JsonException) {
                $validJson = false;
            }
            $check('Valid JSON: data/' . basename($jsonFile), $validJson);
        }
    }

    exit($failed === 0 ? 0 : 1);
}
