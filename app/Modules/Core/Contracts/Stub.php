<?php

namespace App\Modules\Core\Contracts;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait Stub
{

    protected $directories;

    protected $disk;

    protected $module;

    protected $modulePath;

    protected $mainModulePath;


    protected function setModulePath($module)
    {
        $this->modulePath .= $module . '/src/';
    }

    protected function setMainModulePath($module)
    {
        $this->mainModulePath .= $module;
    }

    protected function isModule($module)
    {
        return in_array($module, $this->directories);
    }

    protected function allModules()
    {
        return $this->directories;
    }

    protected function buildModuleDirectories()
    {
        $module  = $this->module;

        if ($this->isModule($module)) {
            $this->info('Module is already exists.');
            exit();
        }

        $source = $module . '/';
        $this->disk->makeDirectory($source);
        $this->disk->makeDirectory($source . 'Models');
        $this->disk->makeDirectory($source . 'routes');
        $this->disk->makeDirectory($source . 'Listeners');
        $this->disk->makeDirectory($source . 'Commands');
        $this->disk->makeDirectory($source . 'Jobs');
        $this->disk->makeDirectory($source . 'Resource');
        $this->disk->makeDirectory($source . 'Events');
        $this->disk->makeDirectory($source . 'Filters');
        $this->disk->makeDirectory($source . 'Actions');
        $this->disk->makeDirectory($source . 'config');
        $this->disk->makeDirectory($source . 'Http');
        $this->disk->makeDirectory($source . 'Http/Controllers');
        $this->disk->makeDirectory($source . 'Http/Requests');
        $this->disk->makeDirectory($source . 'database/migrations');
        $this->disk->makeDirectory($source . 'database/factories');
        $this->disk->makeDirectory($source . 'database/seeds');

        $this->info("{$this->module} module directory created successfuly.");
    }

    protected function rollback()
    {
        if ($this->isModule($this->module)) {
            $this->disk->deleteDirectory($this->module);

            $this->info("Process successfully rollback, module deleted.");
        }
    }

    protected function fileExists($path)
    {
        $module  = $this->module;
        $source = $module . '/src/';

        return $this->disk->exists($source . $path);
    }

    protected function getStub($type)
    {
        $vendorPath = '';
        if (!file_exists('core')) {
            $vendorPath = 'app/modules/';
        }

        return File::get(base_path($vendorPath . "core/stubs/$type.stub"));
    }

