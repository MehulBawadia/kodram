@props([
    'item',
    'type' => 'tv', // tv or movie
])

@php
    $title = $type === 'movie' ? $item['title'] ?? '' : $item['name'] ?? '';
    $image = $item['poster_path'] ?? null;
@endphp

<div class="w-36 shrink-0 snap-start">

    <a href="{{ url('/', [$type, $item['id']]) }}">
        <div class="relative group">
            {{-- Poster --}}
            @if($image)
                <img src="https://image.tmdb.org/t/p/w342{{ $image }}" alt="{{ $title }}" loading="lazy" class="rounded-2xl object-cover w-full h-52 transition duration-300 group-hover:scale-105" />
            @else
                <div class="w-full h-52 bg-slate-800 rounded-2xl"></div>
            @endif

            {{-- Rating --}}
            <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-md text-xs px-2 py-1 rounded-lg text-white">
                ⭐ {{ number_format($item['vote_average'] ?? 0, 1) }}
            </div>
        </div>

        {{-- Title --}}
        <p class="mt-2 text-sm text-white font-medium line-clamp-2">
            {{ $title }}
        </p>
    </a>
</div>
