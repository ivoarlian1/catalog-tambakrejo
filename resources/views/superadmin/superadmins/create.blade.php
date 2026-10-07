@extends('layouts.admin')

@section('title', 'Tambah Superadmin')

@section('content')
<a href="{{ route('superadmin.superadmins.index') }}" class="text-sm text-sea">&larr; Daftar Superadmin</a>
<h1 class="mt-2 text-2xl font-semibold">Tambah Superadmin</h1>

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
    <form method="POST" action="{{ route('superadmin.superadmins.store') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="mb-1 block font-medium">Nama</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="min-h-11 w-full rounded-md border border-sky px-3">
            <x-input-error field="name" />
        </div>
        <div>
            <label for="email" class="mb-1 block font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="min-h-11 w-full rounded-md border border-sky px-3">
            <x-input-error field="email" />
        </div>
        <div>
            <label for="password" class="mb-1 block font-medium">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required class="min-h-11 w-full rounded-md border border-sky px-3">
            <p class="mt-1 text-sm text-sea">Minimal 8 karakter, berisi huruf dan angka.</p>
            <x-input-error field="password" />
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block font-medium">Konfirmasi password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="min-h-11 w-full rounded-md border border-sky px-3">
        </div>
        <button class="min-h-11 rounded-md bg-sea px-5 text-white">Buat akun Superadmin</button>
    </form>
</div>
@endsection
