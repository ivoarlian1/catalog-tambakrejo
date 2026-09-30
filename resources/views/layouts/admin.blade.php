<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin') — E-Catalog Tambakrejo</title>
    <meta name="robots" content="noindex,nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sand font-sans text-navy antialiased">
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2">Lewati ke konten</a>
    <div class="min-h-screen md:grid md:grid-cols-[240px_1fr]">
        <aside class="border-b border-sky bg-navy text-white md:border-b-0 md:border-r">
            <div class="flex items-center justify-between px-4 py-4">
                <a href="{{ auth()->user()?->isSuperadmin() ? route('superadmin.dashboard') : route('catalog-admin.dashboard') }}" class="inline-flex min-h-11 items-center gap-3 font-semibold"><img src="{{ asset('images/logo/ecatalog-mark.svg') }}" alt="Simbol sementara E-Catalog, bukan logo resmi kelurahan" width="36" height="36" class="size-9 shrink-0"><span>{{ $panelTitle }}</span></a>
                <button type="button" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md border border-white/30 md:hidden" data-nav-toggle aria-expanded="false" aria-controls="menu-admin">
                    <span class="sr-only">Buka menu</span>
                    <span aria-hidden="true">☰</span>
                </button>
            </div>
            <nav id="menu-admin" data-nav-panel class="hidden px-4 pb-6 md:block">
                <ul class="flex flex-col gap-2">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="block rounded-md px-3 py-3 {{ request()->routeIs($item['active']) ? 'bg-white/15' : 'hover:bg-white/10' }}">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                    <li>
                        <form method="POST" action="{{ $logoutUrl }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-3 text-left hover:bg-white/10">Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>
        <div>
            <header class="border-b border-sky bg-white px-4 py-4">
                <p class="text-sm text-sea">{{ auth()->user()?->name }}</p>
            </header>
            <main id="konten" class="px-4 py-6">
                @if (session('success'))
                    <p class="mb-4 rounded-lg bg-sky px-4 py-3" role="status">{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800" role="alert">{{ session('error') }}</p>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
