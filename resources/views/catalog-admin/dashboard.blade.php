@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold">Dashboard Katalog</h1>
@if (!$catalog)
    <div class="mt-5 border-l-4 border-amber-500 bg-white p-5" role="status"><h2 class="font-semibold">Belum ada katalog</h2><p class="mt-1 text-sm text-sea">Hubungi Superadmin untuk menetapkan katalog ke akun ini.</p></div>
@else
    <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <section class="overflow-hidden rounded-2xl border border-white/80 bg-white shadow-lg shadow-navy/5"><img src="{{ $catalog->photo_url }}" alt="Sampul {{ $catalog->name }}" class="aspect-[16/7] w-full object-cover" width="1200" height="525"><div class="p-5"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-sm text-sea">{{ $catalog->type_label }} · {{ $catalog->sub_type }}</p><h2 class="mt-1 text-2xl font-semibold">{{ $catalog->name }}</h2></div><span class="rounded-full bg-sky px-3 py-1 text-sm">{{ $catalog->status === 'published' ? 'Published' : 'Draf' }}</span></div><p class="mt-4">{{ $catalog->address }}</p><p class="mt-2 text-sm text-sea">{{ $catalog->contact_person_label }}: {{ $catalog->contact_person ?: 'Belum dicantumkan' }}</p><a href="{{ route('catalog-admin.catalog.edit') }}" class="mt-5 inline-flex min-h-11 items-center rounded-lg bg-sea px-4 text-white transition hover:-translate-y-0.5 hover:bg-navy">Ubah profil dan foto</a></div></section>
        <section aria-label="Ringkasan statistik" class="grid content-start gap-3">
            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/80 bg-white p-4 shadow-md shadow-navy/5">
                <p class="text-sm font-medium text-sea">Jumlah produk</p>
                <p class="text-2xl font-semibold">{{ $productCount }}</p>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/80 bg-white p-4 shadow-md shadow-navy/5">
                <p class="text-sm font-medium text-sea">Produk tersedia</p>
                <p class="text-2xl font-semibold">{{ $availableCount }}</p>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/80 bg-white p-4 shadow-md shadow-navy/5">
                <div>
                    <p class="text-sm font-medium text-sea">Berita & kegiatan</p>
                    <a href="{{ route('catalog-admin.activities.index') }}" class="mt-1 inline-block text-sm font-semibold text-sea underline decoration-sea/40 underline-offset-4 hover:decoration-sea">Kelola</a>
                </div>
                <p class="text-2xl font-semibold">{{ $activityCount }}</p>
            </div>
        </section>
    </div>
@endif
@endsection
