@extends('layouts.admin')
@section('title', 'Berita & Kegiatan')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-sm text-sea">{{ $catalog->name }}</p><h1 class="mt-1 text-2xl font-semibold">Berita & Kegiatan</h1></div><a href="{{ route('catalog-admin.activities.create') }}" class="inline-flex min-h-11 items-center rounded-md bg-sea px-4 font-semibold text-white">Tambah kegiatan</a></div>
<div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
@forelse ($activities as $activity)
<article class="overflow-hidden rounded-md border border-sky bg-white"><img src="{{ $activity->photo_url }}" alt="Foto {{ $activity->title }}" class="aspect-video w-full object-cover" width="640" height="360"><div class="p-4"><h2 class="font-semibold">{{ $activity->title }}</h2><p class="mt-1 text-sm text-sea">{{ $activity->event_date?->translatedFormat('d F Y') ?: 'Tanggal belum ditentukan' }}</p><p class="mt-3 line-clamp-3 text-sm">{{ $activity->description ?: 'Tidak ada deskripsi.' }}</p><div class="mt-4 flex gap-3"><a href="{{ route('catalog-admin.activities.edit', $activity) }}" class="text-sm font-semibold text-sea underline">Edit</a><form method="POST" action="{{ route('catalog-admin.activities.destroy', $activity) }}">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-700 underline">Hapus</button></form></div></div></article>
@empty
<p class="text-sea md:col-span-2 xl:col-span-3">Belum ada berita atau kegiatan yang tersedia.</p>
@endforelse
</div>
<div class="mt-6">{{ $activities->links() }}</div>
@endsection