@props(['action', 'activity' => null])
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if ($activity)<input type="hidden" name="_method" value="PUT">@endif
    <div><label for="title" class="mb-1 block font-medium">Judul kegiatan</label><input id="title" name="title" value="{{ old('title', $activity?->title) }}" required class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="title" /></div>
    <div><label for="event_date" class="mb-1 block font-medium">Tanggal kegiatan</label><input id="event_date" name="event_date" type="date" value="{{ old('event_date', $activity?->event_date?->format('Y-m-d')) }}" class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="event_date" /></div>
    <div><label for="description" class="mb-1 block font-medium">Deskripsi</label><textarea id="description" name="description" rows="6" class="w-full rounded-md border border-sky px-3 py-2">{{ old('description', $activity?->description) }}</textarea><x-input-error field="description" /></div>
    <div><label for="photo" class="mb-1 block font-medium">Foto dokumentasi</label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block min-h-11 w-full rounded-md border border-sky px-3 py-2"><p class="mt-1 text-xs text-sea">JPG, PNG, atau WEBP, maksimal 2 MB.</p><x-input-error field="photo" /></div>
    @if ($activity?->photo)<img src="{{ $activity->photo_url }}" alt="Foto {{ $activity->title }}" class="mt-2 aspect-video max-w-sm rounded-md object-cover" width="640" height="360">@endif
    <button type="submit" class="min-h-11 rounded-md bg-sea px-5 font-semibold text-white">{{ $activity ? 'Simpan perubahan' : 'Simpan kegiatan' }}</button>
</form>