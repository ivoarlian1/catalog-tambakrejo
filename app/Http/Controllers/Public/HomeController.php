<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $countByType = Catalog::query()
            ->published()
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        $categories = Category::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $counts = $categories->mapWithKeys(fn (Category $category) => [
            $category->slug => (int) ($countByType[$category->slug] ?? 0),
        ]);

        $latestCatalogs = Catalog::query()
            ->published()
            ->with(['category', 'coverPhoto'])
            ->latest('id')
            ->take(8)
            ->get();

        return view('public.home', [
            'counts' => $counts,
            'categories' => $categories,
            'latestCatalogs' => $latestCatalogs,
        ]);
    }
}
