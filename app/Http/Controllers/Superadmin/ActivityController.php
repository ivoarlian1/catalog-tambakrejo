<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCatalogActivityRequest;
use App\Http\Requests\UpdateCatalogActivityRequest;
use App\Models\Catalog;
use App\Models\CatalogActivity;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Catalog $catalog): View
    {
        $this->authorize('view', $catalog);
        return view('superadmin.activities.index', ['catalog' => $catalog, 'activities' => $catalog->activities()->latest('event_date')->latest('id')->paginate(12)]);
    }

    public function create(Catalog $catalog): View
    {
        $this->authorize('update', $catalog);
        return view('superadmin.activities.create', compact('catalog'));
    }

    public function store(StoreCatalogActivityRequest $request, Catalog $catalog): RedirectResponse
    {
        $this->authorize('update', $catalog);
        $data = $request->safe()->only(['title', 'description', 'event_date']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->images->store($request->file('photo'), 'activities/'.$catalog->id);
        }
        $catalog->activities()->create($data);
        return redirect()->route('superadmin.catalogs.activities.index', $catalog)->with('success', 'Berita atau kegiatan berhasil ditambahkan.');
    }

    public function edit(Catalog $catalog, CatalogActivity $activity): View
    {
        $this->authorize('update', $activity);
        abort_unless($activity->catalog_id === $catalog->id, 404);
        return view('superadmin.activities.edit', compact('catalog', 'activity'));
    }

    public function update(UpdateCatalogActivityRequest $request, Catalog $catalog, CatalogActivity $activity): RedirectResponse
    {
        $this->authorize('update', $activity);
        abort_unless($activity->catalog_id === $catalog->id, 404);
        $data = $request->safe()->only(['title', 'description', 'event_date']);
        if ($request->hasFile('photo')) {
            $this->images->delete($activity->photo);
            $data['photo'] = $this->images->store($request->file('photo'), 'activities/'.$catalog->id);
        }
        $activity->update($data);
        return redirect()->route('superadmin.catalogs.activities.index', $catalog)->with('success', 'Berita atau kegiatan berhasil diperbarui.');
    }

    public function destroy(Catalog $catalog, CatalogActivity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);
        abort_unless($activity->catalog_id === $catalog->id, 404);
        $this->images->delete($activity->photo);
        $activity->delete();
        return redirect()->route('superadmin.catalogs.activities.index', $catalog)->with('success', 'Berita atau kegiatan berhasil dihapus.');
    }
}