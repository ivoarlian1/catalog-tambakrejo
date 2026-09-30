<?php

use App\Http\Middleware\CatalogAdminMiddleware;
use App\Http\Middleware\SuperadminMiddleware;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            RateLimiter::for('login', function (Request $request) {
                $email = Str::transliterate(Str::lower($request->string('email')->toString()));

                return Limit::perMinute(5)->by($email.'|'.$request->ip());
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'superadmin' => SuperadminMiddleware::class,
            'catalog-admin' => CatalogAdminMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('superadmin') || $request->is('superadmin/*')) {
                return route('superadmin.login');
            }

            if ($request->is('catalog-admin') || $request->is('catalog-admin/*')) {
                return route('catalog-admin.login');
            }

            return route('home');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            /** @var User|null $user */
            $user = $request->user();

            if ($user?->isSuperadmin()) {
                return route('superadmin.dashboard');
            }

            if ($user?->isCatalogAdmin()) {
                return route('catalog-admin.dashboard');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            $status = $exception->getStatusCode();

            if (in_array($status, [403, 404, 419, 422, 429, 500], true) && ! $request->expectsJson()) {
                if (view()->exists('errors.'.$status)) {
                    return response()->view('errors.'.$status, [
                        'message' => $exception->getMessage(),
                    ], $status);
                }
            }

            return null;
        });
    })->create();
