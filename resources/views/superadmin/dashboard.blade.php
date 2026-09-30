@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-sm text-sea">Ringkasan direktori</p><h1 class="mt-1 text-2xl font-semibold">Dashboard</h1></div><a href="{{ route('superadmin.catalogs.create') }}" class="inline-flex min-h-11 items-center rounded-md bg-sea px-4 font-semibold text-white">Tambah katalog</a></div>
<div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([['Total katalog', $stats['total_catalogs']], ['Published', $stats['published']], ['Draf', $stats['draft']], ['Produk', $stats['total_products']], ['Admin aktif', $stats['active_admins']]] as [$label, $value])
        <div class="border-l-4 border-teal bg-white px-4 py-4"><p class="text-sm text-sea">{{ $label }}</p><p class="mt-1 text-2xl font-semibold">{{ $value }}</p></div>
    @endforeach
    @foreach ($categoryStats as $category)
        <div class="border-l-4 border-teal bg-white px-4 py-4"><p class="text-sm text-sea">{{ $category->name }}</p><p class="mt-1 text-2xl font-semibold">{{ $category->catalogs_count }}</p></div>
    @endforeach
</div>
<section class="mt-10"><h2 class="text-xl font-semibold">Katalog terbaru</h2><div class="mt-4 overflow-x-auto rounded-md border border-sky bg-white"><table class="w-full min-w-[680px] text-left text-sm"><thead class="bg-sky"><tr><th class="p-3">Foto</th><th class="p-3">Nama</th><th class="p-3">Kategori</th><th class="p-3">Status</th><th class="p-3">Admin</th></tr></thead><tbody>@forelse ($latestCatalogs as $catalog)<tr class="border-t border-sky"><td class="p-3"><img src="{{ $catalog->photo_url }}" alt="Sampul {{ $catalog->name }}" class="size-12 rounded object-cover" width="48" height="48" loading="lazy"></td><td class="p-3"><a class="font-semibold text-sea" href="{{ route('superadmin.catalogs.show', $catalog) }}">{{ $catalog->name }}</a></td><td class="p-3">{{ $catalog->type_label }}</td><td class="p-3">{{ $catalog->status === 'published' ? 'Published' : 'Draf' }}</td><td class="p-3">{{ $catalog->admin?->name ?? 'Belum ditetapkan' }}</td></tr>@empty<tr><td colspan="5" class="p-5 text-center text-sea">Belum ada katalog.</td></tr>@endforelse</tbody></table></div></section>
@endsection
