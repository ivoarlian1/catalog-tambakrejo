<?php

namespace App\Http\Controllers\CatalogAdmin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $catalog = $user?->catalog;

        if ($catalog === null) {
            return view('catalog-admin.dashboard', [
                'catalog' => null,
                'productCount' => 0,
                'availableCount' => 0,
                'activityCount' => 0,
            ]);
        }

        $this->authorize('view', $catalog);
        $catalog->load(['category', 'coverPhoto']);

        $productCount = $catalog->products()->count();
        $availableCount = $catalog->products()->where('is_available', true)->count();
        $activityCount = $catalog->activities()->count();

        return view('catalog-admin.dashboard', compact('catalog', 'productCount', 'availableCount', 'activityCount'));
    }
}
