<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Katalog Fasum Kelurahan Tambakrejo')</title>
    <meta name="description" content="@yield('meta_description', 'Direktori digital usaha, layanan, dan fasilitas di Kelurahan Tambakrejo.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('og_title', 'E-Catalog Kelurahan Tambakrejo')">
    <meta property="og:description" content="@yield('og_description', 'Direktori digital layanan dan usaha di Kelurahan Tambakrejo.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/placeholder-catalog.svg'))">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-navy antialiased">
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2">Lewati ke konten</a>
    <header class="sticky top-0 z-50 border-b border-sky bg-white">
        <div class="mx-auto max-w-7xl px-4 py-3 md:flex md:items-center md:justify-between md:gap-6">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center gap-3 font-semibold text-navy">
                    <img src="{{ asset('images/logo/tambakrejo.png') }}" alt="Logo Kelurahan Tambakrejo" width="60" height="60" class="size-[60px] shrink-0 object-contain">
                    <span>Katalog Kelurahan Tambakrejo</span>
                </a>
                <button type="button" aria-label="Buka menu" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md border border-sky bg-white md:hidden" data-nav-toggle aria-expanded="false" aria-controls="menu-utama">
                    <span data-nav-icon aria-hidden="true">☰</span>
                </button>
            </div>
            <nav id="menu-utama" data-nav-panel class="hidden max-h-[calc(100dvh-5rem)] w-full overflow-y-auto py-3 md:block md:max-h-none md:w-auto md:overflow-visible md:py-0">
                <ul class="flex flex-col gap-1 md:flex-row md:flex-wrap md:items-center md:justify-end md:gap-x-3 xl:flex-nowrap xl:gap-x-2">
                    <li><a class="inline-flex min-h-11 items-center rounded-md px-3 hover:bg-sky" href="{{ route('home') }}">Beranda</a></li>
                    <li><a class="inline-flex min-h-11 items-center rounded-md px-3 hover:bg-sky" href="{{ route('katalog.index') }}">Katalog</a></li>
                    @foreach ($publicCategories as $category)
                        <li><a class="inline-flex min-h-11 max-w-full items-center rounded-md px-3 hover:bg-sky" href="{{ route('katalog.bytype', $category->slug) }}">{{ $category->name }}</a></li>
                    @endforeach
                    @if (auth()->user()?->isSuperadmin())
                        <li><a class="inline-flex min-h-11 items-center justify-center rounded-md border border-sea px-4 font-semibold text-sea hover:bg-sky" href="{{ route('superadmin.dashboard') }}">Dashboard Superadmin</a></li>
                    @elseif (auth()->user()?->isCatalogAdmin())
                        <li class="flex flex-col gap-1 sm:flex-row sm:items-center">
                            <a class="inline-flex min-h-11 items-center justify-center rounded-md border border-sea px-4 font-semibold text-sea hover:bg-sky" href="{{ route('catalog-admin.dashboard') }}">Dashboard Catalog Admin</a>
                            <form method="POST" action="{{ route('catalog-admin.logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-md px-3 text-sea hover:bg-sky sm:w-auto">Logout</button>
                            </form>
                        </li>
                    @else
                        <li><a class="inline-flex min-h-11 items-center justify-center rounded-md border border-sea px-4 font-semibold text-sea hover:bg-sky" href="{{ route('catalog-admin.login') }}">Login Catalog Admin</a></li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>
    <main id="konten">
        @yield('content')
    </main>
    <footer class="mt-16 border-t border-sky bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-sea sm:flex-row sm:items-center sm:justify-between">
            <p>E-Catalog Kelurahan Tambakrejo. Direktori informasi untuk masyarakat.</p>
            <p class="font-medium">© KKN GIAT 17 UNNES</p>
        </div>
    </footer>
</body>
</html>
