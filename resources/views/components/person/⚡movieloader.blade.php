<?php

use Livewire\Component;

new class extends Component {};
?>

<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @for($i = 0; $i < 5; $i++)
            <div class="rounded-xl overflow-hidden shimmer">
                <div class="h-64"></div>
                <div class="p-4 space-y-3">
                    <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        @endfor
    </div>
</div>
