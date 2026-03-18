<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {

    public $loadingMore = false;

    public $movies = [];
    public $availableGenres = [];

    public $year = null;
    public $genres = [];
    public $sort = 'popularity.desc';

    public $page = 1;
    public $hasMorePages = true;

    public $refreshing = false;

    public function mount()
    {
        $this->availableGenres = app(TMDBService::class)
            ->getMoviesGenres()
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('components.⚡movies');
    }

    public function loadInitial()
    {
        $this->page = 1;
        $this->hasMorePages = true;

        $this->loadMovies(true);
    }

    public function loadMovies($replace = false)
    {
        if (!$this->hasMorePages) return;

        $this->loadingMore = true;

        $data = app(TMDBService::class)
            ->discoverMovies(
                year: $this->year,
                genres: $this->genres,
                sort: $this->sort,
                page: $this->page,
            );

        $results = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['poster_path']))
            ->values()
            ->toArray();

        if ($replace) {
            $this->movies = $results;
        } else {
            $this->movies = array_merge($this->movies, $results);
        }

        $this->hasMorePages = $this->page < ($data['total_pages'] ?? 1);
        $this->page++;

        $this->loadingMore = false;
    }

    public function updated($property)
    {
        $this->refreshing = true;

        $this->page = 1;
        $this->hasMorePages = true;

        $this->loadMovies(true);

        $this->refreshing = false;
    }
};
?>

<div x-data x-init="$nextTick(() => $wire.loadMovies(true))">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-white mb-6">
            Browse Movies
        </h2>

        <div class="space-y-6">
            <div class="flex flex-wrap gap-4">
                <select wire:model.live="year" class="bg-gray-800 border border-gray-700 text-gray-300 px-4 py-2 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="">All Time</option>
                    @for($y = today()->year; $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <select wire:model.live="sort" class="bg-gray-800 border border-gray-700 text-gray-300 px-4 py-2 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="popularity.asc">Popularity (Ascending)</option>
                    <option value="popularity.desc">Popularity (Descending)</option>
                    <option value="primary_release_date.asc">Release Date (Ascending)</option>
                    <option value="primary_release_date.desc">Release Date (Descending)</option>
                    <option value="vote_average.asc">Rating (Low -> High)</option>
                    <option value="vote_average.desc">Rating (High -> Low)</option>
                </select>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-400 mb-3 uppercase tracking-wide">
                    Genres
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($availableGenres as $genre)
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="checkbox" class="rounded bg-gray-800 border-gray-600 text-indigo-500 focus:ring-indigo-500" value="{{ $genre['id'] }}" wire:model.live="genres" />

                            <span class="text-sm text-gray-300 group-hover:text-white transition">
                                {{ $genre['name'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-10 relative">
            <div wire:show="refreshing || loadingMore">
                <livewire:common.cardloader />
            </div>

            <div wire:show="!refreshing && !loadingMore">
                <livewire:common.moviecard :shows="$movies" />
            </div>

            @if($hasMorePages)
                <div class="col-span-full mt-10" wire:key="loader-{{ $page }}" x-data x-intersect.once="$wire.loadMovies()"></div>
            @endif

            <div class="w-full" wire:loading wire:target="loadMovies">
                <livewire:common.cardloader />
            </div>
        </div>
    </div>
</div>
