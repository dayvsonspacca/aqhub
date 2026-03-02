<?php

namespace App\Providers;

use App\Actions\Contracts\CreateMapContract;
use App\Actions\Contracts\CreateMonsterContract;
use App\Actions\CreateMap;
use App\Actions\CreateMonster;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CreateMonsterContract::class, CreateMonster::class);
        $this->app->bind(CreateMapContract::class, CreateMap::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
