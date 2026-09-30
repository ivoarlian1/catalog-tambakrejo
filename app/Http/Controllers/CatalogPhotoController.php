<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\CatalogPhoto;
use App\Support\CatalogPhotoManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CatalogPhotoController extends Controller
{
    public function __construct(private CatalogPhotoManager $photos) {}

    public function store(Request $request, Catalog $catalog): RedirectResponse
    {
        $this->authorize('update', $catalog);

        return $this->saveUploads($request, $catalog, 'superadmin.catalogs.edit', $catalog);
    }

    public function storeOwn(Request $request): RedirectResponse
    {
        $catalog = $this->ownCatalog($request);

        return $this->saveUploads($request, $catalog, 'catalog-admin.catalog.edit');
    }

    public function updateType(Request $request, Catalog $catalog, CatalogPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $catalog);
        abort_unless($photo->catalog_id === $catalog->id, 404);

        $data = $request->validate(['type' => ['required', Rule::in(['cover', 'location', 'gallery'])]]);
        $this->photos->updateType($catalog, $photo, $data['type']);

        return redirect()->route('superadmin.catalogs.edit', $catalog)->with('success', 'Jenis foto berhasil diperbarui.');
    }

    public function updateOwnType(Request $request, CatalogPhoto $photo): RedirectResponse
    {
        $catalog = $this->ownCatalog($request);
        abort_unless($photo->catalog_id === $catalog->id, 404);

        $data = $request->validate(['type' => ['required', Rule::in(['cover', 'location', 'gallery'])]]);
        $this->photos->updateType($catalog, $photo, $data['type']);

        return redirect()->route('catalog-admin.catalog.edit')->with('success', 'Jenis foto berhasil diperbarui.');
    }

    public function destroy(Catalog $catalog, CatalogPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $catalog);
        abort_unless($photo->catalog_id === $catalog->id, 404);

        $this->photos->delete($photo);

        return redirect()->route('superadmin.catalogs.edit', $catalog)->with('success', 'Foto berhasil dihapus.');
    }

    public function destroyOwn(Request $request, CatalogPhoto $photo): RedirectResponse
    {
        $catalog = $this->ownCatalog($request);
        abort_unless($photo->catalog_id === $catalog->id, 404);

        $this->photos->delete($photo);

        return redirect()->route('catalog-admin.catalog.edit')->with('success', 'Foto berhasil dihapus.');
    }

    private function saveUploads(Request $request, Catalog $catalog, string $route, mixed $routeParameter = null): RedirectResponse
    {
        $data = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'extensions:jpeg,jpg,png,webp', 'max:2048'],
            'type' => ['required', Rule::in(['cover', 'location', 'gallery'])],
        ]);

        if ($data['type'] === 'cover' && count($data['photos']) > 1) {
            throw ValidationException::withMessages([
                'photos' => 'Unggah satu foto saja saat memilih jenis foto utama.',
            ]);
        }

        $this->photos->store($catalog, $data['photos'], $data['type']);

        return $routeParameter === null
            ? redirect()->route($route)->with('success', 'Foto berhasil diunggah.')
            : redirect()->route($route, $routeParameter)->with('success', 'Foto berhasil diunggah.');
    }

    private function ownCatalog(Request $request): Catalog
    {
        $user = $request->user();
        abort_unless($user?->isCatalogAdmin() && $user->catalog_id !== null, 403);

        $catalog = Catalog::query()->findOrFail($user->catalog_id);
        $this->authorize('update', $catalog);

        return $catalog;
    }
}
