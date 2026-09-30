@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<a href="{{ route('superadmin.catalogs.products.index', $catalog) }}" class="text-sm text-sea">&larr; Produk {{ $catalog->name }}</a><h1 class="mt-2 text-2xl font-semibold">Edit Produk</h1>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-6 bg-white p-5">@include('components.product-form', ['catalog' => $catalog, 'product' => $product, 'adminPanel' => false])</div>
@endsection
