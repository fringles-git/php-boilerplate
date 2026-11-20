<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? $appName) ?></title>
  <meta name="description" content="<?= esc($description ?? 'A reusable PHP 8.5.12 starter for modern, server-rendered applications.') ?>">
  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#2558a6">
  <link rel="canonical" href="<?= esc($canonical ?? url('/')) ?>">
  <link rel="manifest" href="<?= esc(url('/manifest.webmanifest')) ?>">
  <link rel="icon" href="<?= esc(url('/icon.svg')) ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= esc(url('/assets/app.css')) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= esc($appName) ?>">
  <meta property="og:title" content="<?= esc($title ?? $appName) ?>">
  <meta property="og:description" content="<?= esc($description ?? 'A reusable PHP 8.5.12 starter.') ?>">
  <meta property="og:url" content="<?= esc($canonical ?? url('/')) ?>">
  <meta property="og:image" content="<?= esc(url('/icon.svg')) ?>">

  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="<?= esc($title ?? $appName) ?>">
  <meta name="twitter:description" content="<?= esc($description ?? 'A reusable PHP 8.5.12 starter.') ?>">
  <meta name="twitter:image" content="<?= esc(url('/icon.svg')) ?>">

  <script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $appName,
    'applicationCategory' => 'DeveloperApplication',
    'operatingSystem' => 'Web',
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>
  <header>
    <nav class="shell" aria-label="Main navigation">
      <a class="brand" href="<?= esc(url('/')) ?>"><?= esc($appName) ?></a>
      <?php if ($authEnabled): ?>
        <?php if ($user !== null): ?>
          <a href="<?= esc(url('/profile')) ?>">Profile</a>
          <form method="post" action="<?= esc(url('/logout')) ?>">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
            <button type="submit">Log out</button>
          </form>
        <?php else: ?>
          <a href="<?= esc(url('/login')) ?>">Log in</a>
          <a href="<?= esc(url('/register')) ?>">Register</a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>
  </header>

  <main class="shell">
    <?php foreach (flash_all() as $message): ?>
      <div class="alert <?= $message['type'] === 'error' ? 'alert-error' : '' ?>" role="status"><?= esc($message['text']) ?></div>
    <?php endforeach; ?>
    <?php require $view; ?>
  </main>

  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('<?= esc(url('/service-worker.js')) ?>').catch(() => {}));
    }
  </script>
</body>
</html>
