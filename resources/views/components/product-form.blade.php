@php
    $editing = isset($product);
    $adminPanel = $adminPanel ?? false;
    $action = $adminPanel
        ? ($editing ? route('catalog-admin.products.update', $product) : route('catalog-admin.products.store'))
        : ($editing ? route('superadmin.catalogs.products.update', [$catalog, $product]) : route('superadmin.catalogs.products.store', $catalog));
@endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="max-w-2xl space-y-5">
    @csrf
    @if ($editing)<input type="hidden" name="_method" value="PUT">@endif
    <div><label for="name" class="mb-1 block font-medium">Nama produk</label><input id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="name" /></div>
    <div><label for="description" class="mb-1 block font-medium">Deskripsi</label><textarea id="description" name="description" rows="3" maxlength="2000" class="w-full rounded-md border border-sky px-3 py-2">{{ old('description', $product->description ?? '') }}</textarea><x-input-error field="description" /></div>
    <div><label for="price" class="mb-1 block font-medium">Harga (Rp)</label><input id="price" name="price" type="number" min="0" max="999999999" step="0.01" value="{{ old('price', $product->price ?? '') }}" required class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="price" /></div>
    <div><label for="photo" class="mb-1 block font-medium">Foto (JPEG, PNG, WebP; maks. 2MB)</label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block min-h-11 w-full rounded-md border border-sky p-2"><x-input-error field="photo" />@if ($editing && $product->photo)<img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }} saat ini" class="mt-3 h-24 w-36 rounded-md object-cover">@endif</div>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $product->is_available ?? true)) class="size-4"> Produk tersedia</label>
    <button class="min-h-11 rounded-md bg-sea px-5 font-semibold text-white">{{ $editing ? 'Simpan perubahan' : 'Tambah produk' }}</button>
</form>
