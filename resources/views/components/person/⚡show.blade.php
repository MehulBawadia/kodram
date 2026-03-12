<?php

use Livewire\Component;
use App\Services\TMDBService;

new class extends Component
{
    public $personId;
    public $person = null;

    public $heroSectionLoaded = false;
    public $heroSection = null;

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
</div>
