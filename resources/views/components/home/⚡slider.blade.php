<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {
    public $items;

    public function mount(TMDBService $tmdb)
    {
        $this->items = $tmdb->getSlidersForHomePage();
        // dd($this->items);
    }
};
?>

<div class="relative w-full h-[75vh] overflow-hidden"
    wire:ignore
    x-data="sliderComponent({{ count($items) }})"
    x-init="init()"
>
    <div class="flex h-full transition-transform duration-700 ease-in-out"
         :style="`transform: translateX(-${active * 100}%);`">

        @foreach($items as $item)
            <div class="w-full shrink-0 relative">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://image.tmdb.org/t/p/original{{ $item['backdrop_path'] ?? $item['poster_path'] }}');"></div>

                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/40 to-transparent"></div>

                <!-- Content -->
                <div class="absolute bottom-0 w-full p-6 text-white z-10">
                    <div class="max-w-2xl space-y-4">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-3 py-1 rounded-full bg-indigo-500/90 text-white font-medium">
                                {{ strtoupper($item['type'] === 'tv' ? 'TV Series' : 'Movie') }}
                            </span>

                            <span class="flex items-center gap-1 text-yellow-300 font-semibold">
                                ⭐ {{ number_format($item['vote_average'], 1) }}
                            </span>

                            <span class="text-gray-300">
                                {{ \Carbon\Carbon::parse($item['first_air_date'] ?? $item['release_date'])->format('Y') ?? '' }}
                            </span>
                        </div>

                        <h1 class="text-3xl md:text-5xl font-bold leading-tight drop-shadow-lg">
                            {{ $item['title'] ?? $item['name'] }}
                        </h1>

                        <p class="text-sm md:text-base text-gray-200 line-clamp-3 max-w-xl">
                            {{ $item['overview'] }}
                        </p>

                        <div class="pt-3">
                            <a href="{{ url('/', ['type' => $item['type'], 'id' => $item['id']]) }}" class="inline-block px-8 py-3 rounded-lg font-semibold text-white bg-linear-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-105">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        @foreach($items as $index => $item)
            <button @click="go({{ $index }})"
                    class="w-3 h-3 rounded-full cursor-pointer"
                    :class="active === {{ $index }} ? 'bg-white' : 'bg-white/40'">
            </button>
        @endforeach
    </div>
</div>
