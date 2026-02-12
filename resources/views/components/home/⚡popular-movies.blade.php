<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component
{
    public $items = [];

    public function mount(TMDBService $tmdb)
    {
        $data = $tmdb->getPopularMovies();

        $this->items = collect($data['results'] ?? [])
            ->filter(fn ($item) => !empty($item['poster_path']))
            ->take(12)
            ->values()
            ->toArray();
    }
};
?>

<section class="py-10">
    <x-section-header title="Popular Movies" />

    {{-- Horizontal Scroll --}}
    <div class="relative">
        <div class="absolute right-0 top-0 bottom-0 w-10 bg-linear-to-l from-slate-950 to-transparent pointer-events-none"></div>

        <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory px-6 pb-4 no-scrollbar">
            @foreach($items as $item)
                <x-content-card :item="$item" type="movie" />
            @endforeach
        </div>
    </div>
</section>
