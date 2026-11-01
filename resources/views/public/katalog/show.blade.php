@extends('layouts.public')

@section('title', $catalog->name . ' | E-Catalog Kelurahan Tambakrejo')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($catalog->description ?: $catalog->address), 155))
@section('canonical', $catalog->detail_url)
@section('og_title', $catalog->name . ' | E-Catalog Kelurahan Tambakrejo')
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($catalog->description ?: $catalog->address), 155))
@section('og_image', $catalog->photo_url)

@section('content')
@php
    $contacts = $catalog->availableContacts();
    $galleryPhotos = $catalog->photos->where('type', '!=', 'cover')->values();
    $gallerySlides = collect([
        ['url' => $catalog->photo_url, 'label' => 'Foto utama'],
    ])->concat($galleryPhotos->map(fn ($photo) => [
        'url' => $photo->url,
        'label' => $photo->type_label,
    ]));
@endphp
<article class="mx-auto max-w-6xl px-4 py-10">
    <a href="{{ route('katalog.bytype', $catalog->type) }}" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-sea/15 bg-white/70 px-4 text-sm font-semibold text-sea transition hover:-translate-x-1 hover:bg-white">&larr; {{ $catalog->type_label }}</a>
    <div class="mt-5 grid gap-8 md:grid-cols-[minmax(0,1fr)_280px]">
        <div>
            <section data-gallery aria-label="Slide foto {{ $catalog->name }}">
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/60 shadow-xl shadow-navy/10">
                    <div id="catalog-gallery-track" data-gallery-track role="region" tabindex="0" aria-label="Foto katalog, gunakan tombol panah untuk melihat foto lainnya" class="flex snap-x snap-mandatory overflow-x-auto overscroll-x-contain">
                        @foreach ($gallerySlides as $slide)
                            <button type="button" data-gallery-slide data-image-preview data-image-src="{{ $slide['url'] }}" data-image-alt="{{ $slide['label'] }} {{ $catalog->name }}" aria-label="Perbesar {{ $slide['label'] }} {{ $catalog->name }}" class="group relative block w-full shrink-0 snap-center overflow-hidden text-left">
                                <img src="{{ $slide['url'] }}" alt="{{ $slide['label'] }} {{ $catalog->name }}" class="aspect-[16/9] w-full object-cover transition duration-700 group-hover:scale-[1.03]" width="1200" height="675" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                <span class="absolute bottom-4 right-4 rounded-full border border-white/50 bg-navy/60 px-4 py-2 text-sm font-medium text-white opacity-0 backdrop-blur-md transition group-hover:opacity-100 group-focus-visible:opacity-100">Perbesar foto</span>
                            </button>
                        @endforeach
                    </div>
                    @if ($gallerySlides->count() > 1)
                        <button type="button" data-gallery-previous aria-label="Foto sebelumnya" aria-controls="catalog-gallery-track" class="absolute inset-y-0 left-0 z-10 flex w-1/5 items-center justify-start bg-gradient-to-r from-black/50 to-transparent p-3 text-white focus-visible:outline-white"><span aria-hidden="true" class="inline-flex size-12 items-center justify-center rounded-full border border-white/60 bg-slate-950/90 text-3xl font-semibold leading-none text-white shadow-xl transition hover:scale-105 hover:bg-slate-900">‹</span></button>
                        <button type="button" data-gallery-next aria-label="Foto berikutnya" aria-controls="catalog-gallery-track" class="absolute inset-y-0 right-0 z-10 flex w-1/5 items-center justify-end bg-gradient-to-l from-black/50 to-transparent p-3 text-white focus-visible:outline-white"><span aria-hidden="true" class="inline-flex size-12 items-center justify-center rounded-full border border-white/60 bg-slate-950/90 text-3xl font-semibold leading-none text-white shadow-xl transition hover:scale-105 hover:bg-slate-900">›</span></button>
                        <span data-gallery-counter aria-live="polite" class="absolute bottom-4 left-4 z-20 rounded-lg border border-white/40 bg-slate-950/90 px-3 py-1.5 text-sm font-semibold text-white shadow-lg">1 / {{ $gallerySlides->count() }}</span>
                        <button type="button" data-gallery-open aria-label="Buka galeri foto {{ $catalog->name }}" class="absolute bottom-4 right-4 z-20 flex min-h-14 items-center gap-3 rounded-xl border border-white/70 bg-slate-950/90 px-3 py-2 text-left text-white shadow-xl backdrop-blur-md transition hover:bg-slate-900 focus-visible:outline-white">
                            <span class="relative block h-10 w-14 shrink-0">
                                @foreach ($gallerySlides->take(3) as $slide)
                                    <img src="{{ $slide['url'] }}" alt="" class="absolute top-0 h-9 w-10 rounded-md border-2 border-white object-cover shadow {{ $loop->first ? 'left-0 z-30 -rotate-12' : '' }} {{ $loop->iteration === 2 ? 'left-2 z-20' : '' }} {{ $loop->last && $loop->count > 2 ? 'left-4 z-10 rotate-12' : '' }}" loading="lazy">
                                @endforeach
                            </span>
                            <span><span class="block text-sm font-semibold">Lihat semua foto</span><span class="block text-xs text-white/80">{{ $gallerySlides->count() }} foto</span></span>
                        </button>
                    @endif
                </div>
                @if ($gallerySlides->count() > 1)
                    <div class="mt-3 flex items-center justify-center gap-2.5" role="group" aria-label="Pilih foto">
                        @foreach ($gallerySlides as $slide)
                            <button type="button" data-gallery-dot aria-label="Tampilkan foto {{ $loop->iteration }}: {{ $slide['label'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}" class="size-3 rounded-full border border-white bg-slate-400 transition hover:scale-110 hover:bg-slate-600 {{ $loop->first ? 'scale-110 border-sea bg-sea ring-2 ring-sea/30' : '' }}"></button>
                        @endforeach
                    </div>
                @endif
            @if ($gallerySlides->count() > 1)
                <dialog data-gallery-viewer aria-label="Galeri foto {{ $catalog->name }}" class="m-auto max-h-[92dvh] w-[min(94vw,72rem)] overflow-hidden rounded-3xl border border-white/30 bg-navy p-3 text-white shadow-2xl backdrop:bg-navy/85 backdrop:backdrop-blur-sm sm:p-5">
                    <div class="flex items-center justify-between gap-4 pb-3">
                        <div>
                            <p class="font-semibold">Galeri foto</p>
                            <p class="mt-1 text-sm text-white/70">{{ $catalog->name }} · {{ $gallerySlides->count() }} foto</p>
                        </div>
                        <button type="button" data-gallery-close aria-label="Tutup galeri foto" class="inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-white/40 text-2xl transition hover:bg-white/15">×</button>
                    </div>
                    <div class="grid max-h-[78dvh] grid-cols-2 gap-3 overflow-y-auto pr-1 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                        @foreach ($gallerySlides as $slide)
                            <button type="button" data-gallery-photo data-image-preview data-image-src="{{ $slide['url'] }}" data-image-alt="{{ $slide['label'] }} {{ $catalog->name }}" aria-label="Buka {{ $slide['label'] }} {{ $catalog->name }}" class="group overflow-hidden rounded-xl border border-white/15 bg-white/5 text-left transition hover:border-white/50 hover:bg-white/10">
                                <img src="{{ $slide['url'] }}" alt="{{ $slide['label'] }} {{ $catalog->name }}" class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105" width="800" height="600" loading="lazy">
                                <span class="block truncate px-3 py-2 text-sm text-white/90">{{ $slide['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </dialog>
            @endif
            </section>
            <div aria-label="Nomor urut katalog {{ $catalog->sort_order }}" class="mt-5 inline-flex items-center gap-3 rounded-xl border border-white bg-white/70 px-4 py-3 text-navy shadow-md backdrop-blur-xl">
                <span class="text-2xl font-bold leading-none">{{ $catalog->sort_order }}</span>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                <span class="rounded-full border border-sea/10 bg-sky/80 px-3 py-1 font-medium text-sea">{{ $catalog->type_label }}</span>
                @if ($catalog->sub_type)<span class="rounded-full border border-orange-200/70 bg-sand px-3 py-1 font-medium">{{ $catalog->sub_type }}</span>@endif
            </div>
            <h1 class="mt-4 text-3xl font-bold tracking-tight md:text-4xl">{{ $catalog->name }}</h1>
            @if ($catalog->description)<p class="mt-4 whitespace-pre-line leading-7 text-sea">{{ $catalog->description }}</p>@endif
            <dl class="mt-8 grid gap-4 border-t border-sky pt-6 sm:grid-cols-2">
                <div><dt class="text-sm text-sea">{{ $catalog->contact_person_label }}</dt><dd class="mt-1 font-medium">{{ $catalog->contact_person ?: 'Belum dicantumkan' }}</dd></div>
                <div><dt class="text-sm text-sea">Alamat</dt><dd class="mt-1">{{ $catalog->address }}</dd></div>
                @if ($catalog->latitude !== null && $catalog->longitude !== null)
                    <div><dt class="text-sm text-sea">Koordinat Lokasi</dt><dd class="mt-1 font-mono">{{ $catalog->latitude }}, {{ $catalog->longitude }}</dd></div>
                @endif
                    @if ($catalog->other_contact)<div><dt class="text-sm text-sea">Kontak lainnya</dt><dd class="mt-1">{{ $catalog->other_contact }}</dd></div>@endif
            </dl>
            @if ($contacts)
                <section class="mt-8 border-t border-sky pt-6">
                    <h2 class="text-xl font-semibold">Kontak</h2>
                    <ul class="mt-3 flex flex-wrap gap-3">
                        @foreach ($contacts as $label => $url)
                            <li><a href="{{ $url }}" class="inline-flex min-h-11 items-center rounded-lg border border-sky px-4 text-sea" @if (str_starts_with($url, 'https://')) target="_blank" rel="noopener noreferrer" @endif>{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endif
            @if ($catalog->activities->isNotEmpty())
                <section class="mt-10 border-t border-sky pt-6" aria-labelledby="activities-heading">
                    <h2 id="activities-heading" class="text-xl font-semibold">Berita & Kegiatan</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($catalog->activities as $activity)
                            <article class="overflow-hidden rounded-2xl border border-white/80 bg-white/70 shadow-lg shadow-navy/5 backdrop-blur-xl"><img src="{{ $activity->photo_url }}" alt="Foto {{ $activity->title }}" class="aspect-video w-full object-cover" width="640" height="360" loading="lazy"><div class="p-4"><h3 class="font-semibold">{{ $activity->title }}</h3><p class="mt-1 text-sm text-sea">{{ $activity->event_date?->translatedFormat('d F Y') ?: 'Tanggal belum ditentukan' }}</p>@if ($activity->description)<p class="mt-3 text-sm leading-6">{{ $activity->description }}</p>@endif</div></article>
                        @endforeach
                    </div>
                </section>
            @endif
            @if ($catalog->type === 'umkm')
                <section class="mt-10 border-t border-sky/80 pt-8">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Produk lokal</p>
                            <h2 class="mt-1 text-2xl font-bold">Produk UMKM</h2>
                        </div>
                        <span class="rounded-full border border-sea/10 bg-white/70 px-3 py-1 text-sm font-medium text-sea">{{ $catalog->products->count() }} produk</span>
                    </div>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($catalog->products as $product)
                            <article class="group overflow-hidden rounded-3xl border border-white/80 bg-white/75 shadow-lg shadow-navy/5 backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-sea/10">
                                <button type="button" data-image-preview data-image-src="{{ $product->photo_url }}" data-image-alt="Foto {{ $product->name }}" aria-label="Lihat foto produk {{ $product->name }}" class="relative block w-full overflow-hidden bg-sky text-left">
                                    <img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" width="800" height="600" loading="lazy">
                                    <span class="absolute bottom-3 right-3 rounded-full border border-white/50 bg-navy/60 px-3 py-1.5 text-xs font-medium text-white opacity-0 backdrop-blur-md transition group-hover:opacity-100 group-focus-visible:opacity-100">Lihat foto</span>
                                </button>
                                <div class="flex h-full flex-col gap-3 p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-lg font-bold leading-snug">{{ $product->name }}</h3>
                                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->is_available ? 'bg-teal/10 text-sea' : 'bg-sand text-sea' }}">{{ $product->availability_label }}</span>
                                    </div>
                                    <p class="text-lg font-bold text-sea">{{ $product->formatted_price }}</p>
                                    @if ($product->description)
                                        <p class="text-sm leading-6 text-sea">{{ $product->description }}</p>
                                    @else
                                        <p class="text-sm italic text-sea/70">Deskripsi produk belum tersedia.</p>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="text-sea">Produk belum dicantumkan.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
        <aside class="h-fit border-t border-sky pt-6 md:border-l md:border-t-0 md:pl-6 md:pt-0">
            <h2 class="text-lg font-semibold">QR Code katalog</h2>
            <div class="mt-3 max-w-[240px] bg-white p-2" aria-label="QR Code menuju halaman katalog">{!! $qrSvg !!}</div>
            <a href="{{ route('katalog.qr', ['type' => $catalog->type, 'slug' => $catalog->slug]) }}" class="mt-3 inline-flex min-h-11 items-center rounded-lg bg-sea px-4 text-white">Unduh QR (JPG)</a>
            <p class="mt-3 break-all text-xs text-sea">{{ $catalog->detail_url }}</p>
        </aside>
    </div>
</article>
@endsection
