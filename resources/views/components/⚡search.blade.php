<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {

    public $search = '';
    public $results = [];

    public function updated($property)
    {
        if(strlen($this->search) < 2){
            $this->results = [];
            return;
        }

        $tmdb = app(TMDBService::class);

        $this->results = collect($tmdb->search($this->search))
            ->filter(function ($result) {
                if ($result['media_type'] !== 'person') {
                    return $result['original_language'] === 'ko';
                }

                return $result['known_for_department'] === 'Acting' &&
                    ! blank($result['profile_path']);
            })
            ->values()
            ->toArray();
    }

};
?>

<div class="relative w-full">
    <input
        type="text"
        wire:model.live.debounce.1000ms="search"
        placeholder="Search movies, dramas, people..."
        class="pl-3 border w-full rounded-lg py-1.5 placeholder:text-gray-400 focus:ring-0 focus:outline-none"
    />

    @if(!empty($results))
        <div class="absolute mt-2 w-full rounded-xl bg-slate-900 border border-slate-800 shadow-xl">
            @foreach($results as $item)
                @if($item['media_type'] === 'movie')
                    <a href="{{ route('movies.show', $item['id']) }}" class="flex items-center gap-3 p-3 hover:bg-slate-800">
                @elseif($item['media_type'] === 'tv')
                    <a href="{{ route('dramas.show', $item['id']) }}" class="flex items-center gap-3 p-3 hover:bg-slate-800">
                @elseif($item['media_type'] === 'person')
                    <a href="{{ route('person.show', $item['id']) }}" class="flex items-center gap-3 p-3 hover:bg-slate-800">
                @endif

                    <img
                        src="https://image.tmdb.org/t/p/w92{{ $item['poster_path'] ?? $item['profile_path'] }}"
                        class="w-10 rounded"
                    />

                    <div class="text-sm">
                        <p class="font-bold text-white">
                            {{ $item['title'] ?? $item['name'] }}
                        </p>

                        <p class="text-xs mt-2 text-gray-400 uppercase">
                            {{ ucfirst($item['media_type']) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
