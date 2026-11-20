<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'GET' && $path === '/manifest.webmanifest') {
    header('Content-Type: application/manifest+json; charset=utf-8');
    readfile(__DIR__ . '/manifest.webmanifest');
    exit;
}

if ($method === 'GET' && $path === '/service-worker.js') {
    header('Content-Type: application/javascript; charset=utf-8');
    readfile(__DIR__ . '/service-worker.js');
    exit;
}

if ($method === 'GET' && $path === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    readfile(__DIR__ . '/robots.txt');
    exit;
}

if ($method === 'GET' && $path === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    readfile(__DIR__ . '/sitemap.xml');
    exit;
}

require dirname(__DIR__) . '/templates/layout.php';
