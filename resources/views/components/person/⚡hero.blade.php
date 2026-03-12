<?php

use Livewire\Component;

new class extends Component
{
    public $heroSection = null;

    public $heroBgImage = null;
    public $gender = null;

    public function mount($heroSection)
    {
        $this->heroSection = $heroSection;

        $this->heroBgImage = collect($heroSection['tv_credits']['cast'])->filter(function ($tv) {
            return ! blank($tv['backdrop_path']);
        })->first()['backdrop_path'];

        $this->getGender();
    }

    public function getGender()
    {
        $this->gender = match ($this->heroSection['gender']) {
            0 => 'Not Set / Not Specified',
            1 => 'Female',
            2 => 'Male',
            3 => 'Non-Binary',
        };
    }
};
?>

<section class="relative overflow-hidden">
    @if(! blank($heroBgImage))
        <div class="absolute inset-0">
            <img src="https://image.tmdb.org/t/p/original{{ $heroBgImage }}" class="w-full h-full object-cover blur brightness-50 saturate-150 scale-110 transition duration-700" />
        </div>
    @endif

    <div class="absolute inset-0 bg-linear-to-t from-gray-950 via-gray-950/80 to-gray-950/40"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-6 grid md:grid-cols-3 gap-12 items-center">

        <div>
            @if(! blank($heroSection['profile_path']))
                <img src="https://image.tmdb.org/t/p/w500{{ $heroSection['profile_path'] }}" class="rounded-2xl shadow-2xl border border-white/10 transition duration-500 hover:scale-105" />
            @endif
        </div>

        <div class="md:col-span-2">
            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-indigo-500/10">

                <h1 class="text-4xl md:text-5xl font-bold tracking-tight">
                    {{ $heroSection['name'] }}
                </h1>

                @if(! blank($heroSection['also_known_as']))
                    <p class="text-gray-400 italic mt-2">
                        {{ implode(', ', $heroSection['also_known_as']) }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300 mt-6">
                    <span class="flex items-center gap-1 text-yellow-400 font-medium">
                        ★ {{ number_format($heroSection['popularity'] ?? 0, 1) }}
                    </span>
                    <span>•</span>
                    <span>Born: {{ Carbon\Carbon::parse($heroSection['birthday'])->format('dS M, Y') }} in {{ $heroSection['place_of_birth'] }}</span>
                    <span>•</span>
                    <span>{{ $gender }}</span>
                </div>

                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach($heroSection['genres'] ?? [] as $genre)
                        <span class="px-3 py-1 text-xs bg-indigo-500/20 text-indigo-200 rounded-full border border-indigo-400/20">
                            {{ $genre['name'] }}
                        </span>
                    @endforeach
                </div>

                <p class="text-gray-300 leading-relaxed mt-6 max-w-3xl">
                    {{ $heroSection['biography'] ?? 'No biography available.' }}
                </p>
            </div>
        </div>
    </div>
</section>
