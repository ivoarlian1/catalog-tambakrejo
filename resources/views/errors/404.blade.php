@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-20 text-center"><p class="text-sm font-semibold text-sea">404</p><h1 class="mt-2 text-3xl font-semibold">Halaman tidak ditemukan</h1><p class="mt-3 text-sea">Alamat yang Anda buka tidak tersedia atau katalog sudah tidak dipublikasikan.</p><a href="{{ route('katalog.index') }}" class="mt-6 inline-flex min-h-11 items-center rounded-md bg-sea px-5 text-white">Jelajahi katalog</a></section>
@endsection
