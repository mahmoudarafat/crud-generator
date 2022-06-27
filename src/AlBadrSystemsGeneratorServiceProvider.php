<?php

namespace AlBadrSystems\Generator;

use Illuminate\Support\ServiceProvider;
use AlBadrSystems\Generator\Commands\API\APIControllerGeneratorCommand;
use AlBadrSystems\Generator\Commands\API\APIGeneratorCommand;
use AlBadrSystems\Generator\Commands\API\APIRequestsGeneratorCommand;
use AlBadrSystems\Generator\Commands\API\TestsGeneratorCommand;
use AlBadrSystems\Generator\Commands\APIScaffoldGeneratorCommand;
use AlBadrSystems\Generator\Commands\Common\MigrationGeneratorCommand;
use AlBadrSystems\Generator\Commands\Common\ModelGeneratorCommand;
use AlBadrSystems\Generator\Commands\Common\RepositoryGeneratorCommand;
use AlBadrSystems\Generator\Commands\Publish\GeneratorPublishCommand;
use AlBadrSystems\Generator\Commands\Publish\LayoutPublishCommand;
use AlBadrSystems\Generator\Commands\Publish\PublishTemplateCommand;
use AlBadrSystems\Generator\Commands\Publish\PublishUserCommand;
use AlBadrSystems\Generator\Commands\RollbackGeneratorCommand;
use AlBadrSystems\Generator\Commands\Scaffold\ControllerGeneratorCommand;
use AlBadrSystems\Generator\Commands\Scaffold\RequestsGeneratorCommand;
use AlBadrSystems\Generator\Commands\Scaffold\ScaffoldGeneratorCommand;
use AlBadrSystems\Generator\Commands\Scaffold\ViewsGeneratorCommand;

class AlBadrSystemsGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__.'/../config/laravel_generator.php';
            $this->publishes([
                $configPath => config_path('albadrsystems/laravel_generator.php'),
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel_generator.php', 'albadrsystems.laravel_generator');

        $this->app->singleton('albadrsystems.publish', function ($app) {
            return new GeneratorPublishCommand();
        });

        $this->app->singleton('albadrsystems.api', function ($app) {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.publish.layout', function ($app) {
            return new LayoutPublishCommand();
        });

        $this->app->singleton('albadrsystems.publish.templates', function ($app) {
            return new PublishTemplateCommand();
        });

        $this->app->singleton('albadrsystems.api_scaffold', function ($app) {
            return new APIScaffoldGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.migration', function ($app) {
            return new MigrationGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.repository', function ($app) {
            return new RepositoryGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.api.controller', function ($app) {
            return new APIControllerGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.api.requests', function ($app) {
            return new APIRequestsGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.api.tests', function ($app) {
            return new TestsGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.scaffold.controller', function ($app) {
            return new ControllerGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.scaffold.requests', function ($app) {
            return new RequestsGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.scaffold.views', function ($app) {
            return new ViewsGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.rollback', function ($app) {
            return new RollbackGeneratorCommand();
        });

        $this->app->singleton('albadrsystems.publish.user', function ($app) {
            return new PublishUserCommand();
        });

        $this->commands([
            'albadrsystems.publish',
            'albadrsystems.api',
            'albadrsystems.scaffold',
            'albadrsystems.api_scaffold',
            'albadrsystems.publish.layout',
            'albadrsystems.publish.templates',
            'albadrsystems.migration',
            'albadrsystems.model',
            'albadrsystems.repository',
            'albadrsystems.api.controller',
            'albadrsystems.api.requests',
            'albadrsystems.api.tests',
            'albadrsystems.scaffold.controller',
            'albadrsystems.scaffold.requests',
            'albadrsystems.scaffold.views',
            'albadrsystems.rollback',
            'albadrsystems.publish.user',
        ]);
    }
}
