@extends('layouts.public')

@section('title', $catalog->name . ' | E-Catalog Kelurahan Tambakrejo')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($catalog->description ?: $catalog->address), 155))
@section('canonical', $catalog->detail_url)
@section('og_title', $catalog->name . ' | E-Catalog Kelurahan Tambakrejo')
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($catalog->description ?: $catalog->address), 155))
@section('og_image', $catalog->photo_url)

@section('content')
@php($contacts = $catalog->availableContacts())
<article class="mx-auto max-w-5xl px-4 py-10">
    <a href="{{ route('katalog.bytype', $catalog->type) }}" class="text-sm font-semibold text-sea">&larr; {{ $catalog->type_label }}</a>
    <div class="mt-5 grid gap-8 md:grid-cols-[minmax(0,1fr)_280px]">
        <div>
            <img src="{{ $catalog->photo_url }}" alt="Foto utama {{ $catalog->name }}" class="aspect-[16/9] w-full rounded-lg object-cover" width="1200" height="675" fetchpriority="high">
            @if ($catalog->photos->where('type', '!=', 'cover')->isNotEmpty())
                <section class="mt-6" aria-label="Galeri foto katalog">
                    <h2 class="text-lg font-semibold">Galeri foto</h2>
                    <ul class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($catalog->photos->where('type', '!=', 'cover') as $photo)
                            <li><a href="{{ $photo->url }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-md border border-sky"><img src="{{ $photo->url }}" alt="{{ $photo->type_label }} {{ $catalog->name }}" class="aspect-[4/3] w-full object-cover transition group-hover:scale-[1.02]" width="800" height="600" loading="lazy"><span class="block px-3 py-2 text-sm">{{ $photo->type_label }}</span></a></li>
                        @endforeach
                    </ul>
                </section>
            @endif
            <div class="mt-6 flex flex-wrap gap-2 text-sm">
                <span class="rounded-full bg-sky px-3 py-1">{{ $catalog->type_label }}</span>
                @if ($catalog->sub_type)<span class="rounded-full bg-sand px-3 py-1">{{ $catalog->sub_type }}</span>@endif
            </div>
            <h1 class="mt-4 text-3xl font-semibold">{{ $catalog->name }}</h1>
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
                            <article class="overflow-hidden rounded-md border border-sky bg-white"><img src="{{ $activity->photo_url }}" alt="Foto {{ $activity->title }}" class="aspect-video w-full object-cover" width="640" height="360" loading="lazy"><div class="p-4"><h3 class="font-semibold">{{ $activity->title }}</h3><p class="mt-1 text-sm text-sea">{{ $activity->event_date?->translatedFormat('d F Y') ?: 'Tanggal belum ditentukan' }}</p>@if ($activity->description)<p class="mt-3 text-sm leading-6">{{ $activity->description }}</p>@endif</div></article>
                        @endforeach
                    </div>
                </section>
            @endif
            @if ($catalog->type === 'umkm')
                <section class="mt-10 border-t border-sky pt-6">
                    <h2 class="text-xl font-semibold">Produk</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @forelse ($catalog->products as $product)
                            <article class="flex gap-4 border-b border-sky py-4">
                                <img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="h-20 w-20 rounded-md object-cover">
                                <div><h3 class="font-semibold">{{ $product->name }}</h3><p class="mt-1 text-sm text-sea">{{ $product->formatted_price }}</p><p class="mt-1 text-sm">{{ $product->availability_label }}</p>@if ($product->description)<p class="mt-2 text-sm text-sea">{{ $product->description }}</p>@endif</div>
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
