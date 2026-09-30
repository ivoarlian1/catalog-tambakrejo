<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatalogAdminMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isCatalogAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke area ini.');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('catalog-admin.login')->withErrors([
                'email' => 'Akun Anda sedang tidak aktif. Hubungi administrator.',
            ]);
        }

        return $next($request);
    }
}
