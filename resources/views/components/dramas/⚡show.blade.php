<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $dramaId;
    public $drama = null;

    public $openSeason = null;
    public $seasonEpisodes = [];

    public $heroSectionLoaded = false;
    public $heroSection = null;

    public $castSectionLoaded = false;
    public $castSection = null;

    public $seasonsSectionLoaded = false;
    public $seasonsSection = null;

    public function mount($id)
    {
        $this->dramaId = $id;
    }

    public function loadDrama()
    {
        $tmdb = app(TMDBService::class);

        $this->drama = $tmdb->getDramaDetails($this->dramaId);
    }

    public function loadHeroSection()
    {
        $this->heroSectionLoaded = false;
        $this->loadDrama();
        $this->heroSection = $this->drama;
        $this->heroSectionLoaded = true;
    }

    public function loadCastSection()
    {
        $this->castSectionLoaded = false;

       $this->castSection = collect($this->drama['credits']['cast'] ?? [])
            ->toArray();

        $this->castSectionLoaded = true;
    }

    public function loadSeasonSection()
    {
        $this->seasonsSectionLoaded = false;

       $this->seasonsSection = collect($this->drama['seasons'] ?? [])
            ->toArray();

        $this->seasonsSectionLoaded = true;
    }

    public function toggleSeason($seasonNumber)
    {
        $this->openSeason = $this->openSeason === $seasonNumber ? null : $seasonNumber;

        if ($this->openSeason) {
            $tmdb = app(TMDBService::class);
            $seasonDetails = $tmdb->getDramaSeasonDetails($this->dramaId, $seasonNumber);

            foreach ($this->drama['seasons'] as &$season) {
                if ($season['season_number'] === $seasonNumber) {
                    $this->seasonEpisodes = $seasonDetails['episodes'] ?? [];
                    break;
                }
            }
        }
    }
};
?>

<div>
    <div wire:init="loadHeroSection">
        <div wire:cloak wire:show="!heroSectionLoaded">
            <livewire:common.heroloader />
        </div>

        <div wire:cloak wire:show="heroSectionLoaded">
            @if ($heroSection)
                <livewire:dramas.hero :heroSection="$heroSection" />
            @endif
        </div>
    </div>

    <div wire:init="loadSeasonSection">
        <div wire:cloak wire:show="!seasonsSectionLoaded">
            <livewire:common.seasonloader />
        </div>

        <div wire:cloak wire:show="seasonsSectionLoaded">
            @if (!empty($drama['seasons']))
                <livewire:dramas.seasons :section="$seasonsSection" :episodes="$seasonEpisodes" :season="$openSeason" @toggleSeason="toggleSeason($event.detail.number);" />
            @endif
        </div>
    </div>

    <div wire:init="loadCastSection" class="mt-16">
        <div wire:cloak wire:show="!castSectionLoaded">
            <livewire:common.cardloader />
        </div>

        <div wire:cloak wire:show="castSectionLoaded">
            @if (! blank($castSection))
                <livewire:common.castcard :castSection="$castSection" :castSectionLoaded="$castSectionLoaded" />
            @endif
        </div>
    </div>
</div>
