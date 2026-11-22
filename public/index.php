<?php

declare(strict_types=1);

use App\Auth;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Database;
use App\Http\Request;
use App\Router;
use App\UserRepository;

require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/src/bootstrap.php';

$request = Request::fromGlobals();
$router = new Router();
$home = new HomeController();
$router->get('/', [$home, 'show']);
$router->get('/health', [$home, 'health']);

$router->get('/manifest.webmanifest', static fn (Request $request) => new App\Http\Response((string) file_get_contents(__DIR__ . '/manifest.webmanifest'), 200, ['Content-Type' => 'application/manifest+json; charset=utf-8']));
$router->get('/service-worker.js', static fn (Request $request) => new App\Http\Response((string) file_get_contents(__DIR__ . '/service-worker.js'), 200, ['Content-Type' => 'application/javascript; charset=utf-8']));
$router->get('/icon.svg', static fn (Request $request) => new App\Http\Response((string) file_get_contents(__DIR__ . '/icon.svg'), 200, ['Content-Type' => 'image/svg+xml; charset=utf-8']));
$router->get('/robots.txt', static fn (Request $request) => new App\Http\Response("User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml') . "\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']));
$router->get('/sitemap.xml', static fn (Request $request) => new App\Http\Response('<?xml version="1.0" encoding="UTF-8"?>' . "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"><url><loc>" . esc(url('/')) . "</loc><changefreq>weekly</changefreq><priority>1.0</priority></url></urlset>\n", 200, ['Content-Type' => 'application/xml; charset=utf-8']));
$router->get('/llms.txt', static fn (Request $request) => new App\Http\Response((string) file_get_contents(dirname(__DIR__) . '/public/llms.txt'), 200, ['Content-Type' => 'text/plain; charset=utf-8']));
$router->get('/.well-known/agent.json', static fn (Request $request) => new App\Http\Response((string) file_get_contents(__DIR__ . '/.well-known/agent.json'), 200, ['Content-Type' => 'application/json; charset=utf-8']));

if (config_flag('AUTH_ENABLED', false)) {
    $users = new UserRepository(Database::connection());
    $auth = new Auth($users);
    $authController = new AuthController($users, $auth);
    $profileController = new ProfileController($users, $auth);
    $router->get('/login', [$authController, 'loginForm']);
    $router->post('/login', [$authController, 'login']);
    $router->get('/register', [$authController, 'registerForm']);
    $router->post('/register', [$authController, 'register']);
    $router->post('/logout', [$authController, 'logout']);
    $router->get('/profile', [$profileController, 'show']);
    $router->post('/profile', [$profileController, 'update']);
    $router->post('/profile/delete', [$profileController, 'delete']);
}

$router->dispatch($request)->send();
