# PHP 8.5.12 boilerplate

A dependency-light starter for server-rendered PHP applications, with optional authentication and Supabase/PostgreSQL support.

## Requirements

- PHP 8.5.12 or newer
- PHP `pdo_pgsql` for PostgreSQL/Supabase (optional)
- Node.js/npm only when rebuilding Tailwind CSS
- Apache 2.4 with `mod_rewrite`, or PHP's built-in server

## Quick start

```sh
cp .env.example .env
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

Open `http://127.0.0.1:8000`.

Authentication and database access are disabled by default:

```dotenv
AUTH_ENABLED=false
DB_DRIVER=none
```

Set `AUTH_ENABLED=true` to enable the included account features. Choose `DB_DRIVER=postgres` or `DB_DRIVER=supabase` and configure the corresponding PostgreSQL credentials.

Run `database/schema.sql` in PostgreSQL or Supabase SQL Editor only when authentication is enabled.

## Tailwind CSS

The project uses Tailwind CSS v4. Source styles live in `resources/css/input.css`; the public stylesheet is `public/assets/app.css`.

```sh
npm install
npm run build:css
npm run watch:css
```

PHP does not require Node/npm at runtime.

## Configuration

| Variable | Default | Purpose |
| --- | --- | --- |
| `APP_NAME` | PHP Boilerplate | Application name |
| `APP_ENV` | development | Runtime environment |
| `APP_URL` | `http://127.0.0.1:8000` | Public origin |
| `AUTH_ENABLED` | `false` | Enable local sessions and account routes |
| `DB_DRIVER` | `none` | `none`, `postgres`, or `supabase` |
| `DB_HOST` | empty | PostgreSQL host |
| `DB_PORT` | `5432` | PostgreSQL port |
| `DB_NAME` | `postgres` | PostgreSQL database |
| `DB_USER` | empty | PostgreSQL user |
| `DB_PASSWORD` | empty | PostgreSQL password |
| `DB_SSLMODE` | `require` | `require`, `verify-ca`, or `verify-full` |

Keep `.env` private. Never put database passwords or Supabase service-role credentials in browser code.

## PWA and SEO

- Update `public/manifest.webmanifest`, `public/icon.svg`, `public/robots.txt`, and `public/sitemap.xml` with production values.
- Set `APP_URL` to the public origin.
- PWA service workers require HTTPS except on `localhost`.
- Public routes receive canonical and Open Graph metadata from `templates/layout.php`.

## Tests

```sh
php tests/smoke.php
```

The smoke test validates PHP syntax, JSON files, XML documents, PWA assets, and optional `data/*.json` files.

## License

MIT
