<?php


namespace App\Repositories\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
    }
}