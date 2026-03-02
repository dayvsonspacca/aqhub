<?php

use App\Http\Controllers\AqwGamefilesProxyController;
use Illuminate\Support\Facades\Route;

Route::livewire('/monsters', 'pages::monsters.index');
Route::livewire('/monsters/{monster}', 'pages::monsters.info');

Route::livewire('/maps', 'pages::maps.index');
Route::livewire('/maps/{map}', 'pages::maps.info');

Route::livewire('/regions', 'pages::regions.index');
Route::livewire('/regions/{region}', 'pages::regions.info');

Route::get('/proxy/swf/monster/{filename}', [AqwGamefilesProxyController::class, 'monster'])
    ->where('filename', '.*\.swf');
