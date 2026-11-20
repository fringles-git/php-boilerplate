# PHP Boilerplate

A reusable, dependency-free PHP application starter. It requires **PHP 8.5.12 or newer** and no Composer or Docker.

## Requirements

- PHP 8.5.12+
- PDO MySQL/MariaDB extension
- MySQL 8 or MariaDB 10.6+
- Apache 2.4 (or another server with front-controller routing)

## Quick start

```bash
cp .env.example .env
mysql -u root -p < database/schema.sql
php -S 127.0.0.1:8000 -t public
```

Open `http://127.0.0.1:8000`.

## Configuration

Environment values are read from `.env` and normal process environment variables. Database credentials should remain in `.env`; never commit `.env`.

## Security defaults

- Strict comparison and prepared PDO statements
- `password_hash()` / `password_verify()`
- Per-session CSRF tokens
- Session cookie `httponly`, `samesite=Lax`, and secure mode on HTTPS
- HTML output escaping
- Login username enumeration mitigations
- Database transactions for account mutations

## Useful commands

```bash
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

## License

MIT
