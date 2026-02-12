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
    <livewire:home.slider />

    <livewire:home.popular-tv />

    <livewire:home.popular-movies />
</div>
