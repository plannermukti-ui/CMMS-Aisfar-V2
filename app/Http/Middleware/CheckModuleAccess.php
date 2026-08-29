<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        // If no user is logged in, let authentication middleware handle it
        if (! $user) {
            return $next($request);
        }

        $allowedModules = $user->allowed_modules ?? [];

        if (! in_array($module, $allowedModules)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses modul '.strtoupper($module).'.');
        }

        return $next($request);
    }
}
