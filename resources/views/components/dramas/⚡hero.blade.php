<?php

use Livewire\Component;

new class extends Component
{
    public $heroSection = null;

    public function mount($heroSection)
    {
        $this->heroSection = $heroSection;
    }
};
?>

<section class="relative overflow-hidden">
    @if(!empty($heroSection['backdrop_path']))
        <div class="absolute inset-0">
            <img src="https://image.tmdb.org/t/p/original{{ $heroSection['backdrop_path'] }}" class="w-full h-full object-cover blur brightness-50 saturate-150 scale-110 transition duration-700" />
        </div>
    @endif

    <div class="absolute inset-0 bg-linear-to-t from-gray-950 via-gray-950/80 to-gray-950/40"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-6 grid md:grid-cols-3 gap-12 items-center">

        <div>
            @if(!empty($heroSection['poster_path']))
                <img src="https://image.tmdb.org/t/p/w500{{ $heroSection['poster_path'] }}" class="rounded-2xl shadow-2xl border border-white/10 transition duration-500 hover:scale-105" />
            @endif
        </div>

        <div class="md:col-span-2">
            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-indigo-500/10">

                <h1 class="text-4xl md:text-5xl font-bold tracking-tight">
                    {{ $heroSection['name'] }}
                </h1>

                @if(($heroSection['original_name'] ?? '') !== ($heroSection['name'] ?? ''))
                    <p class="text-gray-400 italic mt-2">
                        {{ $heroSection['original_name'] }}
                    </p>
                @endif

                <p class="text-gray-400 italic mt-2">
                    {{ $heroSection['tagline'] }}
                </p>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300 mt-6">
                    <span class="flex items-center gap-1 text-yellow-400 font-medium">
                        ★ {{ number_format($heroSection['vote_average'] ?? 0, 1) }}
                    </span>
                    <span>•</span>
                    <span>{{ substr($heroSection['first_air_date'] ?? '', 0, 4) }}</span>
                    <span>•</span>
                    <span>{{ $heroSection['number_of_episodes'] ?? 0 }} Episodes</span>
                </div>

                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach($heroSection['genres'] ?? [] as $genre)
                        <span class="px-3 py-1 text-xs bg-indigo-500/20 text-indigo-200 rounded-full border border-indigo-400/20">
                            {{ $genre['name'] }}
                        </span>
                    @endforeach
                </div>

                <p class="text-gray-300 leading-relaxed mt-6 max-w-3xl">
                    {{ $heroSection['overview'] ?? 'No description available.' }}
                </p>
            </div>
        </div>
    </div>
</section>
