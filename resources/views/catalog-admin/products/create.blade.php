@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<a href="{{ route('catalog-admin.products.index') }}" class="text-sm text-sea">&larr; Daftar produk</a><h1 class="mt-2 text-2xl font-semibold">Tambah Produk</h1>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-6 bg-white p-5">@include('components.product-form', ['catalog' => $catalog, 'adminPanel' => true])</div>
@endsection
