<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isSuperadmin()) {
            abort(403, 'Anda tidak memiliki akses ke area ini.');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('superadmin.login')->withErrors([
                'email' => 'Akun Anda sedang tidak aktif.',
            ]);
        }

        return $next($request);
    }
}
