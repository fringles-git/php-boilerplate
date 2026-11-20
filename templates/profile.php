<section class="card">
    <h1>Your profile</h1>
    <?php if (!empty($errors)): ?><ul class="errors"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
        <label>Name<input name="name" required maxlength="100" value="<?= esc($user['name']) ?>"></label>
        <label>Email<input name="email" type="email" required maxlength="254" value="<?= esc($user['email']) ?>"></label>
        <button type="submit">Save</button>
    </form>

    <form method="post" action="<?= esc($baseUrl) ?>/profile/delete" onsubmit="return confirm('Delete this account permanently?');">
        <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
        <button class="danger" type="submit">Delete account</button>
    </form>
</section>
