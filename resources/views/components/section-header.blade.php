@props([
    'title',
    'link' => null,
])

<div class="flex items-center justify-between px-6 mb-6">
    <h2 class="text-lg font-semibold text-white">
        {{ $title }}
    </h2>

    @if ($link)
        <a href="{{ $link }}" class="text-sm text-indigo-400 font-medium">
            View All →
        </a>
    @endif
</div>
