<?php

namespace App\Http\Controllers\CatalogAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCatalogActivityRequest;
use App\Http\Requests\UpdateCatalogActivityRequest;
use App\Models\CatalogActivity;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index(Request $request): View
    {
        $catalog = $request->user()->catalog;
        abort_unless($catalog !== null, 403);
        $this->authorize('view', $catalog);

        return view('catalog-admin.activities.index', [
            'catalog' => $catalog,
            'activities' => $catalog->activities()->latest('event_date')->latest('id')->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        $catalog = $request->user()->catalog;
        abort_unless($catalog !== null, 403);
        return view('catalog-admin.activities.create', compact('catalog'));
    }

    public function store(StoreCatalogActivityRequest $request): RedirectResponse
    {
        $catalog = $request->user()->catalog;
        abort_unless($catalog !== null, 403);
        $data = $request->safe()->only(['title', 'description', 'event_date']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->images->store($request->file('photo'), 'activities/'.$catalog->id);
        }
        $catalog->activities()->create($data);
        return redirect()->route('catalog-admin.activities.index')->with('success', 'Berita atau kegiatan berhasil ditambahkan.');
    }

    public function edit(CatalogActivity $activity): View
    {
        $this->authorize('update', $activity);
        return view('catalog-admin.activities.edit', compact('activity'));
    }

    public function update(UpdateCatalogActivityRequest $request, CatalogActivity $activity): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'description', 'event_date']);
        if ($request->hasFile('photo')) {
            $this->images->delete($activity->photo);
            $data['photo'] = $this->images->store($request->file('photo'), 'activities/'.$activity->catalog_id);
        }
        $activity->update($data);
        return redirect()->route('catalog-admin.activities.index')->with('success', 'Berita atau kegiatan berhasil diperbarui.');
    }

    public function destroy(CatalogActivity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);
        $this->images->delete($activity->photo);
        $activity->delete();
        return redirect()->route('catalog-admin.activities.index')->with('success', 'Berita atau kegiatan berhasil dihapus.');
    }
}