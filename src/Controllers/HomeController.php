<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class HomeController
{
    public function show(Request $request): Response
    {
        return Response::html(render('home', ['title' => config('APP_NAME', 'PHP Boilerplate'), 'description' => 'A reusable PHP 8.5.12 starter for modern server-rendered applications.', 'canonical' => url('/'), 'user' => null, 'authEnabled' => config_flag('AUTH_ENABLED', false)]));
    }

    public function health(Request $request): Response
    {
        return Response::json(['status' => 'ok', 'time' => gmdate(DATE_ATOM)]);
    }
}
