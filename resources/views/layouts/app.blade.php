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

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-linear-to-br flex min-h-screen flex-col from-slate-950 via-slate-900 to-slate-950 font-['Inter'] text-gray-200">
    @include('partials._nav')

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

            <div class="mt-4 text-xs gap-6 md:mt-0">
                Built by <a href="https://bmehul.com" class="inline underline hover:text-indigo-400" target="_blank">Mehul</a> using Laravel {{ app()->version() }}, and Livewire {{ \Composer\InstalledVersions::getPrettyVersion('livewire/livewire') }}
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sliderComponent', (totalSlides) => ({
                active: 0,
                total: totalSlides,
                interval: null,

                init() {
                    this.start();
                },

                start() {
                    if (this.interval) return;

                    this.interval = setInterval(() => {
                        this.next();
                    }, 6000);
                },

                next() {
                    this.active = (this.active + 1) % this.total;
                },

                prev() {
                    this.active = (this.active - 1 + this.total) % this.total;
                },

                go(index) {
                    this.active = index;
                }
            }));
        });
    </script>

</body>
</html>
