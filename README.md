# PHP Boilerplate

A secure, reusable PHP starter using **PHP 8.5.12** and **Supabase PostgreSQL**. No Composer, framework, or Docker required. The repository is a small, server-rendered app you can copy and adapt.

## Requirements

- PHP 8.5.12 (the code targets PHP 8.5; it checks for PHP 8.5 or newer)
- `pdo_pgsql` PHP extension
- Supabase project with database access
- Apache 2.4 with `mod_rewrite`, or PHP's built-in development server

## Quick start

1. Copy `.env.example` to `.env` and set your Supabase PostgreSQL connection details from **Supabase Dashboard → Connect**.
2. In Supabase SQL Editor, run `database/schema.sql`.
3. Start the PHP development server from the repository root:

   ```sh
   php -S 127.0.0.1:8000 -t public
   ```

4. Open `http://127.0.0.1:8000`.

The web server document root must be `public/`; do not expose `.env`, `src/`, `config/`, or `database/` to the web.

## Supabase connection notes

This template connects to Supabase PostgreSQL using PHP PDO (`pdo_pgsql`) and TLS (`DB_SSLMODE=require`). Copy the exact host, port, database, and username shown by your Supabase project's **Connect** panel. A pooled connection is usually a good fit for PHP's short-lived web requests; use the pooler address and credentials provided by Supabase. Do not guess the pooler region or username format. `DB_SSLMODE=require` encrypts the connection; use certificate verification options only when you have configured the required CA certificates.

This starter uses a server-side database connection and a local PHP session for its example authentication. Never put database credentials, a Supabase secret/service-role key, or any other privileged credential in browser JavaScript. For production, keep database access restricted and review Supabase network access and RLS policies for your deployment. Direct database connections made with a privileged server credential bypass client-side Supabase Auth/RLS assumptions; never trust request data or expose this connection to users.

## Included

- Front controller and small URL dispatcher
- Secure session cookie settings, session ID rotation, CSRF tokens, and output escaping
- PDO PostgreSQL prepared queries
- Registration, login, profile update, logout, and account deletion examples
- `password_hash()` / `password_verify()` and password rehashing
- SQL schema, minimal CSS, and dependency-free smoke checks

## Commands

```sh
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

## License

MIT
