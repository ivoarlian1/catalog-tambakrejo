@php
    $editing = isset($catalog);
    $adminProfile = !isset($typeLabels);
    $action = $adminProfile ? route('catalog-admin.catalog.update') : ($editing ? route('superadmin.catalogs.update', $catalog) : route('superadmin.catalogs.store'));
@endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5" data-catalog-form data-subtypes="{{ json_encode($subTypeOptions, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
    @csrf
    @if ($editing)<input type="hidden" name="_method" value="PUT">@endif
    @if ($adminProfile)<input type="hidden" name="type" value="{{ $catalog->type }}">@endif
    <div class="grid gap-5 md:grid-cols-2">
        <div><label for="name" class="mb-1 block font-medium">Nama katalog</label><input id="name" name="name" value="{{ old('name', $catalog->name ?? '') }}" required maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="name" /></div>
        @if (!$adminProfile)
            <div><label for="slug" class="mb-1 block font-medium">Slug URL</label><input id="slug" name="slug" value="{{ old('slug', $catalog->slug ?? '') }}" required pattern="[a-z0-9]+(?:-[a-z0-9]+)*" readonly class="min-h-11 w-full rounded-md border border-sky px-3"><p class="mt-1 text-xs text-sea">Slug dibuat dari nama saat katalog dibuat dan tetap stabil setelahnya.</p><x-input-error field="slug" /></div>
            <div><label for="type" class="mb-1 block font-medium">Kategori</label><select id="type" name="type" data-catalog-type required class="min-h-11 w-full rounded-md border border-sky px-3">@foreach ($typeLabels as $key => $label)<option value="{{ $key }}" @selected(old('type', $catalog->type ?? '') === $key)>{{ $label }}</option>@endforeach</select><x-input-error field="type" /></div>
        @endif
        <div><label for="sub_type" class="mb-1 block font-medium">Subkategori</label><input id="sub_type" name="sub_type" list="catalog-subcategories" value="{{ old('sub_type', $catalog->sub_type ?? '') }}" maxlength="100" required class="min-h-11 w-full rounded-md border border-sky px-3"><datalist id="catalog-subcategories" data-catalog-subtype>@foreach (($adminProfile ? ($subTypeOptions[$catalog->type] ?? []) : collect($subTypeOptions)->flatten()->unique()->sort()) as $option)<option value="{{ $option }}">@endforeach</datalist><x-input-error field="sub_type" /></div>
        @unless ($adminProfile)
            <div><label for="status" class="mb-1 block font-medium">Status</label><select id="status" name="status" required class="min-h-11 w-full rounded-md border border-sky px-3"><option value="draft" @selected(old('status', $catalog->status ?? 'draft') === 'draft')>Draf</option><option value="published" @selected(old('status', $catalog->status ?? '') === 'published')>Published</option></select><x-input-error field="status" /></div>
        @endunless
        <div data-owner-field class="{{ old('type', $catalog->type ?? '') === 'umkm' ? '' : 'hidden' }}"><label for="owner_name" class="mb-1 block font-medium">Nama pemilik (UMKM)</label><input id="owner_name" name="owner_name" value="{{ old('owner_name', $catalog->owner_name ?? '') }}" maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="owner_name" /></div>
        <div data-responsible-field class="{{ old('type', $catalog->type ?? '') !== '' && old('type', $catalog->type ?? '') !== 'umkm' ? '' : 'hidden' }}"><label for="responsible_person" class="mb-1 block font-medium">Penanggung jawab</label><input id="responsible_person" name="responsible_person" value="{{ old('responsible_person', $catalog->responsible_person ?? '') }}" maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="responsible_person" /></div>
        <div class="md:col-span-2"><label for="description" class="mb-1 block font-medium">Deskripsi</label><textarea id="description" name="description" rows="4" maxlength="5000" class="w-full rounded-md border border-sky px-3 py-2">{{ old('description', $catalog->description ?? '') }}</textarea><x-input-error field="description" /></div>
        <div class="md:col-span-2"><label for="address" class="mb-1 block font-medium">Alamat</label><textarea id="address" name="address" rows="2" required maxlength="500" class="w-full rounded-md border border-sky px-3 py-2">{{ old('address', $catalog->address ?? '') }}</textarea><x-input-error field="address" /></div>
        <div><label for="latitude" class="mb-1 block font-medium">Latitude</label><input id="latitude" name="latitude" type="number" step="any" min="-90" max="90" value="{{ old('latitude', $catalog->latitude ?? '') }}" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="latitude" /></div>
        <div><label for="longitude" class="mb-1 block font-medium">Longitude</label><input id="longitude" name="longitude" type="number" step="any" min="-180" max="180" value="{{ old('longitude', $catalog->longitude ?? '') }}" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="longitude" /></div>
        <div><label for="whatsapp" class="mb-1 block font-medium">WhatsApp</label><input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $catalog->whatsapp ?? '') }}" maxlength="20" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="whatsapp" /></div>
        <div><label for="phone" class="mb-1 block font-medium">Telepon</label><input id="phone" name="phone" value="{{ old('phone', $catalog->phone ?? '') }}" maxlength="20" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="phone" /></div>
        <div><label for="email" class="mb-1 block font-medium">Email</label><input id="email" name="email" type="email" value="{{ old('email', $catalog->email ?? '') }}" maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="email" /></div>
        <div><label for="instagram" class="mb-1 block font-medium">Instagram (URL atau nama akun)</label><input id="instagram" name="instagram" value="{{ old('instagram', $catalog->instagram ?? '') }}" maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="instagram" /></div>
        <div><label for="tiktok" class="mb-1 block font-medium">TikTok (URL atau nama akun)</label><input id="tiktok" name="tiktok" value="{{ old('tiktok', $catalog->tiktok ?? '') }}" maxlength="255" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="tiktok" /></div>
        <div><label for="other_contact" class="mb-1 block font-medium">Kontak lainnya</label><input id="other_contact" name="other_contact" value="{{ old('other_contact', $catalog->other_contact ?? '') }}" maxlength="500" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="other_contact" /></div>
    </div>
    <button class="min-h-11 rounded-md bg-sea px-5 font-semibold text-white">{{ $editing ? 'Simpan perubahan' : 'Buat katalog' }}</button>
