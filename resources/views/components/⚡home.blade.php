<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {

    public $sliders = [];
    public $slidersLoaded = false;

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

    <livewire:home.popular-tv />
    <livewire:home.popular-movies />
</div>
