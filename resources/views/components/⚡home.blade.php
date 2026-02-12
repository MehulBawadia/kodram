<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {

    public $sliders = [];
    public $slidersLoaded = false;

    public $popularTv = [];
    public $popularTvLoaded = false;

    public $popularMovies = [];
    public $popularMoviesLoaded = false;

    public function render()
    {
        return view('components.⚡home');
    }

    public function loadSliders()
    {
        $this->sliders = app(TMDBService::class)
            ->getSlidersForHomePage();

        $this->slidersLoaded = true;
    }

    public function loadPopularTv()
    {
        $data = app(TMDBService::class)
            ->getPopularTvSeries();

        $this->popularTv = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['poster_path']))
            ->take(10)
            ->values()
            ->toArray();

        $this->popularTvLoaded = true;
    }

    public function loadPopularMovies()
    {
        $data = app(TMDBService::class)
            ->getPopularMovies();

        $this->popularMovies = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['poster_path']))
            ->take(10)
            ->values()
            ->toArray();

        $this->popularMoviesLoaded = true;
    }
};
?>

<div class="relative">
    <div wire:init="loadSliders">
        <div class="h-[75vh] relative overflow-hidden">
            @if(!$slidersLoaded)
                <div class="absolute inset-0 shimmer">
                    <div class="absolute bottom-0 p-8 space-y-4 w-full max-w-2xl">
                        <div class="h-6 bg-gray-700 rounded w-32"></div>
                        <div class="h-12 bg-gray-700 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-700 rounded w-full"></div>
                        <div class="h-4 bg-gray-700 rounded w-5/6"></div>
                        <div class="h-10 bg-gray-700 rounded w-40 mt-6"></div>
                    </div>
                </div>
            @endif

            @if($slidersLoaded)
                <livewire:home.slider :items="$sliders" />
            @endif
        </div>
    </div>

    <div wire:init="loadPopularTv" class="mt-16">
        <div class="">
            <h2 class="text-2xl font-bold text-white mb-6">
                Popular TV Shows
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @if(!$popularTvLoaded)
                    @for($i = 0; $i < 5; $i++)
                        <div class="rounded-xl overflow-hidden shimmer">
                            <div class="h-64"></div>
                            <div class="p-4 space-y-3">
                                <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                            </div>
                        </div>
                    @endfor
                @endif

                @if($popularTvLoaded)
                    @foreach($popularTv as $show)
                        <div class="relative group rounded-2xl overflow-hidden cursor-pointer">
                            <img src="https://image.tmdb.org/t/p/w500{{ $show['poster_path'] }}" class="w-full aspect-2/3 object-cover transition duration-500 ease-out group-hover:scale-110" alt="{{ $show['name'] }}" />

                            <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-500 opacity-80 group-hover:opacity-100"></div>

                            <div class="absolute inset-0 flex flex-col justify-end p-5 text-white">
                                <div class="transform transition-all duration-500 ease-[cubic-bezier(.22,1,.36,1)] translate-y-2/3 group-hover:translate-y-0">
                                    <h3 class="font-bold text-lg leading-tight transition-all duration-300">
                                        {{ $show['name'] }}
                                    </h3>

                                    <div class="mt-3 text-sm text-gray-300 opacity-0 transition-all duration-500 delay-150 ease-out group-hover:opacity-100 group-hover:-translate-y-1 group-hover:scale-105 group-hover:brightness-110">
                                        <div class="flex items-center space-x-2">
                                            @if(!empty($show['first_air_date']))
                                                <span>{{ \Carbon\Carbon::parse($show['first_air_date'])->format('Y') }}</span>
                                            @endif
                                            <span>•</span>
                                            <span>⭐ {{ number_format($show['vote_average'], 1) }}</span>
                                        </div>

                                        @if(!empty($show['overview']))
                                            <p class="mt-2 text-xs line-clamp-3 text-gray-400">
                                                {{ $show['overview'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div wire:init="loadPopularMovies" class="mt-32">
        <div class="">
            <h2 class="text-2xl font-bold text-white mb-6">
                Popular Movies
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @if(!$popularMoviesLoaded)
                    @for($i = 0; $i < 5; $i++)
                        <div class="rounded-xl overflow-hidden shimmer">
                            <div class="h-64"></div>
                            <div class="p-4 space-y-3">
                                <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                            </div>
                        </div>
                    @endfor
                @endif

                @if($popularMoviesLoaded)
                    @foreach($popularMovies as $show)
                        <div class="relative group rounded-2xl overflow-hidden cursor-pointer">
                            <img src="https://image.tmdb.org/t/p/w500{{ $show['poster_path'] }}" class="w-full aspect-2/3 object-cover transition duration-500 ease-out group-hover:scale-110" alt="{{ $show['title'] }}" />

                            <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-500 opacity-80 group-hover:opacity-100"></div>

                            <div class="absolute inset-0 flex flex-col justify-end p-5 text-white">
                                <div class="transform transition-all duration-500 ease-[cubic-bezier(.22,1,.36,1)] translate-y-2/3 group-hover:translate-y-0">
                                    <h3 class="font-bold text-lg leading-tight transition-all duration-300">
                                        {{ $show['title'] }}
                                    </h3>

                                    <div class="mt-3 text-sm text-gray-300 opacity-0 transition-all duration-500 delay-150 ease-out group-hover:opacity-100 group-hover:-translate-y-1 group-hover:scale-105 group-hover:brightness-110">
                                        <div class="flex items-center space-x-2">
                                            @if(!empty($show['release_date']))
                                                <span>{{ \Carbon\Carbon::parse($show['release_date'])->format('Y') }}</span>
                                            @endif
                                            <span>•</span>
                                            <span>⭐ {{ number_format($show['vote_average'], 1) }}</span>
                                        </div>

                                        @if(!empty($show['overview']))
                                            <p class="mt-2 text-xs line-clamp-3 text-gray-400">
                                                {{ $show['overview'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
