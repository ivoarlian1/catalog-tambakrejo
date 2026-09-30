@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4"><div><h1 class="text-2xl font-semibold">Kategori</h1><p class="mt-1 text-sm text-sea">Kelola kategori katalog dan status ketersediaannya.</p></div><a href="{{ route('superadmin.categories.create') }}" class="inline-flex min-h-11 items-center rounded-md bg-sea px-4 text-white">Tambah kategori</a></div>
<div class="mt-5 overflow-x-auto rounded-md border border-sky bg-white"><table class="w-full min-w-[650px] text-left text-sm"><thead class="bg-sky"><tr><th class="p-3">Kategori</th><th class="p-3">Slug URL</th><th class="p-3">Katalog</th><th class="p-3">Subkategori</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead><tbody>@forelse ($categories as $category)<tr class="border-t border-sky"><td class="p-3 font-semibold">{{ $category->name }}</td><td class="p-3 font-mono text-xs">{{ $category->slug }}</td><td class="p-3">{{ $category->catalogs_count }}</td><td class="p-3">{{ $category->subcategories_count }}</td><td class="p-3">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</td><td class="p-3"><div class="flex items-center gap-3"><a href="{{ route('superadmin.categories.edit', $category) }}" class="text-sea underline">Edit</a>@if ($category->catalogs_count === 0)<form method="POST" action="{{ route('superadmin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="text-red-800 underline">Hapus</button></form>@endif</div></td></tr>@empty<tr><td colspan="6" class="p-6 text-center text-sea">Belum ada kategori.</td></tr>@endforelse</tbody></table></div>
<div class="mt-5">{{ $categories->links() }}</div>
@endsection
