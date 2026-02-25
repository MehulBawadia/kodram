<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $dramaId;
    public $drama = null;

    public $heroSectionLoaded = false;
    public $heroSection = null;

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
</div>
