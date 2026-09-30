@extends('layouts.admin')

@section('title', 'Profil Katalog')

@section('content')
<h1 class="text-2xl font-semibold">Profil Katalog</h1><p class="mt-1 text-sm text-sea">Kategori dan slug katalog dikelola oleh Superadmin.</p>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-6 bg-white p-5"><x-catalog-form :catalog="$catalog" :sub-type-options="$subTypeOptions" /></div>
@endsection
