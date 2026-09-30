@extends('layouts.public')

@section('title', 'Sesi Kedaluwarsa')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-20 text-center"><p class="text-sm font-semibold text-sea">419</p><h1 class="mt-2 text-3xl font-semibold">Sesi kedaluwarsa</h1><p class="mt-3 text-sea">Muat ulang halaman, lalu kirim kembali formulir Anda.</p><a href="{{ url()->previous() }}" class="mt-6 inline-flex min-h-11 items-center rounded-md bg-sea px-5 text-white">Kembali</a></section>
@endsection
