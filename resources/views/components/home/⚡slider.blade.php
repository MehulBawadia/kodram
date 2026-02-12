<?php

use App\Services\TMDBService;
use Livewire\Component;

new class extends Component {
    public $items;

    public function mount(TMDBService $tmdb)
    {
        $this->items = $tmdb->getSlidersForHomePage();
    }
};
?>

<div
    x-data="{ active: 0, total: {{ count($items) }} }"
    x-init="setInterval(() => active = (active + 1) % total, 5000)"
    class="relative overflow-hidden"
>
    <div
        class="flex transition-transform duration-700 ease-in-out"
        :style="'transform: translateX(-' + (active * 100) + '%)'"
    >

        @foreach($items as $item)
            @php
                $title = $item['type'] === 'movie'
                    ? $item['title']
                    : $item['name'];
            @endphp

            <div class="min-w-full relative h-[70vh]">
                <img src="https://image.tmdb.org/t/p/w780{{ $item['backdrop_path'] }}" class="absolute inset-0 w-full h-full object-cover" />

                <div class="absolute inset-0 bg-linear-to-t from-black via-black/70 to-transparent"></div>

                <div class="absolute bottom-0 p-6 text-white space-y-3">
                    <span class="text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full uppercase tracking-wide">
                        {{ $item['type'] === 'movie' ? 'Movie' : 'Series' }}
                    </span>

                    <h1 class="mt-2 text-2xl font-bold">
                        {{ $title }}
                    </h1>

                    <div class="text-sm text-gray-300">
                        ⭐ {{ Number::abbreviate($item['vote_average'], 1) }}
                    </div>

                    <a href="{{ url('/', [$item['type'], $item['id']]) }}" class="inline-block mt-2 px-4 py-2 bg-linear-to-r from-indigo-500 to-purple-600 rounded-xl text-sm font-semibold">
                        View Details
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="absolute bottom-4 right-4 flex gap-2">
        <template x-for="i in total">
            <div class="w-2 h-2 rounded-full cursor-pointer transition-all"
                :class="active === i-1 ? 'bg-indigo-500' : 'bg-white/40'"
                @click="active = i-1"
            ></div>
        </template>
    </div>
</div>
