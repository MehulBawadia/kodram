<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $title ?? 'KoDram - Discover Korean Dramas & Movies' }}
    </title>

    <meta name="description" content="{{ $metaDescription ?? 'Explore Korean dramas and movies with detailed information, ratings, trailers and more.' }}">

    <link rel="icon" type="image/ico" href="{{ asset('favicon.ico') }}" />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-linear-to-br flex min-h-screen flex-col from-slate-950 via-slate-900 to-slate-950 font-['Inter'] text-gray-200">
    <header class="sticky top-0 z-50 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="bg-linear-to-r from-indigo-400 to-indigo-600 bg-clip-text text-2xl font-extrabold text-transparent">
                    KoDram
                </span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                <a href="{{ url('/') }}" class="transition hover:text-indigo-400">
                    Home
                </a>

                <a href="{{ url('/') }}" class="transition hover:text-indigo-400">
                    Dramas
                </a>

                <a href="{{ url('/') }}" class="transition hover:text-indigo-400">
                    Movies
                </a>
            </nav>

            {{-- <div class="hidden w-72 md:block">
                @livewire('search-bar')
            </div> --}}
        </div>
    </header>

    <main class="flex-1">
        <div class="mx-auto max-w-7xl px-6 py-10">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-slate-800 bg-slate-900/60 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between px-6 py-8 text-sm text-gray-400 md:flex-row">
            <p>
                &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
            </p>

            <div class="mt-4 flex gap-6 md:mt-0">
                <a href="{{ url('/') }}" class="transition hover:text-indigo-400">
                    Terms
                </a>
                <a href="{{ url('/') }}" class="transition hover:text-indigo-400">
                    Privacy
                </a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
