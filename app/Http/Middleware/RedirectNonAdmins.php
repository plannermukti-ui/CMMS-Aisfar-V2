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
            $user = auth()->user();

            // Block inactive users
            if ($user->status !== 'active') {
                auth()->logout();

                return redirect()->route('filament.admin.auth.login')->withErrors([
                    'email' => 'Akun Anda belum aktif atau telah dinonaktifkan.',
                ]);
            }

            $allowedModules = $user->allowed_modules ?? [];

            // Check if user is trying to access admin panel (excluding login/logout/registration)
            if ($request->is('admin*') && ! $request->is('admin/login') && ! $request->is('admin/logout') && ! $request->is('admin/register')) {
                // Allow access if user has 'admin' in allowed_modules OR has admin role
                if (! in_array('admin', $allowedModules) && ! $user->hasRole('Super Admin') && ! $user->hasRole('admin') && ! $user->hasRole('Admin')) {
                    return redirect($this->getAllowedModuleUrl($allowedModules));
                }
            }
        }

        return $next($request);
    }

    /**
     * Get the first allowed module URL for the user.
     */
    protected function getAllowedModuleUrl(array $allowedModules): string
    {
        $moduleRoutes = [
            'plt' => '/plt/dashboard',
            'scm' => '/scm/dashboard',
        ];

        // Redirect to the first allowed module (skip admin)
        foreach ($moduleRoutes as $module => $url) {
            if (in_array($module, $allowedModules)) {
                return $url;
            }
        }

        return '/profile';
    }
}
