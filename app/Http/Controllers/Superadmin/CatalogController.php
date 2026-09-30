<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCatalogRequest;
use App\Http\Requests\UpdateCatalogRequest;
use App\Models\Catalog;
use App\Models\Category;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Catalog::class);

        $query = Catalog::query()->with(['admin', 'category', 'coverPhoto'])->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('type') && in_array($request->string('type')->toString(), Catalog::types(), true)) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('status') && in_array($request->string('status')->toString(), ['draft', 'published'], true)) {
            $query->where('status', $request->string('status')->toString());
        }

        return view('superadmin.catalogs.index', [
            'catalogs' => $query->paginate(15)->withQueryString(),
            'typeLabels' => Catalog::typeLabels(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Catalog::class);

        return view('superadmin.catalogs.create', [
            'typeLabels' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->all(),
            'subTypeOptions' => Catalog::subTypeOptions(),
        ]);
    }

    public function store(StoreCatalogRequest $request): RedirectResponse
    {
        $data = $request->safe()->all();

        $catalog = Catalog::query()->create($data);
        $catalog->rememberSubcategory();

        return redirect()
            ->route('superadmin.catalogs.show', $catalog)
            ->with('success', 'Katalog berhasil dibuat.');
    }

    public function show(Catalog $catalog): View
    {
        $this->authorize('view', $catalog);

        $catalog->load(['admin', 'products', 'category', 'photos', 'coverPhoto']);

        return view('superadmin.catalogs.show', compact('catalog'));
    }

    public function edit(Catalog $catalog): View
    {
        $this->authorize('update', $catalog);
        $catalog->load(['photos', 'coverPhoto']);

        $categories = Category::query()
            ->where('is_active', true)
            ->orWhere('slug', $catalog->type)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('superadmin.catalogs.edit', [
            'catalog' => $catalog,
            'typeLabels' => $categories->pluck('name', 'slug')->all(),
            'subTypeOptions' => Catalog::subTypeOptions(),
        ]);
    }

    public function update(UpdateCatalogRequest $request, Catalog $catalog): RedirectResponse
    {
        $data = $request->safe()->except(['slug']);

        $catalog->update($data);
        $catalog->rememberSubcategory();

        return redirect()
            ->route('superadmin.catalogs.show', $catalog)
            ->with('success', 'Katalog berhasil diperbarui.');
    }

    public function destroy(Catalog $catalog): RedirectResponse
    {
        $this->authorize('delete', $catalog);

        foreach ($catalog->photos as $photo) {
            $this->images->delete($photo->file_path);
        }

        $this->images->delete($catalog->photo);
        $catalog->delete();

        return redirect()
            ->route('superadmin.catalogs.index')
            ->with('success', 'Katalog berhasil dihapus.');
    }

    public function publish(Catalog $catalog): RedirectResponse
    {
        $this->authorize('publish', $catalog);

        $catalog->update(['status' => 'published']);

        return back()->with('success', 'Katalog berhasil dipublikasikan.');
    }

    public function unpublish(Catalog $catalog): RedirectResponse
    {
        $this->authorize('publish', $catalog);

        $catalog->update(['status' => 'draft']);

        return back()->with('success', 'Katalog dijadikan draf.');
    }
}
