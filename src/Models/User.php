<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $role = 'user',
    ) {
        if ($id < 1 || $name === '' || !in_array($role, ['user', 'admin'], true)) {
            throw new \InvalidArgumentException('Invalid user identity or role.');
        }
    }

    public static function fromRow(array $row): self
    {
        return new self((int) $row['id'], (string) $row['name'], (string) ($row['username'] ?? ''), (string) $row['email'], (string) ($row['role'] ?? 'user'));
    }

    public static function fromArray(array $data): self
    {
        return self::fromRow($data);
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'username' => $this->username, 'email' => $this->email, 'role' => $this->role];
    }
}
