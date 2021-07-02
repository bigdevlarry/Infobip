<?php

namespace App\Facade;


use Illuminate\Support\Facades\Facade;

class AppUtils extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'app-utils';
    }
}