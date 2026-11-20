# PHP Boilerplate

A modular, dependency-light PHP 8.5.12 starter for server-rendered applications. Authentication and PostgreSQL/Supabase access are optional and disabled by default.

## Requirements

- PHP 8.5.12 or newer
- Optional `pdo_pgsql` for PostgreSQL or Supabase
- Node.js and npm only for Tailwind CSS builds
- Apache 2.4 with `mod_rewrite`, or PHP's built-in server

## Quick start

```sh
cp .env.example .env
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

Open `http://127.0.0.1:8000`.

The starter runs without a database or Composer. Set authentication and database options only when needed:

```dotenv
AUTH_ENABLED=false
DB_DRIVER=none
```

To enable accounts:

```dotenv
AUTH_ENABLED=true
DB_DRIVER=postgres
# or DB_DRIVER=supabase
DB_HOST=...
DB_PORT=5432
DB_NAME=postgres
DB_USER=...
DB_PASSWORD=...
DB_SSLMODE=require
```

For Supabase, use the direct or pooled PostgreSQL host and credentials from the project connection panel. Supabase is PostgreSQL via PDO; no Supabase PHP SDK is required.

## Tailwind CSS

Tailwind CSS 4 source is in `resources/css/input.css`. The generated public stylesheet is `public/assets/app.css`.

```sh
npm install
npm run build:css
npm run watch:css
```

There is no runtime dependency on Node/npm or Composer.

## Routing and templates

`public/index.php` bootstraps the environment, helpers, router, and optional authentication. `src/Router.php` dispatches exact `GET` and `POST` routes. `templates/layout.php` renders the selected `$view` template.

Public routes include `/`, `/robots.txt`, `/sitemap.xml`, `/manifest.webmanifest`, `/service-worker.js`, `/llms.txt`, and `/.well-known/agent.json`.

Authentication routes are registered only when `AUTH_ENABLED=true`: `/login`, `/register`, `/profile`, `/logout`, and `/profile/delete`.

## SEO and PWA

Canonical links and Open Graph/Twitter metadata are produced in `templates/layout.php`. `APP_URL` controls absolute URLs, canonical links, sitemap output, robots output, and PWA asset registration.

The service worker caches public assets and public pages only. Login, register, profile, and logout requests are never served from its cache.

## Agent discovery

The repository includes `public/llms.txt` and `public/.well-known/agent.json` as optional discovery metadata. Keep these files aligned with the application routes and scope.

## Tests

```sh
php tests/smoke.php
```

The smoke test runs `php -l` for PHP files, validates required files and JSON, checks the sitemap XML, and verifies key template and PWA integration points.

## License

MIT
