<?php

declare(strict_types=1);

namespace App;

use App\Models\User;
use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(string $name, string $username, string $email, string $password): User
    {
        $statement = $this->pdo->prepare('INSERT INTO public.users (name, username, email, password_hash) VALUES (:name, :username, :email, :password_hash) RETURNING id, name, username, email, role');
        $statement->execute(['name' => $name, 'username' => $username, 'email' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        return User::fromRow($statement->fetch() ?: throw new \RuntimeException('User insert returned no row.'));
    }

    /** @return array{user: User, password_hash: string}|null */
    public function findByLogin(string $login): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, name, username, email, role, password_hash FROM public.users WHERE username = :username OR email = :email LIMIT 1');
        $statement->execute(['username' => $login, 'email' => $login]);
        $row = $statement->fetch();
        return $row === false ? null : ['user' => User::fromRow($row), 'password_hash' => (string) $row['password_hash']];
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT id, name, username, email, role FROM public.users WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $statement = $this->pdo->prepare('UPDATE public.users SET password_hash = :hash WHERE id = :id');
        $statement->execute(['hash' => $hash, 'id' => $id]);
    }

    public function updateProfile(int $id, string $name, string $email): void
    {
        $statement = $this->pdo->prepare('UPDATE public.users SET name = :name, email = :email WHERE id = :id');
        $statement->execute(['name' => $name, 'email' => $email, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM public.users WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
