<?php

declare(strict_types=1);

namespace App;

use App\Http\Response;

final class Redirect
{
    public static function to(string $path, int $status = 303): never
    {
        $path = safe_redirect_path($path);
        Response::redirect(url($path), $status)->send();
    }

    public static function external(string $url, int $status = 303): never
    {
        if (!preg_match('/\Ahttps:\/\/[a-z0-9.-]+(?::[0-9]{1,5})?(?:\/[^\r\n]*)?\z/i', $url)) {
            throw new \InvalidArgumentException('Only absolute HTTPS URLs are allowed.');
        }
        Response::redirect($url, $status)->send();
    }
}
