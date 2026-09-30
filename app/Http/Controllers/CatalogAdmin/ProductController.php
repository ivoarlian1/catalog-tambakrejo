<?php

namespace App\Http\Controllers\CatalogAdmin;

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

    public function index(Request $request): View
    {
        $catalog = $this->ownCatalog($request);
        abort_unless($catalog->type === 'umkm', 403, 'Fitur produk hanya tersedia untuk UMKM.');

        $products = Product::query()
            ->where('catalog_id', $catalog->id)
            ->latest('id')
            ->paginate(20);

        return view('catalog-admin.products.index', compact('products', 'catalog'));
    }

    public function create(Request $request): View
    {
        $catalog = $this->ownCatalog($request);
        abort_unless($catalog->type === 'umkm', 403, 'Fitur produk hanya tersedia untuk UMKM.');
        $this->authorize('create', Product::class);

        return view('catalog-admin.products.create', compact('catalog'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $catalog = $this->ownCatalog($request);
        abort_unless($catalog->type === 'umkm', 403);

        $data = $request->safe()->only(['name', 'description', 'price']);
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->images->store($request->file('photo'), 'products');
        }

        $catalog->products()->create($data);

        return redirect()
            ->route('catalog-admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorize('update', $product);

        return view('catalog-admin.products.edit', [
            'product' => $product,
            'catalog' => $this->ownCatalog($request),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'description', 'price']);
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $this->images->delete($product->photo);
            $data['photo'] = $this->images->store($request->file('photo'), 'products');
        }

        $product->update($data);

        return redirect()
            ->route('catalog-admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->images->delete($product->photo);
        $product->delete();

        return redirect()
            ->route('catalog-admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function ownCatalog(Request $request): Catalog
    {
        $user = $request->user();

        if ($user === null || $user->catalog_id === null) {
            abort(403, 'Anda belum memiliki katalog yang ditetapkan.');
        }

        $catalog = Catalog::query()->findOrFail($user->catalog_id);
        $this->authorize('view', $catalog);

        return $catalog;
    }
}
