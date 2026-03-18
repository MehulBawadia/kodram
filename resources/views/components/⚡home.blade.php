<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {

    public $sliders = [];
    public $slidersLoaded = false;

    public $popularTv = [];
    public $popularTvLoaded = false;

    public $latestTv = [];
    public $latestTvLoaded = false;

    public $popularMovies = [];
    public $popularMoviesLoaded = false;

    public $latestMovies = [];
    public $latestMoviesLoaded = false;

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

    public function loadLatestTv()
    {
        $data = app(TMDBService::class)
            ->getLatestTvSeries();

        $this->latestTv = collect($data['results'] ?? [])
            ->filter(fn ($item) => !blank($item['poster_path']))
            ->take(20)
            ->values()
            ->toArray();

        $this->latestTvLoaded = true;
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

    public function loadLatestMovies()
    {
        $data = app(TMDBService::class)
            ->getLatestMovies();

        $this->latestMovies = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['poster_path']))
            ->take(20)
            ->values()
            ->toArray();

        $this->latestMoviesLoaded = true;
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
        <h2 class="text-2xl font-bold text-white mb-6">
            Popular TV Shows
        </h2>

        <div wire:show="!popularTvLoaded">
            <livewire:common.cardloader />
        </div>

        <div wire:show="popularTvLoaded">
            <livewire:common.tvcard :shows="$popularTv" />
        </div>
    </div>

    <div wire:init="loadLatestTv" class="mt-16">
        <h2 class="text-2xl font-bold text-white mb-6">
            Latest Finished TV Shows
        </h2>

        <div wire:show="!latestTvLoaded">
            <livewire:common.cardloader />
        </div>

        <div wire:show="latestTvLoaded">
            <livewire:common.tvcard :shows="$latestTv" />
        </div>
    </div>

    <div wire:init="loadPopularMovies" class="mt-32">
        <h2 class="text-2xl font-bold text-white mb-6">
            Popular Movies
        </h2>

        <div wire:show="!popularMoviesLoaded">
            <livewire:common.cardloader />
        </div>

        <div wire:show="popularMoviesLoaded">
            <livewire:common.moviecard :shows="$popularMovies" />
        </div>
    </div>

    <div wire:init="loadLatestMovies" class="mt-16">
        <h2 class="text-2xl font-bold text-white mb-6">
            Latest Finished Movies
        </h2>

        <div wire:show="!latestMoviesLoaded">
            <livewire:common.cardloader />
        </div>

        <div wire:show="latestMoviesLoaded">
            <livewire:common.moviecard :shows="$latestMovies" />
        </div>
    </div>
</div>
