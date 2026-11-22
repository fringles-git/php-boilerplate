<?php

declare(strict_types=1);

namespace App;

final class Auth
{
    public function __construct(private readonly UserRepository $users) {}

    public function currentUser(): ?\App\Models\User
    {
        $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null) {
            $remember = new RememberMe($this->users);
            $remembered = $remember->currentUser();
            if ($remembered !== null) {
                $this->login($remembered);
            }
            return $remembered;
        }
        $user = $this->users->findById((int) $id);
        if ($user === null) unset($_SESSION['user_id']);
        return $user;
    }

    public function login(\App\Models\User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        unset($_SESSION['csrf_token']);
    }

    public function can(?\App\Models\User $user, string $permission): bool
    {
        return $user !== null && ($user->role === 'admin' || ($user->role === 'user' && $permission === 'profile.read'));
    }

    public function logout(): void
    {
        (new RememberMe($this->users))->clear();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $params['path'], 'domain' => $params['domain'], 'secure' => $params['secure'], 'httponly' => $params['httponly'], 'samesite' => $params['samesite'] ?? 'Lax']);
        }
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
}
