@extends('layouts.public')

@section('title', 'Akses Ditolak')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-20 text-center"><p class="text-sm font-semibold text-sea">403</p><h1 class="mt-2 text-3xl font-semibold">Akses ditolak</h1><p class="mt-3 text-sea">Anda tidak memiliki izin untuk membuka halaman ini.</p><a href="{{ route('home') }}" class="mt-6 inline-flex min-h-11 items-center rounded-md bg-sea px-5 text-white">Kembali ke beranda</a></section>
@endsection
