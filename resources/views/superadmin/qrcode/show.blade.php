@extends('layouts.admin')

@section('title', 'QR Code ' . $catalog->name)

@section('content')
<a href="{{ route('superadmin.catalogs.show', $catalog) }}" class="text-sm text-sea">&larr; Detail katalog</a><h1 class="mt-2 text-2xl font-semibold">QR Code katalog</h1><p class="mt-1 text-sea">{{ $catalog->name }} · {{ $catalog->status === 'published' ? 'Published' : 'Draf' }}</p>
<div class="mt-6 grid max-w-3xl gap-8 rounded-md border border-sky bg-white p-6 sm:grid-cols-[300px_1fr]"><div class="flex min-h-[300px] items-center justify-center bg-white p-4" aria-label="QR Code menuju halaman katalog">{!! $qrSvg !!}</div><div><h2 class="text-lg font-semibold">{{ $catalog->name }}</h2><p class="mt-2 text-sm text-sea">QR hanya berisi URL detail katalog Laravel.</p><p class="mt-4 break-all rounded-md bg-sand p-3 text-sm text-sea">{{ $url }}</p><div class="mt-5 flex flex-wrap gap-2"><a href="{{ route('superadmin.catalogs.qr.jpg', $catalog) }}" class="inline-flex min-h-11 items-center rounded-md bg-sea px-4 font-semibold text-white">Unduh JPG</a><a href="{{ route('superadmin.catalogs.qr.download', $catalog) }}" class="inline-flex min-h-11 items-center rounded-md border border-sea px-4 text-sea">Unduh SVG</a><button type="button" onclick="window.print()" class="inline-flex min-h-11 items-center rounded-md border border-sea px-4 text-sea">Cetak</button></div></div></div>
@endsection
