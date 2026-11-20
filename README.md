# PHP Boilerplate

A reusable PHP 8.5.12 starter for Supabase PostgreSQL with Tailwind CSS, SEO metadata, PWA assets, no Composer, and no Docker.

## Requirements

- PHP 8.5.12 or newer
- PHP `pdo_pgsql` extension
- A Supabase PostgreSQL database
- Node.js/npm only when rebuilding Tailwind CSS

## Quick start

1. Copy `.env.example` to `.env` and enter the connection details shown in Supabase **Connect**.
2. Run `database/schema.sql` in Supabase SQL Editor.
3. Start the app:

```sh
php -S 127.0.0.1:8000 -t public
```

4. Open `http://127.0.0.1:8000`.

## SEO

- Canonical, Open Graph, and Twitter metadata are included in `templates/layout.php`.
- `public/robots.txt` permits public crawling and declares the sitemap URL.
- `public/sitemap.xml` contains the initial home route.
- Set `APP_URL` to the production origin and update the sitemap for every public route.
- Add real page titles and descriptions from route-specific metadata when expanding the app.

## PWA

- `public/manifest.webmanifest` defines installation metadata and icons.
- `public/service-worker.js` provides a production-safe offline fallback and cache cleanup.
- `public/icon.svg` provides a scalable app icon.
- Update the manifest name, theme colors, categories, and icon files for your project.
- Service workers require HTTPS, except for `localhost` development.

## Supabase

Use the exact pooled or direct PostgreSQL connection details shown by Supabase. Use `DB_SSLMODE=require` unless a stricter TLS/CA setup is configured.

Never expose Supabase service-role credentials or database passwords to browser JavaScript.

## Commands

```sh
php tests/smoke.php
npm install
npm run build:css
npm run watch:css
php -S 127.0.0.1:8000 -t public
```

## License

MIT
