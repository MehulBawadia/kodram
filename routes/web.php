<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home')->name('home');
Route::livewire('/dramas', 'dramas')->name('dramas');
Route::livewire('/movies', 'movies')->name('movies');
