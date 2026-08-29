<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonAdmins
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Block inactive users
            if (auth()->user()->status !== 'active') {
                auth()->logout();

                return redirect()->route('filament.admin.auth.login')->withErrors([
                    'email' => 'Akun Anda belum aktif atau telah dinonaktifkan.',
                ]);
            }

            // Check if user is trying to access admin panel (excluding login/logout/registration)
            if ($request->is('admin*') && ! $request->is('admin/login') && ! $request->is('admin/logout') && ! $request->is('admin/register')) {
                // If not Super Admin or admin, redirect to PLANT dashboard
                if (! auth()->user()->hasRole('Super Admin') && ! auth()->user()->hasRole('admin') && ! auth()->user()->hasRole('Admin')) {
                    return redirect()->route('plt.dashboard');
                }
            }
        }

        return $next($request);
    }
}
