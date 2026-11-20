# PHP Boilerplate

A secure, reusable PHP starter using **PHP 8.5.12** and **Supabase PostgreSQL**. No Composer, framework, or Docker required. Tailwind CSS is included for styling; Node/npm is only needed when rebuilding the CSS.

## Requirements

- PHP 8.5.12 or newer
- PHP `pdo_pgsql` extension
- Supabase project with database access
- Node.js/npm only for Tailwind CSS builds
- Apache 2.4 with `mod_rewrite`, or PHP's built-in development server

## Quick start

1. Copy `.env.example` to `.env` and set your Supabase PostgreSQL connection details from **Supabase Dashboard → Connect**.
2. In Supabase SQL Editor, run `database/schema.sql`.
3. Install CSS tooling and build the stylesheet (optional if you keep/edit the committed CSS):

   ```sh
   npm install
   npm run build:css
   ```

4. Start the PHP development server from the repository root:

   ```sh
   php -S 127.0.0.1:8000 -t public
   ```

5. Open `http://127.0.0.1:8000`.

The web server document root must be `public/`; do not expose `.env`, `src/`, `config/`, or `database/` to the web.

## Supabase connection

The app connects to Supabase PostgreSQL using PDO (`pdo_pgsql`) and TLS (`DB_SSLMODE=require`). Copy the exact host, port, database, and username shown by your Supabase project's **Connect** panel. A pooled connection is usually a good fit for PHP's short-lived web requests; use the pooler address and credentials provided by Supabase. Do not guess its region or username format.

This starter uses a server-side database connection and a local PHP session for its example authentication. Never put database credentials, a Supabase secret/service-role key, or any other privileged credential in browser JavaScript. Keep database access restricted and review Supabase network access and RLS policies for your deployment. Direct database connections made with a privileged server credential bypass client-side Supabase Auth/RLS assumptions; never trust request data or expose this connection to users.

## Tailwind CSS

Tailwind v3 is pinned in `package.json`; configuration is in `tailwind.config.js`, and styles live in `resources/css/input.css`. The generated stylesheet is `public/assets/app.css`.

```sh
npm install
npm run build:css
npm run watch:css
```

PHP does not need Node/npm at runtime. Composer and Docker are not used.

## Included

- Front controller and small URL dispatcher
- Secure session cookie settings, session ID rotation, CSRF tokens, and output escaping
- PDO PostgreSQL prepared queries
- Registration, login, profile update, logout, and account deletion examples
- `password_hash()` / `password_verify()` and password rehashing
- Supabase SQL schema, Tailwind CSS config, and dependency-free PHP smoke checks

## Commands

```sh
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

## License

MIT
