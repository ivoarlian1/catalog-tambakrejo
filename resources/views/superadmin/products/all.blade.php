@extends('layouts.admin')

@section('title', 'Semua Produk')

@section('content')
<h1 class="text-2xl font-semibold">Semua Produk</h1><p class="mt-1 text-sm text-sea">Produk katalog UMKM</p>
<form method="GET" class="mt-5 flex gap-3"><label class="sr-only" for="search">Cari produk</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Nama produk" class="min-h-11 w-full max-w-md rounded-md border border-sky px-3"><button class="min-h-11 rounded-md border border-sea px-4 text-sea">Cari</button></form>
<div class="mt-5 overflow-x-auto rounded-md border border-sky bg-white"><table class="w-full min-w-[680px] text-left text-sm"><thead class="bg-sky"><tr><th class="p-3">Foto</th><th class="p-3">Produk</th><th class="p-3">Katalog</th><th class="p-3">Harga</th><th class="p-3">Status</th></tr></thead><tbody>@forelse ($products as $product)<tr class="border-t border-sky"><td class="p-3"><img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="size-12 rounded object-cover" width="48" height="48" loading="lazy"></td><td class="p-3">{{ $product->name }}</td><td class="p-3">{{ $product->catalog?->name ?? 'Katalog dihapus' }}</td><td class="p-3">{{ $product->formatted_price }}</td><td class="p-3">{{ $product->availability_label }}</td></tr>@empty<tr><td colspan="5" class="p-6 text-center text-sea">Belum ada produk.</td></tr>@endforelse</tbody></table></div><div class="mt-5">{{ $products->links() }}</div>
@endsection
