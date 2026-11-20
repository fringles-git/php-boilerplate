<section class="card">
    <h1>Log in</h1>
    <?php if (!empty($errors)): ?><ul class="errors"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
        <label>Username or email<input name="username" autocomplete="username" required value="<?= esc($old['username'] ?? '') ?>"></label>
        <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
        <button type="submit">Log in</button>
    </form>
</section>
