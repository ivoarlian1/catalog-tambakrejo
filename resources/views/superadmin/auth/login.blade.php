@extends('layouts.auth')

@section('title', 'Masuk Superadmin')

@section('content')
<h1 class="text-2xl font-semibold">Masuk Superadmin</h1>
<p class="mt-2 text-sm text-sea">Kelola katalog dan akun admin Kelurahan Tambakrejo.</p>
@if ($errors->any())<div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-800" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('superadmin.login.post') }}" class="mt-6 space-y-4">
    @csrf
    <div><label for="email" class="mb-1 block font-medium">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="min-h-11 w-full rounded-md border border-sky px-3"><x-input-error field="email" /></div>
    <div><label for="password" class="mb-1 block font-medium">Password</label><div class="relative"><input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-11 w-full rounded-md border border-sky px-3 pr-14"><button type="button" class="absolute inset-y-0 right-0 inline-flex min-w-11 items-center justify-center px-3 text-sea" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password"><span aria-hidden="true" class="text-lg">&#128065;</span><span class="sr-only">Tampilkan password</span></button></div><x-input-error field="password" /></div>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="remember" value="1" class="size-4"> Ingat saya</label>
    <button class="min-h-11 w-full rounded-md bg-sea px-4 font-semibold text-white">Masuk</button>
</form>
<a href="{{ route('home') }}" class="mt-5 inline-block text-sm text-sea">Kembali ke katalog publik</a>
@endsection
