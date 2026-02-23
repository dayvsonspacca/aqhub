<?php

use App\Http\Controllers\AqwGamefilesProxyController;
use Illuminate\Support\Facades\Route;

Route::livewire('/monsters', 'pages::monsters.index');
Route::livewire('/monsters/{monster}', 'pages::monsters.info');

Route::get('/proxy/swf/monster/{filename}', [AqwGamefilesProxyController::class, 'monster'])
    ->where('filename', '.*\.swf');
