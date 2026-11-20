<?php

declare(strict_types=1);

use App\Auth;
use App\Database;
use App\UserRepository;

require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/src/Database.php';
require dirname(__DIR__) . '/src/Support.php';
require dirname(__DIR__) . '/src/UserRepository.php';
require dirname(__DIR__) . '/src/Auth.php';

if (PHP_VERSION_ID < 80500) {
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
$users = new UserRepository(Database::connection());
$auth = new Auth($users);
$user = $auth->currentUser();
$errors = [];
$old = [];

try {
    if ($method === 'POST' && !csrf_validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $errors[] = 'Your form session expired. Please try again.';
        render('error', compact('errors', 'user'));
        exit;
    }

    if ($path === '/' && $method === 'GET') {
        render('home', compact('user'));
        exit;
    }

    if ($path === '/register' && $method === 'GET') {
        render('register', compact('user', 'errors', 'old'));
        exit;
    }

    if ($path === '/register' && $method === 'POST') {
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
            } catch (\PDOException $exception) {
                if ($exception->getCode() === '23505') {
                    $errors[] = 'That username or email address is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
        render('register', compact('user', 'errors', 'old'));
        exit;
    }

    if ($path === '/login' && $method === 'GET') {
        render('login', compact('user', 'errors', 'old'));
        exit;
    }

    if ($path === '/login' && $method === 'POST') {
        $old['username'] = strtolower(trim((string) ($_POST['username'] ?? '')));
        $candidate = $users->findByLogin($old['username']);
        if ($candidate !== null && password_verify((string) ($_POST['password'] ?? ''), $candidate['password_hash'])) {
            if (password_needs_rehash($candidate['password_hash'], PASSWORD_DEFAULT)) {
                $users->updatePassword((int) $candidate['id'], password_hash((string) $_POST['password'], PASSWORD_DEFAULT));
            }
            $auth->login($candidate);
            flash('You are now logged in.');
            redirect('/profile');
        }
        $errors[] = 'The username/email or password was incorrect.';
        render('login', compact('user', 'errors', 'old'));
        exit;
    }

    if ($path === '/logout' && $method === 'POST') {
        $auth->logout();
        flash('You have been logged out.');
        redirect('/');
    }

    if ($path === '/profile' && $method === 'GET') {
        require_login($user);
        render('profile', compact('user', 'errors'));
        exit;
    }

    if ($path === '/profile' && $method === 'POST') {
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
            } catch (\PDOException $exception) {
                if ($exception->getCode() === '23505') {
                    $errors[] = 'That email address is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
        $user['name'] = $name;
        $user['email'] = $email;
        render('profile', compact('user', 'errors'));
        exit;
    }

    if ($path === '/profile/delete' && $method === 'POST') {
        require_login($user);
        $users->delete((int) $user['id']);
        $auth->logout();
        flash('Your account has been deleted.');
        redirect('/');
    }

    http_response_code(404);
    render('404', compact('user'));
} catch (\Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    render('error', ['user' => $user, 'errors' => ['Something went wrong. Check the server log for details.']]);
}
