<?php

namespace App\Modules\Core;


use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadCommands();
        $this->loadSettings();
    }

    public function register()
    {
        //
    }

    public function loadSettings()
    {
        //
    }

    public function loadCommands()
    {
        $this->commands([
            \App\Modules\Core\Commands\ModuleGenerator::class,
        ]);
    }
}
