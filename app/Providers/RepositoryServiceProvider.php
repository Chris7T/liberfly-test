<?php

namespace App\Providers;

use App\Repositories\Item\ItemEloquentRepository;
use App\Repositories\Item\ItemInterfaceRepository;
use App\Repositories\User\UserEloquentRepository;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterfaceRepository::class, UserEloquentRepository::class);
        $this->app->bind(ItemInterfaceRepository::class, ItemEloquentRepository::class);
    }
}
