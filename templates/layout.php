<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? $appName) ?></title>
    <link rel="stylesheet" href="<?= esc(url('/assets/app.css')) ?>">
</head>
<body>
<main class="shell">
    <nav aria-label="Main navigation">
        <a class="brand" href="<?= esc(url('/')) ?>">PHP Boilerplate</a>
        <div>
            <?php if ($user !== null): ?>
                <a href="<?= esc(url('/profile')) ?>">Profile</a>
                <form class="inline" method="post" action="<?= esc(url('/logout')) ?>">
                    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
                    <button class="link-button" type="submit">Log out</button>
                </form>
            <?php else: ?>
                <a href="<?= esc(url('/login')) ?>">Log in</a>
                <a href="<?= esc(url('/register')) ?>">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <?php foreach (flash_all() as $message): ?>
        <div class="alert <?= esc($message['type']) ?>" role="status"><?= esc($message['text']) ?></div>
    <?php endforeach; ?>
    <?php require $view; ?>
</main>
</body>
</html>
