<?php

declare(strict_types=1);

namespace App;

final class RememberMe
{
    private const COOKIE_NAME = 'php_boilerplate_remember';

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function issue(UserRepository $users, int $userId): void
    {
        $selector = bin2hex(random_bytes(12));
        $verifier = bin2hex(random_bytes(32));
        $users->createRememberToken($userId, $selector, hash('sha256', $verifier), time() + (86400 * 30));
        Cookie::put(self::COOKIE_NAME, $selector . ':' . $verifier, time() + (86400 * 30), ['samesite' => 'Lax']);
    }

    public function currentUser(): ?\App\Models\User
    {
        $value = Cookie::get(self::COOKIE_NAME);
        if (!is_string($value) || substr_count($value, ':') !== 1) {
            return null;
        }
        [$selector, $verifier] = explode(':', $value, 2);
        if (!preg_match('/\A[a-f0-9]{24}\z/', $selector) || !preg_match('/\A[a-f0-9]{64}\z/', $verifier)) {
            $this->clear();
            return null;
        }
        $row = $this->users->findRememberToken($selector);
        if ($row === null || $row['expires_at'] < time() || !hash_equals((string) $row['verifier_hash'], hash('sha256', $verifier))) {
            if ($row !== null) {
                $this->users->deleteRememberToken($selector);
            }
            $this->clear();
            return null;
        }
        return $this->users->findById((int) $row['user_id']);
    }

    public function clear(): void
    {
        $value = Cookie::get(self::COOKIE_NAME);
        if (is_string($value) && substr_count($value, ':') === 1) {
            [$selector] = explode(':', $value, 2);
            $this->users->deleteRememberToken($selector);
        }
        Cookie::delete(self::COOKIE_NAME);
    }
}
