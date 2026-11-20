<section class="intro">
    <p class="eyebrow">A minimal foundation</p>
    <h1>Build your next PHP app with confidence.</h1>
    <p>PHP 8.5.12, Supabase PostgreSQL, secure defaults — without a framework, Composer, or Docker.</p>
    <?php if ($user === null): ?>
        <p><a class="button" href="<?= esc(url('/register')) ?>">Create an account</a></p>
    <?php else: ?>
        <p><a class="button" href="<?= esc(url('/profile')) ?>">View your profile</a></p>
    <?php endif; ?>
</section>
<section class="features">
    <article><h2>Modern PHP</h2><p>Strict types, small focused classes, and a lightweight front controller.</p></article>
    <article><h2>Supabase Postgres</h2><p>PDO prepared statements, TLS, and SQL schema for Supabase SQL Editor.</p></article>
    <article><h2>Secure basics</h2><p>CSRF protection, escaped output, secure sessions, and password hashing.</p></article>
</section>
