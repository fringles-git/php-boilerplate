<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>PHP Boilerplate - PHP 8.5 and Supabase Starter</title>
    <meta name="description" content="A secure, reusable PHP 8.5.12 starter with Supabase PostgreSQL, Tailwind CSS, SEO, and PWA support.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2558a6">
    <meta name="color-scheme" content="light">

    <link rel="canonical" href="<?= esc($canonical ?? 'https://example.com/') ?>">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/app.css">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">
    <meta property="og:site_name" content="PHP Boilerplate">
    <meta property="og:title" content="PHP Boilerplate">
    <meta property="og:description" content="A secure PHP 8.5.12 starter for Supabase PostgreSQL.">
    <meta property="og:url" content="<?= esc($canonical ?? 'https://example.com/') ?>">
    <meta property="og:image" content="/icon.svg">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="PHP Boilerplate">
    <meta name="twitter:description" content="A secure PHP 8.5.12 starter for Supabase PostgreSQL.">
    <meta name="twitter:image" content="/icon.svg">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => 'PHP Boilerplate',
        'applicationCategory' => 'DeveloperApplication',
        'operatingSystem' => 'Web',
        'description' => 'A secure PHP 8.5.12 starter with Supabase PostgreSQL and Tailwind CSS.',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
</head>
<body>
    <main>
        <h1>PHP Boilerplate</h1>
        <p>PHP 8.5.12, Supabase PostgreSQL, Tailwind CSS, SEO, and PWA support.</p>
    </main>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
