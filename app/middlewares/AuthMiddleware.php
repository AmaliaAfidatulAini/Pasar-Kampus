<?php

namespace App\Middlewares;

use Core\Request;
use Closure;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session())
            redirect('/login');

        return $next($request);
    }
}
