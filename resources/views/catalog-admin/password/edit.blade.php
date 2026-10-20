@extends('layouts.admin')

@section('title', 'Ubah Password')

@section('content')
<div class="mx-auto max-w-2xl">
    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sea">Keamanan akun</p>
    <h1 class="mt-2 text-2xl font-bold">Ubah Password</h1>
    <p class="mt-2 leading-6 text-sea">Masukkan password saat ini untuk melindungi akun, lalu buat password baru yang hanya Anda ketahui.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('catalog-admin.password.update') }}" class="mt-6 grid gap-5 rounded-2xl border border-white/80 bg-white/80 p-5 shadow-lg shadow-navy/5 backdrop-blur-xl sm:p-7">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password" class="mb-1.5 block text-sm font-semibold">Password saat ini</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="min-h-12 w-full rounded-xl border border-sky bg-white px-4">
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold">Password baru</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" aria-describedby="password-hint" class="min-h-12 w-full rounded-xl border border-sky bg-white px-4">
            <p id="password-hint" class="mt-1.5 text-sm text-sea">Minimal 8 karakter, serta mengandung huruf dan angka.</p>
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold">Konfirmasi password baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="min-h-12 w-full rounded-xl border border-sky bg-white px-4">
        </div>

        <div class="flex flex-wrap gap-3 border-t border-sky pt-5">
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sea px-5 font-semibold text-white transition hover:-translate-y-0.5 hover:bg-navy">Simpan password baru</button>
            <a href="{{ route('catalog-admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sky px-5 font-semibold text-sea transition hover:bg-sky">Kembali ke dashboard</a>
        </div>
    </form>
</div>
@endsection
