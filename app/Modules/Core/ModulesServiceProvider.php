<?php

namespace App\Modules\Core;

/**
 * ServiceProvider
 *
 * The service provider for the modules. After being registered
 * it will make sure that each of the modules are properly loaded
 * i.e. with their routes, views etc.
 *
 * @author kundan Roy <query@programmerlab.com>
 * @package App\Modules
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{

    /**
     * Will make sure that the required modules have been fully loaded
     *
     * @return void routeModule
     */
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
