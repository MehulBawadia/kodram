<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return view('components.⚡home');
    }
};
?>

<div>
    <livewire:home.hero />

    <livewire:home.trending-tv />
</div>
