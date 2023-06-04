<?php

namespace BugHive;

use Monolog\Logger;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use BugHive\Commands\TestCommand;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        // Publish configuration file
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../config/buggerHive.php' => config_path('bughive.php'),
            ]);
        }

        // Register views
        $this->app['view']->addNamespace('bughive', __DIR__ . '/../resources/views');

        // Register facade
        if (class_exists(\Illuminate\Foundation\AliasLoader::class)) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('BugHive', 'BugHive\Facade');
        }
          // Register commands
          $this->commands([
            TestCommand::class,
        ]);
        // Map any routes
        $this->mapLaraBugApiRoutes();

        // Create an alias to the larabug-js-client.blade.php include
        Blade::include('bughive::bughive-js-client', 'bughiveJavaScriptClient');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/buggerhive.php', 'bughive');

        $this->app->singleton('bughive', function ($app) {
            return new BugHive(new \BugHive\Http\Client(
                config('bughive.login_key', 'login_key'),
                config('bughive.project_key', 'project_key')
            ));
        });

        if ($this->app['log'] instanceof \Illuminate\Log\LogManager) {
            $this->app['log']->extend('bughive', function ($app, $config) {
                $handler = new \BugHive\Logger\BugHiveHandler(
                    $app['bughive']
                );

                return new Logger('bughive', [$handler]);
            });
        }
    }

    protected function mapLaraBugApiRoutes()
    {
        Route::group(
            [
                'namespace' => '\BugHive\Http\Controllers',
                'prefix' => 'bughive-api'
            ],
            function ($router) {
                require __DIR__ . '/../routes/api.php';
            }
        );
    }
}