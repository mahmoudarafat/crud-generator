<?php

namespace AlBadrSystems\Generator\Commands\Publish;

use AlBadrSystems\Generator\Utils\FileUtil;
use Symfony\Component\Console\Input\InputOption;

class GeneratorPublishCommand extends PublishBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'albadrsystems:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes & init api routes, base controller, base test cases traits.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
//         $this->updateRouteServiceProvider();
        $this->publishTestCases();
        $this->publishBaseController();
        $repositoryPattern = config('albadrsystems.laravel_generator.options.repository_pattern', true);
        if ($repositoryPattern) {
            $this->publishBaseRepository();
        }
        if ($this->option('localized')) {
            $this->publishLocaleFiles();
        }
    }

    /**
     * Replaces dynamic variables of template.
     *
     * @param string $templateData
     *
     * @return string
     */
    private function fillTemplate($templateData)
    {
        $apiVersion = config('albadrsystems.laravel_generator.api_version', 'v1');
        $apiPrefix = config('albadrsystems.laravel_generator.api_prefix', 'api');

        $templateData = str_replace('$API_VERSION$', $apiVersion, $templateData);
        $templateData = str_replace('$API_PREFIX$', $apiPrefix, $templateData);
        $appNamespace = $this->getLaravel()->getNamespace();
        $appNamespace = substr($appNamespace, 0, strlen($appNamespace) - 1);
        $templateData = str_replace('$NAMESPACE_APP$', $appNamespace, $templateData);

        return $templateData;
    }

    private function updateRouteServiceProvider()
    {
        $routeServiceProviderPath = app_path('Providers'.DIRECTORY_SEPARATOR.'RouteServiceProvider.php');

        if (!file_exists($routeServiceProviderPath)) {
            $this->error("Route Service provider not found on $routeServiceProviderPath");

            return 1;
        }

        $fileContent = file_get_contents($routeServiceProviderPath);

        $search = "Route::prefix('api')".PHP_EOL.str(' ')->repeat(16)."->middleware('api')";
        $beforeContent = str($fileContent)->before($search);
        $afterContent = str($fileContent)->after($search);

        $finalContent = $beforeContent.$search.PHP_EOL.str(' ')->repeat(16)."->as('api.')".$afterContent;
        file_put_contents($routeServiceProviderPath, $finalContent);

        return 0;
    }

    private function publishTestCases()
    {
        $testsPath = config('albadrsystems.laravel_generator.path.tests', base_path('tests/'));
        $testsNameSpace = config('albadrsystems.laravel_generator.namespace.tests', 'Tests');
        $createdAtField = config('albadrsystems.laravel_generator.timestamps.created_at', 'created_at');
        $updatedAtField = config('albadrsystems.laravel_generator.timestamps.updated_at', 'updated_at');

        $templateData = get_template('test.api_test_trait', 'crud-generator');

        $templateData = str_replace('$NAMESPACE_TESTS$', $testsNameSpace, $templateData);
        $templateData = str_replace('$TIMESTAMPS$', "['$createdAtField', '$updatedAtField']", $templateData);

        $fileName = 'ApiTestTrait.php';

        if (file_exists($testsPath.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }

        FileUtil::createFile($testsPath, $fileName, $templateData);
        $this->info('ApiTestTrait created');

        $testAPIsPath = config('albadrsystems.laravel_generator.path.api_test', base_path('tests/APIs/'));
        if (!file_exists($testAPIsPath)) {
            FileUtil::createDirectoryIfNotExist($testAPIsPath);
            $this->info('APIs Tests directory created');
        }

        $testRepositoriesPath = config('albadrsystems.laravel_generator.path.repository_test', base_path('tests/Repositories/'));
        if (!file_exists($testRepositoriesPath)) {
            FileUtil::createDirectoryIfNotExist($testRepositoriesPath);
            $this->info('Repositories Tests directory created');
        }
    }

    private function publishBaseController()
    {
        $templateData = get_template('app_base_controller', 'crud-generator');

        $templateData = $this->fillTemplate($templateData);

        $controllerPath = app_path('Http/Controllers/');

        $fileName = 'AppBaseController.php';

        if (file_exists($controllerPath.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }

        FileUtil::createFile($controllerPath, $fileName, $templateData);

        $this->info('AppBaseController created');
    }

    private function publishBaseRepository()
    {
        $templateData = get_template('base_repository', 'crud-generator');

        $templateData = $this->fillTemplate($templateData);

        $repositoryPath = app_path('Repositories/');

        FileUtil::createDirectoryIfNotExist($repositoryPath);

        $fileName = 'BaseRepository.php';

        if (file_exists($repositoryPath.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }

        FileUtil::createFile($repositoryPath, $fileName, $templateData);

        $this->info('BaseRepository created');
    }

    private function publishLocaleFiles()
    {
        $localesDir = __DIR__.'/../../../locale/';

        $this->publishDirectory($localesDir, resource_path('lang'), 'lang', true);

        $this->comment('Locale files published');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['localized', null, InputOption::VALUE_NONE, 'Localize files.'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
}
