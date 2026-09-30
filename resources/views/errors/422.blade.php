@extends('layouts.public')

@section('title', 'Data Tidak Valid')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-20 text-center"><p class="text-sm font-semibold text-sea">422</p><h1 class="mt-2 text-3xl font-semibold">Data tidak dapat diproses</h1><p class="mt-3 text-sea">Periksa kembali data yang dikirim, lalu coba lagi.</p><a href="{{ url()->previous() }}" class="mt-6 inline-flex min-h-11 items-center rounded-md bg-sea px-5 text-white">Kembali ke formulir</a></section>
@endsection
