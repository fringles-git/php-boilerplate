<section class="card"><h1>Unable to continue</h1><?php foreach (($errors ?? []) as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?><a href="<?= esc(url('/')) ?>">Return home</a></section>
