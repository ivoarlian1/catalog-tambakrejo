@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<a href="{{ route('superadmin.categories.index') }}" class="text-sm text-sea">&larr; Daftar kategori</a><h1 class="mt-2 text-2xl font-semibold">Tambah kategori</h1>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-6 max-w-2xl bg-white p-5"><form method="POST" action="{{ route('superadmin.categories.store') }}" class="space-y-4">@csrf
    <div><label for="name" class="mb-1 block font-medium">Nama kategori</label><input id="name" name="name" value="{{ old('name') }}" required class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="name" /></div>
    <div><label for="slug" class="mb-1 block font-medium">Slug</label><input id="slug" name="slug" value="{{ old('slug') }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" class="min-h-11 w-full rounded-md border border-sky px-3"><p class="mt-1 text-xs text-sea">Slug URL dibuat dari nama bila dikosongkan.</p><x-input-error field="slug" /></div>
    <div><label for="sort_order" class="mb-1 block font-medium">Urutan Tampil</label><input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', 0) }}" class="min-h-11 w-full rounded-md border border-sky px-3"><p class="mt-1 text-xs text-sea">Menentukan posisi kategori pada daftar dan halaman publik. Angka lebih kecil tampil lebih dahulu.</p></div>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="size-4"> Kategori aktif untuk katalog baru</label>
    <button class="min-h-11 rounded-md bg-sea px-5 text-white">Simpan kategori</button>
</form></div>
@endsection
