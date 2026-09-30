@extends('layouts.public')

@section('title', 'E-Catalog Kelurahan Tambakrejo')
@section('meta_description', 'Direktori digital usaha, layanan, dan fasilitas di Kelurahan Tambakrejo.')
@section('og_title', 'E-Catalog Kelurahan Tambakrejo')

@section('content')
<section class="bg-navy text-white">
    <div class="mx-auto max-w-6xl px-4 py-16">
        <h1 class="text-3xl font-semibold md:text-5xl">E-Catalog Kelurahan Tambakrejo</h1>
        <p class="mt-4 max-w-2xl text-sky">Temukan usaha, layanan, dan fasilitas di Kelurahan Tambakrejo melalui satu direktori yang mudah dicari dan nyaman dibuka dari ponsel.</p>
        <form action="{{ route('katalog.index') }}" method="GET" class="mt-8 flex max-w-xl flex-col gap-3 sm:flex-row">
            <label for="search" class="sr-only">Cari katalog</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Cari katalog..." class="min-h-12 w-full rounded-lg border-0 bg-white px-4 text-navy">
            <button type="submit" class="min-h-12 rounded-lg bg-teal px-6">Cari</button>
        </form>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12">
    <h2 class="text-2xl font-semibold">Kategori</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($categories as $category)
            @php($type = $category->slug)
            <a href="{{ route('katalog.bytype', $type) }}" class="rounded-md border border-sky bg-white p-5 hover:bg-sky">
                <p class="text-lg font-semibold">{{ $category->name }}</p>
                <p class="mt-2 text-sm text-sea">{{ $counts[$type] }} katalog</p>
            </a>
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <h2 class="text-2xl font-semibold">Katalog Terbaru</h2>
    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($latestCatalogs as $catalog)
            @include('components.catalog-card', ['catalog' => $catalog])
        @empty
            <div class="sm:col-span-2 lg:col-span-4">
                @include('components.empty-state', ['title' => 'Belum ada katalog.', 'description' => 'Katalog yang sudah dipublikasikan akan muncul di sini.'])
            </div>
        @endforelse
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <h2 class="text-2xl font-semibold">Tentang E-Catalog</h2>
        <p class="mt-4 max-w-3xl text-sea">E-Catalog membantu masyarakat menemukan informasi usaha dan layanan di Kelurahan Tambakrejo. Setiap entri menampilkan deskripsi, alamat, kontak, dan QR Code menuju halaman detail. Peta digital dikelola sebagai sistem terpisah di luar website ini.</p>
    </div>
</section>
@endsection
