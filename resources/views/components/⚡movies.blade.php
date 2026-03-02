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
            @if($refreshing)
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm z-20 flex items-center justify-center" wire:target="loadMovies">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 w-full px-4">
                        @for($i = 0; $i < 5; $i++)
                            <div class="rounded-2xl overflow-hidden shimmer">
                                <div class="h-64"></div>
                                <div class="p-4 space-y-3">
                                    <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($movies as $index => $show)
                    <a href="{{ route('movies.show', $show['id']) }}" class="relative group rounded-2xl overflow-hidden cursor-pointer" wire:key="drama-{{ $show['id'] }}-{{ $index }}">

                        <img src="https://image.tmdb.org/t/p/w500{{ $show['poster_path'] }}" alt="{{ $show['title'] }}" class="w-full aspect-2/3 object-cover transition duration-500 ease-out group-hover:scale-110" />

                        <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-500 opacity-80 group-hover:opacity-100"></div>

                        <div class="absolute inset-0 flex flex-col justify-end p-5 text-white">
                            <div class="transform transition-all duration-500 ease-[cubic-bezier(.22,1,.36,1)] translate-y-2/3 group-hover:translate-y-0">

                                <h3 class="font-bold text-lg leading-tight">
                                    {{ $show['title'] }}
                                </h3>

                                <div class="mt-3 text-sm text-gray-300 opacity-0 transition-all duration-500 delay-150 ease-out group-hover:opacity-100 group-hover:-translate-y-1 group-hover:scale-105 group-hover:brightness-110">
                                    <div class="flex items-center space-x-2">
                                        @if(!empty($show['release_date']))
                                            <span>{{ \Carbon\Carbon::parse($show['release_date'])->format('Y') }}</span>
                                        @endif

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
                    </a>
                @endforeach

                @if($hasMorePages)
                    <div class="col-span-full mt-10" wire:key="loader-{{ $page }}" x-data x-intersect.once="$wire.loadMovies()"></div>
                @endif

                <div wire:loading wire:target="loadMovies" class="col-span-full">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        @for($i = 0; $i < 5; $i++)
                            <div class="rounded-2xl overflow-hidden shimmer">
                                <div class="rounded-2xl overflow-hidden shimmer">
                                    <div class="h-64"></div>
                                    <div class="p-4 space-y-3">
                                        <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                                        <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
