<?php

use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {

    #[Reactive]
    public $section;

    #[Reactive]
    public $episodes;

    #[Reactive]
    public $season;

    public function toggleSeason($seasonNumber)
    {
        $number = $this->season === $seasonNumber ? null : $seasonNumber;

        $this->dispatch("toggle-season", number: $number);
    }
};
?>

<section class="max-w-7xl mt-16 mx-auto">
    <h2 class="text-2xl font-semibold mb-8">Seasons</h2>

    <div class="space-y-4">
        @foreach($section as $item)
            @if($item['season_number'] > 0)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 transition-all duration-300 hover:bg-white/10 hover:scale-[1.01]">
                    <div class="flex justify-between items-center cursor-pointer" wire:click="toggleSeason({{ $item['season_number'] }})">
                        <div>
                            <h3 class="text-lg font-semibold">
                                {{ $item['name'] }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ $item['episode_count'] }} Episodes
                            </p>
                        </div>

                        <div class="text-indigo-400 text-xl">
                            {{ $season === $item['season_number'] ? '-' : '+' }}
                        </div>
                    </div>

                    @if($season === $item['season_number'])
                        <div class="mt-4 space-y-6">
                            @foreach($episodes as $episode)
                                <div class="bg-white/10 rounded-lg p-4">
                                    <div class="flex space-x-4 items-center">
                                        <h4 class="font-medium">
                                            {{ $episode['episode_number'] }}. {{ $episode['name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-400">
                                            {{ \Carbon\Carbon::parse($episode['air_date'])->format('M j, Y') ?? 'N/A' }}
                                        </p>
                                    </div>

                                    <p class="mt-4 tracking-wide leading-relaxed">{{ $episode['overview'] ?? 'No overview available.' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</section>
