<?php

namespace App\Repositories\Tournament;

use Illuminate\Support\ServiceProvider;

class TournamentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TournamentInterface::class, TournamentRepository::class);
    }
}