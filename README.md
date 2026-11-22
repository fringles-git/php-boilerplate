# PHP Boilerplate

A dependency-light PHP 8.5.12 starter with a small MVC structure, server-rendered templates, optional PostgreSQL authentication, and no Composer or Docker requirement.

## Requirements

- PHP 8.5.12 or newer
- `mbstring` for UTF-8 validation and text helpers
- Optional `pdo_pgsql` for PostgreSQL/Supabase authentication
- Node.js/npm only for building Tailwind CSS

```sh
cp .env.example .env
php tests/smoke.php
php -S 127.0.0.1:8000 -t public
```

## Architecture

- `public/index.php` is the single front controller.
- `src/bootstrap.php` provides the `App\\` autoloader, secure session setup, security headers and exception handling.
- `src/Http/Request.php` normalizes server input.
- `src/Http/Response.php` sends HTML, JSON or redirects.
- `src/Router.php` dispatches exact `GET` and `POST` routes.
- `src/Controllers/` contains thin controllers.
- `src/Models/` contains typed entities and readonly DTO-style values.
- `src/UserRepository.php` isolates SQL and remember-token persistence.
- `src/Support/` contains logger, pagination, text and date utilities.

## Reusable API

### Input

```php
$username = \App\Input::string('username', '', 'post');
$page = \App\Input::int('page', 1, 'get') ?? 1;

if (\App\Input::exists('post')) {
    // Handle a submitted form.
}
```

`Input::get()` does not sanitize by itself: validate values and escape them at the output boundary.

### Validation

```php
$validator = \App\Validation::check($_POST, [
    'username' => ['required' => true, 'username' => true],
    'email' => ['required' => true, 'email' => true],
    'password' => ['required' => true, 'password' => true, 'min_password' => 12],
    'confirm_password' => ['required' => true, 'match' => 'password'],
]);

if (!$validator->valid()) {
    foreach ($validator->errors() as $field => $message) {
        // Store or render the field-specific message.
    }
}
```

Supported rules include `required`, `optional`, `min`, `max`, `email`, `username`, `password`, `match`, and custom `label` text.

### Password and Hash

```php
$hash = \App\Password::hash('secret-passphrase');
$valid = \App\Password::verify('secret-passphrase', $hash);
$hash = \App\Password::rehashIfNeeded('secret-passphrase', $hash);

$random = \App\Hash::random(32);
$sha256 = \App\Hash::make('opaque value');
```

Use `Password` for credentials and `Hash` for tokens or non-password digests.

### Cookie

```php
\App\Cookie::put('preference', 'compact', time() + 86400);
$preference = \App\Cookie::get('preference');
\App\Cookie::delete('preference');
```

Cookies are HttpOnly and SameSite=Lax by default. `secure` follows the current HTTPS/proxy configuration.

### Redirect

```php
\App\Redirect::to('/profile');
\App\Redirect::external('https://example.com/login');
```

Internal redirects reject protocol-relative URLs, absolute URLs and backslashes. External redirects allow HTTPS URLs only.

### Remember me

Remember-me is opt-in and disabled by default. Enable account features and add the checkbox to the login form:

```html
<label><input type="checkbox" name="remember" value="1"> Remember me</label>
```

The implementation stores a 24-character selector and a 64-character verifier separately. Only `sha256(verifier)` is persisted; the raw verifier exists only in the HttpOnly cookie. The database table is `public.remember_tokens` and tokens expire after 30 days. Logout and account deletion clear matching tokens.

### Request, Response and Router

```php
$request = \App\Http\Request::fromGlobals();
$response = \App\Http\Response::json(['status' => 'ok']);
$response->send();
```

```php
$router = new \App\Router();
$router->get('/health', static fn (\App\Http\Request $request) =>
    \App\Http\Response::json(['status' => 'ok'])
);
```

## Persistence and security

PDO uses exception mode, associative fetches and native prepared statements. Mutations can be grouped with `Database::transaction()`. CSRF and authentication use secure session IDs and constant-time comparisons. Output uses `esc()` and HTML templates. Open redirects are blocked by `safe_redirect_path()` and `Redirect`.

## CLI and health endpoint

```sh
php bin/console help
php bin/console health
```

`GET /health` returns JSON. The CLI checks the application and, when authentication is enabled, PostgreSQL connectivity.

## Origin and license

This project is an independently maintained derivative of [simple-php-boilerplate](https://github.com/jandaryl/simple-php-boilerplate) by Jandaryl. The original README’s API ideas for Input, Redirect, Cookie, Hash, Password, Validation and authentication helpers informed this implementation. No third-party code has been copied without review, and the reusable implementation is adapted for PHP 8.5.12 and this lightweight MVC architecture.

Both projects are MIT licensed. The full license is in [LICENSE](LICENSE).
