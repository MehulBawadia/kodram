<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $movieId;
    public $movie = null;

    public $heroSectionLoaded = false;
    public $heroSection = null;

    public $castSectionLoaded = false;
    public $castSection = null;

    public function mount($id)
    {
        $this->movieId = $id;
    }

    public function loadMovie()
    {
        $tmdb = app(TMDBService::class);

        $this->movie = $tmdb->getMovieDetails($this->movieId);
    }

    public function loadHeroSection()
    {
        $this->heroSectionLoaded = false;
        $this->loadMovie();
        $this->heroSection = $this->movie;
        $this->heroSectionLoaded = true;
    }

    public function loadCastSection()
    {
        $this->castSectionLoaded = false;

       $this->castSection = collect($this->movie['credits']['cast'] ?? [])
            ->toArray();

        $this->castSectionLoaded = true;
    }
};
?>

<div>
    <div wire:init="loadHeroSection">
        <div wire:cloak wire:show="!heroSectionLoaded">
            <livewire:movies.heroloader />
        </div>

        <div wire:cloak wire:show="heroSectionLoaded">
            @if ($heroSection)
                <livewire:movies.hero :heroSection="$heroSection" />
            @endif
        </div>
    </div>

    <div wire:init="loadCastSection" class="mt-16">
        <livewire:movies.cast :castSection="$castSection" :castSectionLoaded="$castSectionLoaded" />
    </div>
</div>