    protected function model()
    {
        $modelTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNamePluralLowerCase}}',
            ],
            [
                $this->module,
                strtolower(Str::plural($this->module)),
            ],
            $this->getStub('Model')
        );

        $this->createStubToFile("Models/{$this->module}.php", $modelTemplate);
    }

    protected function modelPivot($pivotName)
    {
        $modelTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{pivotSingularNamePascalCase}}',
                '{{pivotSingularNameLowerCase}}',
            ],
            [
                $this->module,
                Str::studly($pivotName),
                $pivotName,
            ],
            $this->getStub('Model')
        );

        $this->createStubToFile("Models/" . Str::studly($pivotName) . ".php", $modelTemplate);
    }

    protected function presenter()
    {
        $presenterTemplate = str_replace(
            ['{{moduleName}}'],
            [$this->module],
            $this->getStub('Presenter')
        );

        $this->createStubToFile("Presenters/{$this->module}Presenter.php", $presenterTemplate);
    }

    protected function config()
    {
        $moduleTemplate = str_replace(
            ['{{moduleName}}'],
            [$this->module],
            $this->getStub('Config')
        );

        $this->createStubToFile("config/" . strtolower($this->module) . ".php", $moduleTemplate);
    }


    protected function routes()
    {
        $routeTemplate = str_replace(
            ['{{moduleNameSingularLowerCase}}', '{{moduleName}}'],
            [strtolower(Str::kebab($this->module)), $this->module],
            $this->getStub('Route')
        );

        $this->createStubToFile(strtolower($this->module) . ".php", $routeTemplate);
    }

    protected function controller()
    {
        $controllerTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNamePluralLowerCase}}',
                '{{moduleNameSingularLowerCase}}'
            ],
            [
                $this->module,
                strtolower(Str::plural($this->module)),
                strtolower($this->module)
            ],
            $this->getStub('Controller')
        );

        $this->createStubToFile("Http/Controllers/{$this->module}Controller.php", $controllerTemplate);
    }

    protected function request()
    {
        $requestTemplate = str_replace(
            ['{{moduleName}}'],
            [$this->module],
            $this->getStub('Request')
        );

        $this->createStubToFile("Http/Requests/{$this->module}Request.php", $requestTemplate);
    }

    protected function observer()
    {
        $observerTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNameSingularLowerCase}}'
            ],
            [
                $this->module,
                strtolower($this->module)
            ],
            $this->getStub('Observer')
        );

        $this->createStubToFile("Models/{$this->module}Observer.php", $observerTemplate);
    }

    protected function resource($cloneToRoot = false)
    {
        $resourceStub = ($cloneToRoot) ? $this->getStub('RootResource') : $this->getStub('Resource');
        $resourceTemplate = str_replace(
            ['{{moduleName}}'],
            [$this->module],
            $resourceStub
        );

        if ($cloneToRoot) {
            $this->createToPath("Resource/{$this->module}/{$this->module}RootResource.php", $resourceTemplate);
        } else {
            $this->createStubToFile("Resource/{$this->module}Resource.php", $resourceTemplate);
        }
    }

    protected function serviceProvider()
    {
        $serviceProviderTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNamePluralLowerCase}}',
                '{{moduleNameSingularLowerCase}}'
            ],
            [
                $this->module,
                strtolower(Str::plural($this->module)),
                strtolower($this->module)
            ],
            $this->getStub('ServiceProvider')
        );

        $this->createStubToFile("{$this->module}ServiceProvider.php", $serviceProviderTemplate);
    }

    protected function authServiceProvider()
    {
        $authServiceProviderTemplate = str_replace(
            ['{{moduleName}}'],
            [$this->module],
            $this->getStub('AuthModuleServiceProvider')
        );

        $this->createStubToFile("{$this->module}AuthModuleServiceProvider.php", $authServiceProviderTemplate);
    }

    protected function policy()
    {
        $policyTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNameSingularLowerCase}}',
                '{{moduleNameSlug}}'
            ],
            [
                $this->module,
                strtolower($this->module),
                strtolower(Str::kebab($this->module))
            ],
            $this->getStub('Policy')
        );

        $this->createStubToFile("Models/Policies/{$this->module}Policy.php", $policyTemplate);
    }

    protected function gate()
    {
        $gateTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNameSlug}}'
            ],
            [
                $this->module,
                strtolower(Str::kebab($this->module))
            ],
            $this->getStub('Gate')
        );

        $this->createStubToFile("Models/Gates/{$this->module}Gate.php", $gateTemplate);
    }

    protected function registerModule()
    {
        $registerModuleTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNameSingularLowerCase}}'
            ],
            [
                $this->module,
                strtolower(Str::kebab($this->module))
            ],
            $this->getStub('RegisterModule')
        );

        $this->createStubToFile("Listeners/Register{$this->module}Module.php", $registerModuleTemplate);
    }

    protected function registerPermissionModule()
    {
        $registerPermissionModuleTemplate = str_replace(
            [
                '{{moduleName}}'
            ],
            [
                $this->module
            ],
            $this->getStub('RegisterPermissionModule')
        );

        $this->createStubToFile("Listeners/Register{$this->module}PermissionModule.php", $registerPermissionModuleTemplate);
    }

    protected function seed()
    {
        $this->info('Creating seeder file.');
        $seederTemplate = str_replace(
            [
                '{{moduleName}}'
            ],
            [
                $this->module
            ],
            $this->getStub('Seed')
        );

        $this->createStubToFile("database/seeds/{$this->module}TableSeeder.php", $seederTemplate);
    }

    protected function factory()
    {
        $this->info('Creating factory file.');
        $factoryTemplate = str_replace(
            [
                '{{moduleName}}'
            ],
            [
                $this->module
            ],
            $this->getStub('Factory')
        );

        $this->createStubToFile("database/factories/{$this->module}Factory.php", $factoryTemplate);
    }

    protected function migrate()
    {

        $module = strtolower($this->module);
        $migrationPath = 'modules/' . $this->modulePath . 'database/migrations';
        $modulePlural = Str::plural($module);

        $this->info('Creating a migration scripts.');
        Artisan::call("make:migration create_{$module}_table --path={$migrationPath} --create={$modulePlural}");

        $this->seed();
        $this->factory();
        // Artisan::call("make:seeder {$this->module}TableSeeder");
        // Artisan::call("make:factory {$this->module}Factory --model={$this->module}");

        $hasPivot = $this->anticipate('Is your table requires pivot table (Yes or No)?', ['Yes', 'No']);

        if (strtolower($hasPivot) === 'yes') {
            $pivot = $this->ask('Pivot to what Module(table name)?');

            if (!$this->isModule($pivot)) {
                $this->info("{$pivot} module does not exists.");
                exit();
            }

            $arrayNames = [strtolower($pivot), $module];
            sort($arrayNames);
            $pivotTableName = implode('_', $arrayNames);

            Artisan::call("make:migration create_{$pivotTableName}_table --path={$migrationPath} --create={$pivotTableName}");

            $this->modelPivot($pivotTableName);
        }

        $this->info('Migration script created.');
    }

    protected function event()
    {
        $observerEvents = ['Creating', 'Created', 'Saving', 'Saved', 'Updating', 'Updated'];

        foreach ($observerEvents as $events) {
            $eventTemplate = str_replace(
                [
                    '{{className}}',
                    '{{moduleName}}',
                ],
                [
                    $this->module . $events,
                    $this->module,
                ],
                $this->getStub('Event')
            );
            $this->createStubToFile("Events/{$this->module}{$events}.php", $eventTemplate);
        }
    }

    protected function eventServiceProvider()
    {
        $hasEventServiceProvider = $this->anticipate('Do you wish to create event service provider (Yes or No)?', ['Yes', 'No']);

        if (strtolower($hasEventServiceProvider) === 'yes') {

            $eventServiceProviderTemplate = str_replace(
                [
                    '{{moduleName}}',
                ],
                [
                    $this->module,
                ],
                $this->getStub('EventServiceProvider')
            );

            $this->createStubToFile("{$this->module}EventServiceProvider.php", $eventServiceProviderTemplate);

            $this->eventSubscriber();
        }
    }

    protected function eventSubscriber()
    {
        $eventSubscriberTemplate = str_replace(
            [
                '{{moduleName}}',
            ],
            [
                $this->module,
            ],
            $this->getStub('EventSubscriber')
        );

        $this->createStubToFile("Listeners/{$this->module}EventSubscriber.php", $eventSubscriberTemplate);
    }

    protected function command()
    {
        $hasCommand = $this->anticipate('Do you wish to create command (Yes or No)?', ['Yes', 'No']);

        if (strtolower($hasCommand) === 'yes') {
            $commandTemplate = str_replace(
                [
                    '{{moduleName}}',
                    '{{moduleNameSingularLowerCase}}'
                ],
                [
                    $this->module,
                    strtolower($this->module),
                ],
                $this->getStub('Command')
            );

            $this->createStubToFile("Commands/{$this->module}Command.php", $commandTemplate);
        }
    }

    protected function jobs()
    {
        $hasJob = $this->anticipate('Do you wish to create job (Yes or No)?', ['Yes', 'No']);

        if (strtolower($hasJob) === 'yes') {
            $jobTemplate = str_replace(
                [
                    '{{moduleName}}',
                    '{{moduleNameSingularLowerCase}}'
                ],
                [
                    $this->module,
                    strtolower($this->module),
                ],
                $this->getStub('Job')
            );

            $this->createStubToFile("Jobs/{$this->module}Job.php", $jobTemplate);
        }
    }

    public function pusherEvent($name)
    {
        $this->info('Notes:');
        $this->info('(1) Make sure to install pusher/pusher-php-server for the pusher event to work');
        $this->info('(2) Add PUSHER_APP_ID, PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_LOG credentials to your .env file');
        $this->info('(3) Update the value of BROADCAST_DRIVER to pusher');

        $pusherTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{pusherEventName}}',
                '{{pusherEventNameSlug}}'
            ],
            [
                $this->module,
                $name,
                strtolower(Str::kebab($name))
            ],
            $this->getStub('PusherEvent')
        );

        $this->createStubToFile("Events/{$name}.php", $pusherTemplate);
    }

    public function pusherEventJs($name)
    {
        $pusherEventJsTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{pusherEventName}}',
                '{{pusherEventNameSlug}}',
                '{{pusherEventNameLower}}'
            ],
            [
                $this->module,
                $name,
                strtolower(Str::kebab($name)),
                lcfirst($name)
            ],
            $this->getStub('PusherEventJs')
        );

        $this->createStubToFile("public/js/pusher/" . strtolower(Str::kebab($name)) . ".js", $pusherEventJsTemplate);
    }

    public function pusherMainJs()
    {
        $pusherMainJsTemplate = str_replace(
            [],
            [],
            $this->getStub('pusherMainJs')
        );

        $this->createStubToFile("public/js/pusher-main.js", $pusherMainJsTemplate);
    }

    protected function filter($name)
    {
        $filterTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{filterName}}'
            ],
            [
                $this->module,
                $name
            ],
            $this->getStub('Filter')
        );

        $this->createStubToFile("Filters/{$name}Filter.php", $filterTemplate);
    }

    protected function action($name, $namespace)
    {

        $filename = "Actions/{$name}Action.php";
        $files = $this->disk->files($this->modulePath . 'Actions');

        if (in_array($filename, $files)) {
            $this->info("{$filename} already exists, please enter a unique action name.");
            exit();
        }


        $filterTemplate = str_replace(
            [
                '{{actionName}}',
                '{{actionNameSingularLowerCase}}',
                '{{namespace}}'
            ],
            [
                $name,
                strtolower($name),
                $namespace
            ],
            $this->getStub('Action')
        );

        $this->createStubToFile($filename, $filterTemplate);
    }

    protected function createStubToFile($file, $template, $mainDirectory = false)
    {
        $path =  $this->module . '//';

        $this->disk->put(
            $path . $file,
            $template
        );

        $this->info("$file created successfuly.");
    }

    protected function createToPath($file, $template)
    {
        $this->disk->put(
            $file,
            $template
        );

        $this->info("{$this->disk->getAdapter()->getPathPrefix()}{$file} created successfuly");
    }

    protected function build()
    {
        $this->buildModuleDirectories();
        $this->model();
        $this->config();
        $this->routes();
        // $this->policy();
        // $this->gate();
        // $this->presenter();
        $this->controller();
        $this->request();
        $this->observer();
        $this->resource();
        $this->registerModule();
        $this->registerPermissionModule();
        $this->serviceProvider();
        // $this->authServiceProvider();
        $this->event();
        $this->eventServiceProvider();
        $this->jobs();
        $this->command();
        $this->migrate();
    }
}
