@extends('layouts.public')

@section('title', 'Gangguan Layanan')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-20 text-center"><p class="text-sm font-semibold text-sea">500</p><h1 class="mt-2 text-3xl font-semibold">Layanan sedang mengalami gangguan</h1><p class="mt-3 text-sea">Silakan coba kembali beberapa saat lagi.</p><a href="{{ route('home') }}" class="mt-6 inline-flex min-h-11 items-center rounded-md bg-sea px-5 text-white">Kembali ke beranda</a></section>
@endsection
