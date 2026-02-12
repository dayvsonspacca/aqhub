<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::users.index');
Route::livewire('/enemies', 'pages::enemies.index');