</form>
@if ($editing)
    @include('components.catalog-photos', ['catalog' => $catalog, 'adminPanel' => $adminProfile])
@endif
@if (!$adminProfile)
    <script>
        const catalogForm = document.querySelector('[data-catalog-form]');
        const categoryField = catalogForm?.querySelector('[data-catalog-type]');
        const subcategoryList = catalogForm?.querySelector('[data-catalog-subtype]');
        const nameField = catalogForm?.querySelector('[name="name"]');
        const slugField = catalogForm?.querySelector('[name="slug"]');
        const ownerField = catalogForm?.querySelector('[data-owner-field]');
        const responsibleField = catalogForm?.querySelector('[data-responsible-field]');

        const updateContactFields = () => {
            const type = categoryField?.value || catalogForm?.querySelector('[name="type"]')?.value || '';
            const isUmkm = type === 'umkm';

            ownerField?.classList.toggle('hidden', !isUmkm);
            responsibleField?.classList.toggle('hidden', !type || isUmkm);
            ownerField?.querySelector('input')?.toggleAttribute('required', isUmkm);
            responsibleField?.querySelector('input')?.toggleAttribute('required', Boolean(type) && !isUmkm);
        };

        const updateSlug = () => {
            if (!catalogForm?.querySelector('[name="_method"]') && nameField && slugField) {
                slugField.value = nameField.value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        };

        const updateSubcategories = () => {
            const options = JSON.parse(catalogForm.dataset.subtypes)[categoryField.value] ?? [];
            subcategoryList.replaceChildren(...options.map((label) => new Option(label, label)));
        };

        categoryField?.addEventListener('change', () => {
            updateSubcategories();
            updateContactFields();
        });

        updateSubcategories();
        nameField?.addEventListener('input', updateSlug);
        updateContactFields();
        updateSlug();
    </script>
@endif
