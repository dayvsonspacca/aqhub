<?php

namespace App\Providers;

use App\Actions\Contracts\CreateMonsterContract;
use App\Actions\CreateMonster;
use App\AqwSocketClient\Factories\DefaultAqwClientFactory;
use App\AqwSocketClient\Interfaces\AqwAuthServiceInterface;
use App\AqwSocketClient\Interfaces\AqwClientFactoryInterface;
use App\AqwSocketClient\Services\HttpAqwAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AqwAuthServiceInterface::class, HttpAqwAuthService::class);
        $this->app->bind(AqwClientFactoryInterface::class, DefaultAqwClientFactory::class);
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
