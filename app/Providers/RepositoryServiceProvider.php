<?php

namespace App\Providers;

use App\Repositories\UserEloquentRepository;
use App\Repositories\UserInterfaceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterfaceRepository::class, UserEloquentRepository::class);
    }
}
