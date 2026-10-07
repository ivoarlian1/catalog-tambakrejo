@extends('layouts.admin')

@section('title', 'Edit Superadmin')

@section('content')
<a href="{{ route('superadmin.superadmins.index') }}" class="text-sm text-sea">&larr; Daftar Superadmin</a>
<h1 class="mt-2 text-2xl font-semibold">Edit Superadmin</h1>

@if ($errors->any())
    <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-6 max-w-2xl bg-white p-5">
    <form method="POST" action="{{ route('superadmin.superadmins.update', $superadmin) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="mb-1 block font-medium">Nama</label>
            <input id="name" name="name" value="{{ old('name', $superadmin->name) }}" required class="min-h-11 w-full rounded-md border border-sky px-3">
            <x-input-error field="name" />
        </div>
        <div>
            <label for="email" class="mb-1 block font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $superadmin->email) }}" required class="min-h-11 w-full rounded-md border border-sky px-3">
            <x-input-error field="email" />
        </div>
        <div>
            <label for="password" class="mb-1 block font-medium">Password baru (kosongkan jika tidak diubah)</label>
            <input id="password" name="password" type="password" autocomplete="new-password" class="min-h-11 w-full rounded-md border border-sky px-3">
            <p class="mt-1 text-sm text-sea">Minimal 8 karakter, berisi huruf dan angka.</p>
            <x-input-error field="password" />
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block font-medium">Konfirmasi password baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="min-h-11 w-full rounded-md border border-sky px-3">
        </div>
        @if ($superadmin->is(auth()->user()))
            <p class="text-sm text-sea">Status akun Anda: {{ $superadmin->is_active ? 'aktif' : 'nonaktif' }}. Status akun sendiri tidak dapat diubah.</p>
        @else
            <input type="hidden" name="is_active" value="0">
            <label class="flex min-h-11 items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $superadmin->is_active)) class="size-4">
                Akun aktif
            </label>
        @endif
        <button class="min-h-11 rounded-md bg-sea px-5 text-white">Simpan perubahan</button>
    </form>
</div>
@endsection
