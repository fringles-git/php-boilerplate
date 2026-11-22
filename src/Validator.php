<?php

declare(strict_types=1);

namespace App;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function required(string $field, string $value, string $label): self
    {
        if ($value === '') {
            $this->errors[$field] = $label . ' is required.';
        }
        return $this;
    }

    public function maxLength(string $field, string $value, int $max, string $label): self
    {
        if (mb_strlen($value, 'UTF-8') > $max) {
            $this->errors[$field] = $label . ' must be at most ' . $max . ' characters.';
        }
        return $this;
    }

    public function email(string $field, string $value): self
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false || strlen($value) > 254) {
            $this->errors[$field] = 'Enter a valid email address.';
        }
        return $this;
    }

    public function username(string $value): self
    {
        if (!preg_match('/\A[a-z0-9_]{3,30}\z/', $value)) {
            $this->errors['username'] = 'Use 3–30 lowercase letters, numbers, or underscores.';
        }
        return $this;
    }

    public function password(string $value, int $minimum = 12): self
    {
        if (strlen($value) < $minimum) {
            $this->errors['password'] = 'Password must be at least ' . $minimum . ' characters.';
        }
        return $this;
    }

    public function add(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function valid(): bool
    {
        return $this->errors === [];
    }
}
