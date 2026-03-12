<?php

use Livewire\Component;

new class extends Component
{
    public $castSection = null;
    public $castSectionLoaded = false;

    public function mount($castSection, $castSectionLoaded)
    {
        $this->castSection = $castSection;
        $this->castSectionLoaded = $castSectionLoaded;
    }
};
?>

<div>
    <h2 class="text-2xl font-bold text-white mb-6">
        Cast
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @if(!$castSectionLoaded)
            @for($i = 0; $i < 5; $i++)
                <div class="rounded-xl overflow-hidden shimmer">
                    <div class="h-64"></div>
                    <div class="p-4 space-y-3">
                        <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                    </div>
                </div>
            @endfor
        @endif

        @if($castSectionLoaded)
            @foreach($castSection as $person)
                <a href="{{ route('person.show', $person['id']) }}" class="relative group rounded-2xl overflow-hidden cursor-pointer">
                    <img src="https://image.tmdb.org/t/p/w500{{ $person['profile_path'] }}" class="w-full aspect-2/3 object-cover transition duration-500 ease-out group-hover:scale-110" alt="{{ $person['name'] }}" />

                    <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-500 opacity-80 group-hover:opacity-100"></div>

                    <div class="absolute inset-0 flex flex-col justify-end p-5 text-white">
                        <div class="transform transition-all duration-500 ease-[cubic-bezier(.22,1,.36,1)] translate-y-2/3 group-hover:translate-y-0">
                            <h3 class="font-bold text-lg leading-tight transition-all duration-300">
                                {{ $person['name'] }}
                            </h3>

                            <div class="mt-3 text-sm text-gray-300 opacity-0 transition-all duration-500 delay-150 ease-out group-hover:opacity-100 group-hover:-translate-y-1 group-hover:scale-105 group-hover:brightness-110">
                                Played as {{ $person['character'] }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</div>
