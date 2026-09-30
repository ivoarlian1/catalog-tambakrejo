@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold">Dashboard Katalog</h1>
@if (!$catalog)
    <div class="mt-5 border-l-4 border-amber-500 bg-white p-5" role="status"><h2 class="font-semibold">Belum ada katalog</h2><p class="mt-1 text-sm text-sea">Hubungi Superadmin untuk menetapkan katalog ke akun ini.</p></div>
@else
    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_280px]">
        <section class="overflow-hidden rounded-md border border-sky bg-white"><img src="{{ $catalog->photo_url }}" alt="Sampul {{ $catalog->name }}" class="aspect-[16/7] w-full object-cover" width="1200" height="525"><div class="p-5"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-sm text-sea">{{ $catalog->type_label }} · {{ $catalog->sub_type }}</p><h2 class="mt-1 text-2xl font-semibold">{{ $catalog->name }}</h2></div><span class="rounded-full bg-sky px-3 py-1 text-sm">{{ $catalog->status === 'published' ? 'Published' : 'Draf' }}</span></div><p class="mt-4">{{ $catalog->address }}</p><p class="mt-2 text-sm text-sea">{{ $catalog->contact_person_label }}: {{ $catalog->contact_person ?: 'Belum dicantumkan' }}</p><a href="{{ route('catalog-admin.catalog.edit') }}" class="mt-5 inline-flex min-h-11 items-center rounded-md bg-sea px-4 text-white">Ubah profil dan foto</a></div></section>
        <section class="grid gap-3 sm:grid-cols-3"><div class="rounded-md border border-sky bg-white p-4"><p class="text-sm text-sea">Jumlah produk</p><p class="mt-1 text-2xl font-semibold">{{ $productCount }}</p></div><div class="rounded-md border border-sky bg-white p-4"><p class="text-sm text-sea">Tersedia</p><p class="mt-1 text-2xl font-semibold">{{ $availableCount }}</p></div><div class="rounded-md border border-sky bg-white p-4"><p class="text-sm text-sea">Berita & kegiatan</p><p class="mt-1 text-2xl font-semibold">{{ $activityCount }}</p><a href="{{ route('catalog-admin.activities.index') }}" class="mt-2 inline-block text-sm font-semibold text-sea underline">Kelola</a></div></section>
    </div>
@endif
@endsection
