@extends('layouts.public')

@section('title', ($currentTypeLabel ?? 'Katalog') . ' | E-Catalog Kelurahan Tambakrejo')
@section('meta_description', $currentTypeLabel ? 'Cari '.$currentTypeLabel.' di Kelurahan Tambakrejo.' : 'Cari dan jelajahi usaha, layanan, dan fasilitas di Kelurahan Tambakrejo.')

@section('content')
<section class="relative isolate overflow-hidden border-b border-white/70 bg-gradient-to-br from-white/90 via-sky/60 to-cream">
    <div class="mx-auto max-w-6xl px-4 py-12 md:py-16">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Direktori Tambakrejo</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight md:text-4xl">{{ $currentTypeLabel ?? 'Jelajahi Katalog' }}</h1>
        <p class="mt-3 max-w-2xl text-sea">Temukan berbagai usaha dan layanan lokal. Gunakan pencarian atau pilih kategori untuk mulai menjelajah.</p>
        <form action="{{ $currentType ? route('katalog.bytype', $currentType) : route('katalog.index') }}" method="GET" class="mt-6 grid gap-3 rounded-2xl border border-white bg-white/70 p-3 shadow-lg shadow-navy/5 backdrop-blur-xl md:grid-cols-[1fr_220px_auto]">
            <label class="sr-only" for="search">Cari nama, alamat, atau deskripsi</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Nama, alamat, atau deskripsi" class="min-h-12 rounded-lg border border-sky bg-white px-4">
            <label class="sr-only" for="sub_type">Subkategori</label>
            <select id="sub_type" name="sub_type" class="min-h-12 rounded-lg border border-sky bg-white px-4">
                <option value="">Semua subkategori</option>
                @foreach (($currentType ? ($subTypeOptions[$currentType] ?? []) : collect($subTypeOptions)->flatten()->unique()->sort()->values()) as $option)
                    <option value="{{ $option }}" @selected(request('sub_type') === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button class="min-h-12 rounded-xl bg-sea px-6 font-semibold text-white transition hover:-translate-y-0.5 hover:bg-navy">Cari</button>
        </form>
        <nav aria-label="Filter kategori" class="mt-5 flex flex-wrap gap-2">
            <a class="rounded-full border border-sky px-4 py-2 text-sm transition hover:-translate-y-0.5 {{ !$currentType ? 'bg-navy text-white shadow-md' : 'bg-white/70 hover:bg-white' }}" href="{{ route('katalog.index') }}">Semua</a>
            @foreach ($typeLabels as $type => $label)
                <a class="rounded-full border border-sky px-4 py-2 text-sm transition hover:-translate-y-0.5 {{ $currentType === $type ? 'bg-navy text-white shadow-md' : 'bg-white/70 hover:bg-white' }}" href="{{ route('katalog.bytype', $type) }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-10">
    <p class="text-sm font-medium text-sea">{{ $catalogs->total() }} hasil ditemukan</p>
    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($catalogs as $catalog)
            @include('components.catalog-card', ['catalog' => $catalog])
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                @include('components.empty-state', ['title' => 'Katalog belum ditemukan.', 'description' => 'Ubah kata kunci atau filter untuk mencoba pencarian lain.'])
            </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $catalogs->links() }}</div>
</section>
@endsection
