<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $personId;
    public $person = null;

    public $heroSectionLoaded = false;
    public $heroSection = null;

    public $tvSeriesLoaded = false;
    public $tvSeries = null;

    public $moviesLoaded = false;
    public $movies = null;

    public function mount($id)
    {
        $this->personId = $id;
    }

    public function loadPerson()
    {
        $tmdb = app(TMDBService::class);

        $this->person = $tmdb->getPersonDetails($this->personId);
        // dd($this->person);
    }

    public function loadHeroSection()
    {
        $this->heroSectionLoaded = false;
        $this->loadPerson();
        $this->heroSection = $this->person;
        $this->heroSectionLoaded = true;
    }

    public function loadTvSeries()
    {
        $this->tvSeriesLoaded = false;

        $this->tvSeries = collect($this->person['tv_credits']['cast'])
            ->filter(function ($tv) {
                return ! Str::contains(cache('unwanted_tv_genres', ''), $tv['genre_ids']);
            })
            ->filter(function ($tv) {
                return ! blank($tv['backdrop_path']);
            })
            ->sortByDesc(function ($tv) {
                return \Carbon\Carbon::parse($tv['first_air_date'])->format('Y-m-d');
            })
            ->values()
            ->toArray();

        $this->tvSeriesLoaded = true;
    }

    public function loadMovies()
    {
        $this->moviesLoaded = false;

        $this->movies = collect($this->person['movie_credits']['cast'])
            ->filter(function ($tv) {
                return ! Str::contains(cache('unwanted_movie_genres', ''), $tv['genre_ids']);
            })
            ->filter(function ($tv) {
                return ! blank($tv['poster_path']);
            })
            ->filter(function ($tv) {
                return ! blank($tv['release_date']);
            })
            ->sortByDesc(function ($tv) {
                return \Carbon\Carbon::parse($tv['release_date'])->format('Y-m-d');
            })
            ->values()
            ->toArray();

        $this->moviesLoaded = true;
    }
};
?>

<div>
    <div wire:init="loadHeroSection">
        <div wire:cloak wire:show="!heroSectionLoaded">
            <livewire:person.heroloader />
        </div>

        <div wire:cloak wire:show="heroSectionLoaded">
            @if ($heroSection)
                <livewire:person.hero :heroSection="$heroSection" />
            @endif
        </div>
    </div>

    <div wire:init="loadTvSeries" class="mt-16">
        <h2 class="text-2xl font-bold text-white mb-6">
            TV Shows
        </h2>

        <div wire:cloak wire:show="!tvSeriesLoaded">
            <livewire:person.tvloader />
        </div>

        <div wire:cloak wire:show="tvSeriesLoaded">
            @if ($tvSeries)
                <livewire:person.tv :tvSeries="$tvSeries" />
            @endif
        </div>
    </div>

    <div wire:init="loadMovies" class="mt-16">
        <h2 class="text-2xl font-bold text-white mb-6">
            Movies
        </h2>

        <div wire:cloak wire:show="!moviesLoaded">
            <livewire:person.movieloader />
        </div>

        <div wire:cloak wire:show="moviesLoaded">
            @if ($movies)
                <livewire:person.movie :movies="$movies" />
            @endif
        </div>
    </div>
</div>
