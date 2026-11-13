<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(in_array($request->user()?->role, ['superadmin', 'accounting'], true), 403, 'Buku kas hanya dapat diakses administrator dan accounting.');

        return $next($request);
    }
}
