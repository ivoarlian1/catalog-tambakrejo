<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'published' => Catalog::query()->where('status', 'published')->count(),
            'draft' => Catalog::query()->where('status', 'draft')->count(),
            'total_catalogs' => Catalog::query()->count(),
            'total_products' => Product::query()->count(),
            'total_admins' => User::query()->where('role', 'catalog_admin')->count(),
            'active_admins' => User::query()->where('role', 'catalog_admin')->where('is_active', true)->count(),
        ];

        $latestCatalogs = Catalog::query()
            ->with(['admin', 'category', 'coverPhoto'])
            ->latest('id')
            ->take(5)
            ->get();

        $categoryStats = Category::query()
            ->withCount('catalogs')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('superadmin.dashboard', compact('stats', 'latestCatalogs', 'categoryStats'));
    }
}
