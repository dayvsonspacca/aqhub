<?php

namespace App\Providers;

use App\Actions\Contracts\CreateMapContract;
use App\Actions\Contracts\CreateMonsterContract;
use App\Actions\Contracts\UpsertQuestContract;
use App\Actions\CreateMap;
use App\Actions\CreateMonster;
use App\Actions\UpsertQuest;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Server;
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
        $this->app->bind(UpsertQuestContract::class, UpsertQuest::class);
        $this->app->bind(SocketClient::class, fn () => new SocketClient(Server::espada()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
