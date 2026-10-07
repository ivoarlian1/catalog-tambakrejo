<?php

use App\Http\Controllers\CatalogAdmin\AuthController as CatalogAdminAuthController;
use App\Http\Controllers\CatalogAdmin\CatalogController as CatalogAdminCatalogController;
use App\Http\Controllers\CatalogAdmin\DashboardController as CatalogAdminDashboardController;
use App\Http\Controllers\CatalogAdmin\ProductController as CatalogAdminProductController;
use App\Http\Controllers\CatalogAdmin\ActivityController as CatalogAdminActivityController;
use App\Http\Controllers\CatalogPhotoController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KatalogController;
use App\Http\Controllers\Superadmin\AuthController as SuperadminAuthController;
use App\Http\Controllers\Superadmin\CategoryController as SuperadminCategoryController;
use App\Http\Controllers\Superadmin\CatalogAdminController;
use App\Http\Controllers\Superadmin\CatalogController as SuperadminCatalogController;
use App\Http\Controllers\Superadmin\ActivityController as SuperadminActivityController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\ProductController as SuperadminProductController;
use App\Http\Controllers\Superadmin\QrCodeController;
use App\Http\Controllers\Superadmin\SuperadminUserController;
use App\Http\Middleware\CatalogAdminMiddleware;
use App\Http\Middleware\SuperadminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('katalog')->name('katalog.')->group(function () {
    Route::get('/', [KatalogController::class, 'index'])->name('index');
    Route::get('/{type}', [KatalogController::class, 'byType'])
        ->where('type', '[a-z0-9_]+(?:-[a-z0-9_]+)*')
        ->name('bytype');
    Route::get('/{type}/{slug}', [KatalogController::class, 'show'])
        ->where('type', '[a-z0-9_]+(?:-[a-z0-9_]+)*')
        ->name('show');
    Route::get('/{type}/{slug}/qr', [KatalogController::class, 'qr'])
        ->where('type', '[a-z0-9_]+(?:-[a-z0-9_]+)*')
        ->name('qr');
});

Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', [SuperadminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [SuperadminAuthController::class, 'login'])
            ->middleware('throttle:login')
            ->name('login.post');
    });

    Route::post('/logout', [SuperadminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', SuperadminMiddleware::class])->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', SuperadminCategoryController::class)->except(['show']);

        Route::resource('catalogs', SuperadminCatalogController::class);
        Route::post('/catalogs/{catalog}/publish', [SuperadminCatalogController::class, 'publish'])->name('catalogs.publish');
        Route::post('/catalogs/{catalog}/unpublish', [SuperadminCatalogController::class, 'unpublish'])->name('catalogs.unpublish');

        Route::get('/catalogs/{catalog}/qr', [QrCodeController::class, 'show'])->name('catalogs.qr');
        Route::get('/catalogs/{catalog}/qr/download', [QrCodeController::class, 'download'])->name('catalogs.qr.download');
        Route::get('/catalogs/{catalog}/qr/jpg', [QrCodeController::class, 'downloadJpeg'])->name('catalogs.qr.jpg');

        Route::prefix('catalogs/{catalog}/photos')->name('catalogs.photos.')->scopeBindings()->group(function () {
            Route::post('/', [CatalogPhotoController::class, 'store'])->name('store');
            Route::patch('/{photo}', [CatalogPhotoController::class, 'updateType'])->name('update');
            Route::delete('/{photo}', [CatalogPhotoController::class, 'destroy'])->name('destroy');
        });

        Route::get('/products', [SuperadminProductController::class, 'all'])->name('products.index');

        Route::prefix('catalogs/{catalog}/products')->name('catalogs.products.')->scopeBindings()->group(function () {
            Route::get('/', [SuperadminProductController::class, 'index'])->name('index');
            Route::get('/create', [SuperadminProductController::class, 'create'])->name('create');
            Route::post('/', [SuperadminProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [SuperadminProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [SuperadminProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [SuperadminProductController::class, 'destroy'])->name('destroy');
        });

        Route::resource('catalogs.activities', SuperadminActivityController::class)->except(['show'])->scoped();

        Route::resource('admins', CatalogAdminController::class)->except(['show']);
        Route::post('/admins/{admin}/toggle-active', [CatalogAdminController::class, 'toggleActive'])->name('admins.toggle-active');

        Route::resource('superadmins', SuperadminUserController::class)->except(['show']);
        Route::post('/superadmins/{superadmin}/toggle-active', [SuperadminUserController::class, 'toggleActive'])
            ->name('superadmins.toggle-active');
    });
});

Route::prefix('catalog-admin')->name('catalog-admin.')->group(function () {
    Route::get('/', [CatalogAdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CatalogAdminAuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.post');

    Route::post('/logout', [CatalogAdminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', CatalogAdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [CatalogAdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/catalog', [CatalogAdminCatalogController::class, 'edit'])->name('catalog.edit');
        Route::put('/catalog', [CatalogAdminCatalogController::class, 'update'])->name('catalog.update');
        Route::post('/catalog/photos', [CatalogPhotoController::class, 'storeOwn'])->name('catalog.photos.store');
        Route::patch('/catalog/photos/{photo}', [CatalogPhotoController::class, 'updateOwnType'])->name('catalog.photos.update');
        Route::delete('/catalog/photos/{photo}', [CatalogPhotoController::class, 'destroyOwn'])->name('catalog.photos.destroy');

        Route::resource('products', CatalogAdminProductController::class)->except(['show']);
        Route::resource('activities', CatalogAdminActivityController::class)->except(['show']);
    });
});
