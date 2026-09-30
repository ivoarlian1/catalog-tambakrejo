<?php

namespace App\Http\Controllers\CatalogAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->isSuperadmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if (Auth::check() && Auth::user()?->isCatalogAdmin()) {
            return redirect()->route('catalog-admin.dashboard');
        }

        return view('catalog-admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check() && Auth::user()?->isSuperadmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if (Auth::check() && Auth::user()?->isCatalogAdmin()) {
            return redirect()->route('catalog-admin.dashboard');
        }

        $credentials = $request->validate(
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'Email atau user ID wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ],
        );

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        $user = Auth::user();

        if ($user === null || ! $user->isCatalogAdmin()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        if (! $user->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('catalog-admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog-admin.login');
    }
}
