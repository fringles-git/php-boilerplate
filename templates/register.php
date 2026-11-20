<section class="card">
    <h1>Create account</h1>
    <?php if ($errors !== []): ?><ul class="errors"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <form method="post" action="<?= esc(url('/register')) ?>">
        <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
        <label>Name<input name="name" autocomplete="name" required maxlength="100" value="<?= esc($old['name'] ?? '') ?>"></label>
        <label>Username<input name="username" autocomplete="username" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_]+" value="<?= esc($old['username'] ?? '') ?>"></label>
        <label>Email<input name="email" type="email" autocomplete="email" required maxlength="254" value="<?= esc($old['email'] ?? '') ?>"></label>
        <label>Password<input name="password" type="password" autocomplete="new-password" required minlength="12"></label>
        <button type="submit">Register</button>
    </form>
</section>
