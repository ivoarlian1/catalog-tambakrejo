@extends('layouts.public')

@section('title', 'E-Catalog Kelurahan Tambakrejo')
@section('meta_description', 'Direktori digital usaha, layanan, dan fasilitas di Kelurahan Tambakrejo.')
@section('og_title', 'E-Catalog Kelurahan Tambakrejo')

@section('content')
<section class="relative isolate overflow-hidden bg-navy text-white">
    <div aria-hidden="true" class="absolute -right-24 -top-36 -z-10 size-[30rem] rounded-full bg-teal/40 blur-3xl"></div>
    <div aria-hidden="true" class="absolute -bottom-56 -left-24 -z-10 size-[28rem] rounded-full bg-sea/60 blur-3xl"></div>
    <div class="mx-auto max-w-6xl px-4 py-20 md:py-28">
        <div class="motion-enter max-w-3xl">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-sky backdrop-blur-md">Jelajahi potensi lokal</span>
            <h1 class="mt-6 text-4xl font-bold tracking-tight md:text-6xl">E-Catalog Kelurahan Tambakrejo</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-sky">Temukan usaha, layanan, dan fasilitas di Kelurahan Tambakrejo melalui satu direktori yang mudah dicari dan nyaman dibuka dari ponsel.</p>
        </div>
        <form action="{{ route('katalog.index') }}" method="GET" class="motion-enter mt-9 flex max-w-2xl flex-col gap-3 rounded-2xl border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur-xl sm:flex-row" style="animation-delay: 100ms">
            <label for="search" class="sr-only">Cari katalog</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Cari usaha, layanan, atau fasilitas..." class="min-h-12 w-full rounded-xl border border-white/30 bg-white/95 px-4 text-navy placeholder:text-sea/70">
            <button type="submit" class="min-h-12 shrink-0 rounded-xl bg-teal px-7 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-teal/90 focus-visible:outline-white">Cari katalog <span aria-hidden="true">→</span></button>
        </form>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Temukan yang dibutuhkan</p>
            <h2 class="mt-2 text-2xl font-bold md:text-3xl">Jelajahi kategori</h2>
        </div>
    </div>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($categories as $category)
            @php($type = $category->slug)
            <a href="{{ route('katalog.bytype', $type) }}" class="group rounded-2xl border border-white/80 bg-white/70 p-5 shadow-lg shadow-navy/5 backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:border-teal/40 hover:shadow-xl hover:shadow-sea/10">
                <span class="inline-flex size-11 items-center justify-center rounded-xl bg-sky text-xl text-sea transition duration-300 group-hover:rotate-6 group-hover:scale-110" aria-hidden="true">✦</span>
                <p class="mt-4 text-lg font-semibold">{{ $category->name }}</p>
                <p class="mt-1 text-sm text-sea">{{ $counts[$type] }} katalog <span class="float-right transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></p>
            </a>
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Pilihan terbaru</p>
            <h2 class="mt-2 text-2xl font-bold md:text-3xl">Daftar katalog</h2>
        </div>
        <a href="{{ route('katalog.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-sea/20 bg-white/70 px-5 font-semibold text-sea transition hover:border-sea hover:bg-white">Lihat semua <span aria-hidden="true">→</span></a>
    </div>
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

<section class="border-y border-white/80 bg-white/55 backdrop-blur-xl">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Tentang kami</p>
        <h2 class="mt-2 text-2xl font-bold">Informasi lokal, dalam satu tempat</h2>
        <p class="mt-4 max-w-3xl leading-7 text-sea">E-Catalog membantu masyarakat menemukan informasi usaha dan layanan di Kelurahan Tambakrejo. Setiap entri menampilkan deskripsi, alamat, kontak, dan QR Code menuju halaman detail. Peta digital dikelola sebagai sistem terpisah di luar website ini.</p>
    </div>
</section>
@endsection
