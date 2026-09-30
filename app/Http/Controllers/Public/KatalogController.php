<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Support\CatalogQrCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Catalog::query()->published()->with(['category', 'coverPhoto'])->orderBy('name')->orderBy('id');
        $this->applyFilters($query, $request);

        return view('public.katalog.index', [
            'catalogs' => $query->paginate(12)->withQueryString(),
            'typeLabels' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->all(),
            'subTypeOptions' => Catalog::subTypeOptions(),
            'currentType' => $request->string('type')->toString() ?: null,
            'currentTypeLabel' => null,
        ]);
    }

    public function byType(Request $request, string $type): View
    {
        $category = Category::query()->where('slug', $type)->firstOrFail();

        $query = Catalog::query()
            ->published()
            ->with(['category', 'coverPhoto'])
            ->ofType($type)
            ->orderBy('name')
            ->orderBy('id');

        $this->applyFilters($query, $request, $type);

        return view('public.katalog.index', [
            'catalogs' => $query->paginate(12)->withQueryString(),
            'typeLabels' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->all(),
            'subTypeOptions' => Catalog::subTypeOptions(),
            'currentType' => $type,
            'currentTypeLabel' => $category->name,
        ]);
    }

    public function show(string $type, string $slug, CatalogQrCode $qrCode): View
    {
        $catalog = Catalog::query()
            ->published()
            ->with(['category', 'coverPhoto', 'photos', 'activities' => fn ($query) => $query->latest('event_date')->latest('id')->limit(6)])
            ->where('type', $type)
            ->where('slug', $slug)
            ->firstOrFail();

        if ($catalog->type === 'umkm') {
            $catalog->load(['products' => fn ($query) => $query->orderBy('name')->orderBy('id')]);
        }

        return view('public.katalog.show', [
            'catalog' => $catalog,
            'qrSvg' => $qrCode->svg($catalog),
        ]);
    }

    public function qr(string $type, string $slug, CatalogQrCode $qrCode): StreamedResponse
    {
        $catalog = Catalog::query()
            ->published()
            ->where('type', $type)
            ->where('slug', $slug)
            ->firstOrFail();

        $jpeg = $qrCode->jpeg($catalog, 1000);

        return response()->streamDownload(
            function () use ($jpeg): void {
                echo $jpeg;
            },
            'qr-'.$catalog->slug.'.jpg',
            ['Content-Type' => 'image/jpeg'],
        );
    }

    private function applyFilters(Builder $query, Request $request, ?string $forcedType = null): void
    {
        $type = $forcedType ?? $request->string('type')->toString();

        if ($forcedType === null && $type !== '') {
            $query->ofType($type);
        }

        if ($request->filled('sub_type')) {
            $subType = $request->string('sub_type')->toString();
            $query->where('sub_type', $subType);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('sub_type', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
            });
        }
    }
}
