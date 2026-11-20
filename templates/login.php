<section class="card">
    <h1>Log in</h1>
    <?php if ($errors !== []): ?><ul class="errors"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <form method="post" action="<?= esc(url('/login')) ?>">
        <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
        <label class="form-label">Username or email<input class="field" name="username" autocomplete="username" required value="<?= esc($old['username'] ?? '') ?>"></label>
        <label class="form-label">Password<input class="field" name="password" type="password" autocomplete="current-password" required></label>
        <button type="submit">Log in</button>
    </form>
</section>
