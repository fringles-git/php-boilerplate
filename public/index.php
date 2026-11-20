<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/src/Support.php';
require dirname(__DIR__) . '/src/Router.php';
require dirname(__DIR__) . '/src/Database.php';
require dirname(__DIR__) . '/src/UserRepository.php';
require dirname(__DIR__) . '/src/Auth.php';

if (PHP_VERSION_ID < 80512) {
    http_response_code(500);
    exit('PHP 8.5.12 or newer is required.');
}

$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_name('php_boilerplate_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$authEnabled = config_flag('AUTH_ENABLED', false);
$user = null;
$errors = [];
$old = [];
$users = null;
$auth = null;

if ($authEnabled) {
    try {
        $users = new App\UserRepository(App\Database::connection());
        $auth = new App\Auth($users);
        $user = $auth->currentUser();
    } catch (Throwable $exception) {
        error_log((string) $exception);
        http_response_code(500);
        render('error', ['errors' => ['Authentication is enabled, but the database is unavailable.']]);
        exit;
    }
}

$router = new App\Router($method, $path);

$router->get('/manifest.webmanifest', static function (): void {
    header('Content-Type: application/manifest+json; charset=utf-8');
    readfile(__DIR__ . '/manifest.webmanifest');
});

$router->get('/service-worker.js', static function (): void {
    header('Content-Type: application/javascript; charset=utf-8');
    readfile(__DIR__ . '/service-worker.js');
});

$router->get('/icon.svg', static function (): void {
    header('Content-Type: image/svg+xml; charset=utf-8');
    readfile(__DIR__ . '/icon.svg');
});

$router->get('/robots.txt', static function (): void {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml') . "\n";
});

$router->get('/sitemap.xml', static function (): void {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url><loc>' . esc(url('/')) . '</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>' . "\n";
    echo '</urlset>' . "\n";
});

$router->get('/llms.txt', static function (): void {
    header('Content-Type: text/plain; charset=utf-8');
    readfile(dirname(__DIR__) . '/public/llms.txt');
});

$router->get('/.well-known/agent.json', static function (): void {
    header('Content-Type: application/json; charset=utf-8');
    readfile(dirname(__DIR__) . '/public/.well-known/agent.json');
});

$router->get('/', static function () use ($user, $authEnabled): void {
    render('home', [
        'title' => config('APP_NAME', 'PHP Boilerplate'),
        'description' => 'A reusable PHP 8.5.12 starter for modern server-rendered applications.',
        'canonical' => url('/'),
        'user' => $user,
        'authEnabled' => $authEnabled,
    ]);
});

if ($authEnabled) {
    $router->get('/login', static function () use ($user, $authEnabled, &$errors, &$old): void {
        render('login', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors, 'old' => $old]);
    });

    $router->post('/login', static function () use (&$old, &$errors, $users, $auth, $user, $authEnabled): void {
        verify_csrf();
        $old['username'] = strtolower(trim((string) ($_POST['username'] ?? '')));
        $candidate = $users->findByLogin($old['username']);
        $password = (string) ($_POST['password'] ?? '');
        if ($candidate !== null && password_verify($password, $candidate['password_hash'])) {
            if (password_needs_rehash($candidate['password_hash'], PASSWORD_DEFAULT)) {
                $users->updatePassword((int) $candidate['id'], password_hash($password, PASSWORD_DEFAULT));
            }
            $auth->login($candidate);
            flash('You are now logged in.');
            redirect('/profile');
        }
        $errors[] = 'The username/email or password was incorrect.';
        render('login', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors, 'old' => $old]);
    });

    $router->get('/register', static function () use ($user, $authEnabled, &$errors, &$old): void {
        render('register', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors, 'old' => $old]);
    });

    $router->post('/register', static function () use (&$old, &$errors, $users, $auth, $user, $authEnabled): void {
        verify_csrf();
        $old = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'username' => strtolower(trim((string) ($_POST['username'] ?? ''))),
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
        ];
        $password = (string) ($_POST['password'] ?? '');
        if ($old['name'] === '' || mb_strlen($old['name']) > 100) {
            $errors[] = 'Name is required and must be at most 100 characters.';
        }
        if (!preg_match('/\A[a-z0-9_]{3,30}\z/', $old['username'])) {
            $errors[] = 'Username must be 3–30 characters using letters, numbers, or underscores.';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 254) {
            $errors[] = 'Enter a valid email address.';
        }
        if (strlen($password) < 12) {
            $errors[] = 'Password must be at least 12 characters.';
        }
        if ($errors === []) {
            try {
                $newUser = $users->create($old['name'], $old['username'], $old['email'], $password);
                $auth->login($newUser);
                flash('Account created. Welcome!');
                redirect('/profile');
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23505') {
                    $errors[] = 'That username or email address is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
        render('register', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors, 'old' => $old]);
    });

    $router->get('/profile', static function () use ($user, $authEnabled, &$errors): void {
        require_login($user);
        render('profile', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors]);
    });

    $router->post('/profile', static function () use ($users, $authEnabled, &$errors, $user): void {
        verify_csrf();
        require_login($user);
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Name is required and must be at most 100 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            $errors[] = 'Enter a valid email address.';
        }
        if ($errors === []) {
            try {
                $users->updateProfile((int) $user['id'], $name, $email);
                flash('Profile updated.');
                redirect('/profile');
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23505') {
                    $errors[] = 'That email address is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
        $user['name'] = $name;
        $user['email'] = $email;
        render('profile', ['user' => $user, 'authEnabled' => $authEnabled, 'errors' => $errors]);
    });

    $router->post('/logout', static function () use ($auth): void {
        verify_csrf();
        $auth->logout();
        flash('You have been logged out.');
        redirect('/');
    });

    $router->post('/profile/delete', static function () use ($users, $auth, $user): void {
        verify_csrf();
        require_login($user);
        $users->delete((int) $user['id']);
        $auth->logout();
        flash('Your account has been deleted.');
        redirect('/');
    });
}

$router->dispatch();
