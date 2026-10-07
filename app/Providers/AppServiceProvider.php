<?php

namespace App\Providers;

use App\Models\Catalog;
use App\Models\CatalogActivity;
use App\Models\Category;
use App\Models\Product;
use App\Policies\CatalogPolicy;
use App\Policies\CatalogActivityPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Catalog::class, CatalogPolicy::class);
        Gate::policy(CatalogActivity::class, CatalogActivityPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        Password::defaults(fn (): Password => Password::min(8)->letters()->numbers());

        RateLimiter::for('login', function (Request $request) {
            $email = Str::transliterate(Str::lower($request->string('email')->toString()));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        View::composer('layouts.admin', function ($view): void {
            $user = auth()->user();

            if ($user?->isSuperadmin()) {
                $view->with([
                    'panelTitle' => 'Superadmin',
                    'logoutUrl' => route('superadmin.logout'),
                    'menu' => [
                        ['label' => 'Dashboard', 'url' => route('superadmin.dashboard'), 'active' => 'superadmin.dashboard'],
                        ['label' => 'Kategori', 'url' => route('superadmin.categories.index'), 'active' => 'superadmin.categories.*'],
                        ['label' => 'Katalog', 'url' => route('superadmin.catalogs.index'), 'active' => 'superadmin.catalogs.*'],
                        ['label' => 'Catalog Admin', 'url' => route('superadmin.admins.index'), 'active' => 'superadmin.admins.*'],
                        ['label' => 'Akun Superadmin', 'url' => route('superadmin.superadmins.index'), 'active' => 'superadmin.superadmins.*'],
                        ['label' => 'Produk', 'url' => route('superadmin.products.index'), 'active' => 'superadmin.products.*'],
                    ],
                ]);
            }

            if ($user?->isCatalogAdmin()) {
                $menu = [
                    ['label' => 'Dashboard', 'url' => route('catalog-admin.dashboard'), 'active' => 'catalog-admin.dashboard'],
                    ['label' => 'Profil Katalog', 'url' => route('catalog-admin.catalog.edit'), 'active' => 'catalog-admin.catalog.*'],
                ];

                if ($user->catalog?->type === 'umkm') {
                    $menu[] = ['label' => 'Produk', 'url' => route('catalog-admin.products.index'), 'active' => 'catalog-admin.products.*'];
                }

                $menu[] = ['label' => 'Berita & Kegiatan', 'url' => route('catalog-admin.activities.index'), 'active' => 'catalog-admin.activities.*'];

                $view->with([
                    'panelTitle' => 'Catalog Admin',
                    'logoutUrl' => route('catalog-admin.logout'),
                    'menu' => $menu,
                ]);
            }
        });

        View::composer('layouts.public', function ($view): void {
            $view->with('publicCategories', Category::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get());
        });
    }
}
