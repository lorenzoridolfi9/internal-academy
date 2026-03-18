<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isEmployee()) {
            abort(403, 'Accesso non autorizzato.');
        }

        return $next($request);
    }
}
