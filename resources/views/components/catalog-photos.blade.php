@php
    $adminPanel = $adminPanel ?? false;
    $uploadAction = $adminPanel ? route('catalog-admin.catalog.photos.store') : route('superadmin.catalogs.photos.store', $catalog);
    $typeRoute = fn ($photo) => $adminPanel
        ? route('catalog-admin.catalog.photos.update', $photo)
        : route('superadmin.catalogs.photos.update', [$catalog, $photo]);
    $deleteRoute = fn ($photo) => $adminPanel
        ? route('catalog-admin.catalog.photos.destroy', $photo)
        : route('superadmin.catalogs.photos.destroy', [$catalog, $photo]);
@endphp
<section class="mt-8 border-t border-sky pt-6">
    <div><h2 class="text-xl font-semibold">Foto katalog</h2><p class="mt-1 text-sm text-sea">Foto utama menjadi sampul. Foto lokasi dan tambahan tampil di galeri.</p></div>
    @if ($catalog->photos->isNotEmpty())
        <ul class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($catalog->photos as $photo)
                <li class="overflow-hidden rounded-md border border-sky bg-white">
                    <img src="{{ $photo->url }}" alt="{{ $photo->type_label }} katalog {{ $catalog->name }}" class="aspect-[4/3] w-full object-cover" loading="lazy" width="800" height="600">
                    <div class="space-y-3 p-3">
                        <form method="POST" action="{{ $typeRoute($photo) }}" class="flex items-end gap-2">
                            @csrf @method('PATCH')
                            <div class="min-w-0 flex-1"><label for="photo-type-{{ $photo->id }}" class="mb-1 block text-sm font-medium">Jenis foto</label><select id="photo-type-{{ $photo->id }}" name="type" class="min-h-10 w-full rounded-md border border-sky px-2"><option value="cover" @selected($photo->type === 'cover')>Foto utama / cover</option><option value="location" @selected($photo->type === 'location')>Foto lokasi</option><option value="gallery" @selected($photo->type === 'gallery')>Foto tambahan</option></select></div>
                            <button class="min-h-10 shrink-0 rounded-md border border-sea px-3 text-sm text-sea">Simpan</button>
                        </form>
                        <form method="POST" action="{{ $deleteRoute($photo) }}" onsubmit="return confirm('Hapus foto ini?')">
                            @csrf @method('DELETE')
                            <button class="min-h-10 text-sm text-red-800 underline">Hapus foto</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @elseif ($catalog->photo)
        <div class="mt-4 max-w-xs overflow-hidden rounded-md border border-sky bg-white">
            <img src="{{ $catalog->photo_url }}" alt="Foto lama {{ $catalog->name }}" class="aspect-[4/3] w-full object-cover" width="800" height="600">
            <p class="p-3 text-sm text-sea">Foto lama tetap ditampilkan sebagai sampul sampai foto baru dipilih.</p>
        </div>
    @else
        <p class="mt-4 text-sm text-sea">Belum ada foto. Sampul akan menggunakan gambar fallback.</p>
    @endif
    <form method="POST" action="{{ $uploadAction }}" enctype="multipart/form-data" class="mt-5 grid gap-3 rounded-md border border-sky bg-white p-4 sm:grid-cols-[1fr_220px_auto] sm:items-end">
        @csrf
        <div><label for="catalog-photos" class="mb-1 block font-medium">Tambah foto (maks. 10 file, 2 MB per foto)</label><input id="catalog-photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required class="block min-h-11 w-full rounded-md border border-sky p-2"><x-input-error field="photos" />@foreach ($errors->get('photos.*') as $messages)@foreach ($messages as $message)<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@endforeach @endforeach</div>
        <div><label for="photo-type" class="mb-1 block font-medium">Jenis foto</label><select id="photo-type" name="type" required class="min-h-11 w-full rounded-md border border-sky px-3"><option value="gallery">Foto tambahan</option><option value="location">Foto lokasi</option><option value="cover">Foto utama / cover (satu file)</option></select><x-input-error field="type" /></div>
        <button class="min-h-11 rounded-md bg-sea px-5 font-semibold text-white">Unggah foto</button>
    </form>
</section>
