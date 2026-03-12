<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home')->name('home');
Route::livewire('/dramas', 'dramas')->name('dramas');
Route::livewire('/dramas/{id}', 'dramas.show')->name('dramas.show');
Route::livewire('/movies', 'movies')->name('movies');
Route::livewire('/movies/{id}', 'movies.show')->name('movies.show');
Route::livewire('/person/{id}', 'person.show')->name('person.show');
