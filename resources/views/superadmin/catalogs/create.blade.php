@extends('layouts.admin')

@section('title', 'Tambah Katalog')

@section('content')
<a href="{{ route('superadmin.catalogs.index') }}" class="text-sm text-sea">&larr; Daftar katalog</a><h1 class="mt-2 text-2xl font-semibold">Tambah katalog</h1>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-semibold">Periksa kembali isian berikut:</p><ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-6 bg-white p-5"><x-catalog-form :type-labels="$typeLabels" :sub-type-options="$subTypeOptions" /></div>
@endsection
