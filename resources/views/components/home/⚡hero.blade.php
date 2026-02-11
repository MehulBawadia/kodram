<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {
    public $hero;

    public function mount(TMDBService $tmdb)
    {
        $data = $tmdb->getPopularTvSeries();

        $results = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['backdrop_path']))
            ->sortByDesc('vote_average')
            ->values();

        $this->hero = $results->first();
    }
};
?>

<div class="relative h-[70vh] w-full overflow-hidden">

    {{-- Background Image --}}
    <img src="https://image.tmdb.org/t/p/w780{{ $hero['backdrop_path'] }}" alt="{{ $hero['name'] }}" class="absolute inset-0 w-full h-full object-cover transition duration-500" loading="lazy" />

    {{-- linear Overlay --}}
    <div class="absolute inset-0 bg-linear-to-t from-black via-black/70 to-transparent"></div>

    {{-- Content --}}
    <div class="absolute bottom-0 p-6 text-white space-y-4">

        {{-- Title --}}
        <h1 class="text-2xl font-bold leading-tight">
            {{ $hero['name'] }}
        </h1>

        {{-- Rating --}}
        <div class="flex items-center gap-2 text-sm">
            <span class="bg-indigo-500 px-2 py-1 rounded-lg text-xs font-semibold">
                ⭐ {{ number_format($hero['vote_average'], 1) }}
            </span>

            <span class="text-gray-300">
                {{ \Carbon\Carbon::parse($hero['first_air_date'])->year ?? '' }}
            </span>
        </div>

        {{-- Overview --}}
        <p class="text-sm text-gray-300 line-clamp-2">
            {{ $hero['overview'] }}
        </p>

        {{-- CTA Buttons --}}
        <div class="flex gap-3 pt-2">
            <a href="#" class="px-4 py-2 bg-linear-to-r from-indigo-500 to-purple-600 rounded-xl text-sm font-semibold">
                View Details
            </a>

            <a href="#" class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-sm">
                Browse
            </a>
        </div>
    </div>
</div>
