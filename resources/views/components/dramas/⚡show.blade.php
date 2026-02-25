<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $dramaId;
    public $drama = null;

    public $heroSectionLoaded = false;
    public $heroSection = null;

    public $castSectionLoaded = false;
    public $castSection = null;

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
};
?>

<div>
    <div wire:init="loadHeroSection">
        <div wire:cloak wire:show="!heroSectionLoaded">
            <livewire:dramas.heroloader />
        </div>

        <div wire:cloak wire:show="heroSectionLoaded">
            @if ($heroSection)
                <livewire:dramas.hero :heroSection="$heroSection" />
            @endif
        </div>
    </div>

    <div wire:init="loadCastSection" class="mt-16">
        <livewire:dramas.cast :castSection="$castSection" :castSectionLoaded="$castSectionLoaded" />
    </div>
</div>
