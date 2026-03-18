<?php

use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    #[Reactive]
    public $shows;
};
?>

<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach($shows as $show)
            <a href="{{ route('dramas.show', $show['id']) }}" class="relative group rounded-2xl overflow-hidden cursor-pointer">
                <img src="https://image.tmdb.org/t/p/w500{{ $show['poster_path'] }}" class="w-full aspect-2/3 object-cover transition duration-500 ease-out group-hover:scale-110" alt="{{ $show['name'] }}" />

                <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-500 opacity-80 group-hover:opacity-100"></div>

                <div class="absolute inset-0 flex flex-col justify-end p-5 text-white">
                    <div class="transform transition-all duration-500 ease-[cubic-bezier(.22,1,.36,1)] translate-y-2/3 group-hover:translate-y-0">
                        <h3 class="font-bold text-lg leading-tight transition-all duration-300">
                            {{ $show['name'] }}
                        </h3>

                        <div class="mt-3 text-sm text-gray-300 opacity-0 transition-all duration-500 delay-150 ease-out group-hover:opacity-100 group-hover:-translate-y-1 group-hover:scale-105 group-hover:brightness-110">
                            <div class="flex items-center space-x-2">
                                @if(!empty($show['first_air_date']))
                                    <span>{{ \Carbon\Carbon::parse($show['first_air_date'])->format('Y') }}</span>
                                @endif
                                <span>•</span>
                                <span>⭐ {{ number_format($show['vote_average'], 1) }}</span>
                            </div>

                            @if(!empty($show['overview']))
                                <p class="mt-2 text-xs line-clamp-3 text-gray-400">
                                    {{ $show['overview'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
