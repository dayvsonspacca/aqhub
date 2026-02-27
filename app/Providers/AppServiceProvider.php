<?php

namespace App\Providers;

use App\Actions\Contracts\CreateMonsterContract;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
