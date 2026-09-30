<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $route = $user->isSuperadmin() ? 'superadmin.login' : 'catalog-admin.login';

            return redirect()->route($route)->withErrors([
                'email' => 'Akun Anda sedang tidak aktif. Hubungi administrator.',
            ]);
        }

        return $next($request);
    }
}
