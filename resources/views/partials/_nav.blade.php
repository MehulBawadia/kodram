<header class="sticky top-0 z-50 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md" x-data="{ openMobileMenu: false, openSearch: false }">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="bg-linear-to-r from-indigo-400 to-indigo-600 bg-clip-text text-2xl font-extrabold text-transparent">
                {{ config('app.name') }}
            </span>
        </a>

        <div class="hidden w-96 lg:w-120 md:block">
            <livewire:search />
        </div>

        <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="{{ route('home') }}" class="transition hover:text-indigo-400">
                Home
            </a>

            <a href="{{ route('dramas') }}" class="transition hover:text-indigo-400">
                Dramas
            </a>

            <a href="{{ route('movies') }}" class="transition hover:text-indigo-400">
                Movies
            </a>
        </nav>

        <div class="relative md:hidden space-x-6">
            <button type="button" class="font-bold" @click="openSearch = !openSearch; openMobileMenu = false">
                <span :class="{'hidden': openSearch}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#fff" class="w-5 h-5">
                        <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                    </svg>
                </span> <!-- Search icon -->

                <span :class="{'hidden': !openSearch}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="#fff" class="w-5 h-5">
                        <path d="M55.1 73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L147.2 256 9.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192.5 301.3 329.9 438.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.8 256 375.1 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192.5 210.7 55.1 73.4z"/>
                    </svg>
                </span> <!-- X icon -->
            </button>

            <button type="button" class="font-semibold" @click="openMobileMenu = !openMobileMenu; openSearch = false">
                <span :class="{ 'hidden': openMobileMenu }">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="#fff" class="w-5 h-5">
                        <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
                    </svg>
                </span> <!-- Hamburger icon -->

                <span :class="{ 'hidden': !openMobileMenu }">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="#fff" class="w-5 h-5">
                        <path d="M55.1 73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L147.2 256 9.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192.5 301.3 329.9 438.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.8 256 375.1 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192.5 210.7 55.1 73.4z"/>
                    </svg>
                </span> <!-- X icon -->
            </button>
        </div>

        <nav class="px-6 absolute right-0 top-16 hidden w-full flex-col rounded border shadow border-slate-800 bg-slate-900/95 backdrop-blur-sm"
            :class="{ 'hidden': !openMobileMenu, 'flex': openMobileMenu }">
            <a href="{{ route('home') }}" class="my-2 font-semibold transition hover:text-indigo-400">
                Home
            </a>

            <a href="{{ route('dramas') }}" class="my-2 font-semibold transition hover:text-indigo-400">
                Dramas
            </a>

            <a href="{{ route('movies') }}" class="my-2 font-semibold transition hover:text-indigo-400">
                Movies
            </a>
        </nav>
    </div>

    <div class="hidden w-3/4 mx-auto my-4" :class="{'hidden': !openSearch}">
        <livewire:search />
    </div>
</header>
