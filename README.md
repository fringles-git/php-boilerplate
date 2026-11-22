# PHP Boilerplate

A dependency-light PHP 8.5.12 starter with a small MVC structure, server-rendered templates, optional PostgreSQL authentication, and no Composer or Docker requirement.

## Requirements and quick start

- PHP 8.5.12 or newer (plus `mbstring` for UTF-8 validation)
- Optional `pdo_pgsql` for PostgreSQL/Supabase authentication
- Node.js/npm only for building Tailwind CSS

```sh
cp .env.example .env
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

Set `AUTH_ENABLED=true` and configure the PostgreSQL settings in `.env` to enable accounts. Apply `database/schema.sql` to the database first. Existing installations are upgraded with the role-column addition in that schema.

## Architecture

- `public/index.php` is the single front controller and registers routes.
- `src/bootstrap.php` registers the `App\\` autoloader, configures sessions/security headers, and installs environment-aware exception handling.
- `src/Http` contains the normalized request and response objects; `src/Router.php` dispatches exact routes.
- `src/Controllers` contains thin home, authentication, and profile controllers.
- `src/Models/User.php` is the typed readonly user entity; `src/UserRepository.php` owns SQL persistence.
- `src/Validator.php` returns field-keyed validation errors; `src/Support` holds logging, pagination, text, and date helpers.
- `templates/` contains server-rendered page views and the shared layout.

PDO uses exception mode, associative fetches, native prepared statements, and a transaction helper. Sessions use HttpOnly/SameSite cookies, CSRF tokens, ID rotation on authentication changes, and local-only redirect validation. Validation failures use PRG with safe old-input restoration; passwords are never repopulated.

## CLI and endpoints

```sh
php bin/console help
php bin/console health
```

`GET /health` returns a minimal JSON liveness response. The CLI health command also verifies database connectivity when authentication is enabled. Other public routes include `/`, `/robots.txt`, `/sitemap.xml`, `/manifest.webmanifest`, `/service-worker.js`, `/llms.txt`, and `/.well-known/agent.json`.

## Assets and tests

Tailwind sources are in `resources/css/input.css`; build with `npm install && npm run build:css`. Run the PHP syntax and core smoke checks with `php tests/smoke.php`.
