<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') — E-Catalog Tambakrejo</title>
    <meta name="robots" content="noindex,nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sand font-sans text-navy antialiased">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-4 py-10">
        <div class="w-full rounded-md border border-sky bg-white p-6">
            @yield('content')
        </div>
    </main>
</body>
</html>
