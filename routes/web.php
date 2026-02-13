<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/monsters', 'pages::monsters.index');
Route::livewire('/monsters/{monster}', 'pages::monsters.info');
