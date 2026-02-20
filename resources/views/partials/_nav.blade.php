<header class="sticky top-0 z-50 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="bg-linear-to-r from-indigo-400 to-indigo-600 bg-clip-text text-2xl font-extrabold text-transparent">
                KoDram
            </span>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="{{ route('home') }}" class="transition hover:text-indigo-400">
                Home
            </a>

            <a href="{{ route('dramas') }}" class="transition hover:text-indigo-400">
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
