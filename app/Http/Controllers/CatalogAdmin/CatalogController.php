<?php

namespace App\Http\Controllers\CatalogAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOwnCatalogRequest;
use App\Models\Catalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function edit(Request $request): View
    {
        $catalog = $this->ownCatalog($request);
        $catalog->load(['category', 'photos', 'coverPhoto']);

        return view('catalog-admin.catalog.edit', [
            'catalog' => $catalog,
            'subTypeOptions' => Catalog::subTypeOptions(),
        ]);
    }

    public function update(UpdateOwnCatalogRequest $request): RedirectResponse
    {
        $catalog = $this->ownCatalog($request);

        $data = $request->safe()->except(['type']);

        $catalog->update($data);
        $catalog->rememberSubcategory();

        return redirect()
            ->route('catalog-admin.catalog.edit')
            ->with('success', 'Profil katalog berhasil diperbarui.');
    }

    private function ownCatalog(Request $request): Catalog
    {
        $user = $request->user();

        if ($user === null || $user->catalog_id === null) {
            abort(403, 'Anda belum memiliki katalog yang ditetapkan.');
        }

        $catalog = Catalog::query()->findOrFail($user->catalog_id);

        $this->authorize('update', $catalog);

        return $catalog;
    }
}
