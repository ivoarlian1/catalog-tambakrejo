<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Catalog;
use App\Models\Product;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function all(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with('catalog')->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('name', 'like', '%'.$search.'%');
        }

        return view('superadmin.products.all', [
            'products' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function index(Catalog $catalog): View
    {
        $this->authorize('view', $catalog);
        abort_unless($catalog->type === 'umkm', 404);

        $products = $catalog->products()->latest('id')->paginate(20);

        return view('superadmin.products.index', compact('catalog', 'products'));
    }

    public function create(Catalog $catalog): View
    {
        $this->authorize('update', $catalog);
        abort_unless($catalog->type === 'umkm', 404);

        return view('superadmin.products.create', compact('catalog'));
    }

    public function store(StoreProductRequest $request, Catalog $catalog): RedirectResponse
    {
        $this->authorize('update', $catalog);
        abort_unless($catalog->type === 'umkm', 404);

        $data = $request->safe()->only(['name', 'description', 'price']);
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->images->store($request->file('photo'), 'products');
        }

        $catalog->products()->create($data);

        return redirect()
            ->route('superadmin.catalogs.products.index', $catalog)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Catalog $catalog, Product $product): View
    {
        $this->authorize('update', $product);

        return view('superadmin.products.edit', compact('catalog', 'product'));
    }

    public function update(UpdateProductRequest $request, Catalog $catalog, Product $product): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'description', 'price']);
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $this->images->delete($product->photo);
            $data['photo'] = $this->images->store($request->file('photo'), 'products');
        }

        $product->update($data);

        return redirect()
            ->route('superadmin.catalogs.products.index', $catalog)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Catalog $catalog, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->images->delete($product->photo);
        $product->delete();

        return redirect()
            ->route('superadmin.catalogs.products.index', $catalog)
            ->with('success', 'Produk berhasil dihapus.');
    }
}
