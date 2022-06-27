<?php

namespace AlBadrSystems\Generator\Commands;

use Illuminate\Console\Command;
use AlBadrSystems\Generator\Common\CommandData;
use AlBadrSystems\Generator\Generators\API\APIControllerGenerator;
use AlBadrSystems\Generator\Generators\API\APIRequestGenerator;
use AlBadrSystems\Generator\Generators\API\APIRoutesGenerator;
use AlBadrSystems\Generator\Generators\API\APITestGenerator;
use AlBadrSystems\Generator\Generators\FactoryGenerator;
use AlBadrSystems\Generator\Generators\MigrationGenerator;
use AlBadrSystems\Generator\Generators\ModelGenerator;
use AlBadrSystems\Generator\Generators\RepositoryGenerator;
use AlBadrSystems\Generator\Generators\RepositoryTestGenerator;
use AlBadrSystems\Generator\Generators\Scaffold\ControllerGenerator;
use AlBadrSystems\Generator\Generators\Scaffold\MenuGenerator;
use AlBadrSystems\Generator\Generators\Scaffold\RequestGenerator;
use AlBadrSystems\Generator\Generators\Scaffold\RoutesGenerator;
use AlBadrSystems\Generator\Generators\Scaffold\ViewGenerator;
use AlBadrSystems\Generator\Utils\FileUtil;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RollbackGeneratorCommand extends Command
{
    /**
     * The command Data.
     *
     * @var CommandData
     */
    public $commandData;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'albadrsystems:rollback';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback a full CRUD API and Scaffold for given model';

    /**
     * @var Composer
     */
    public $composer;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->composer = app()['composer'];
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->argument('type');
        if (!in_array($type, [
            CommandData::$COMMAND_TYPE_API,
            CommandData::$COMMAND_TYPE_SCAFFOLD,
            CommandData::$COMMAND_TYPE_API_SCAFFOLD,
        ])) {
            $this->error('invalid rollback type');
        }

        $this->commandData = new CommandData($this, $this->argument('type'));
        $this->commandData->fireEvent($type, FileUtil::FILE_DELETING);
        $this->commandData->config->mName = $this->commandData->modelName = $this->argument('model');

        $this->commandData->config->init($this->commandData, ['tableName', 'prefix', 'plural', 'views']);

        $views = $this->commandData->getOption('views');
        if (!empty($views)) {
            $views = explode(',', $views);
            $viewGenerator = new ViewGenerator($this->commandData);
            $viewGenerator->rollback($views);

            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();
            $this->commandData->fireEvent($type, FileUtil::FILE_DELETED);

            return;
        }

        $migrationGenerator = new MigrationGenerator($this->commandData);
        $migrationGenerator->rollback();

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->rollback();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->rollback();

        $requestGenerator = new APIRequestGenerator($this->commandData);
        $requestGenerator->rollback();

        $controllerGenerator = new APIControllerGenerator($this->commandData);
        $controllerGenerator->rollback();

        $routesGenerator = new APIRoutesGenerator($this->commandData);
        $routesGenerator->rollback();

        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->rollback();

        $controllerGenerator = new ControllerGenerator($this->commandData);
        $controllerGenerator->rollback();

        $viewGenerator = new ViewGenerator($this->commandData);
        $viewGenerator->rollback();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->rollback();

        if ($this->commandData->getAddOn('tests')) {
            $repositoryTestGenerator = new RepositoryTestGenerator($this->commandData);
            $repositoryTestGenerator->rollback();

            $apiTestGenerator = new APITestGenerator($this->commandData);
            $apiTestGenerator->rollback();
        }

        $factoryGenerator = new FactoryGenerator($this->commandData);
        $factoryGenerator->rollback();

        if ($this->commandData->config->getAddOn('menu.enabled')) {
            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->rollback();
        }

        $this->info('Generating autoload files');
        $this->composer->dumpOptimized();

        $this->commandData->fireEvent($type, FileUtil::FILE_DELETED);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['tableName', null, InputOption::VALUE_REQUIRED, 'Table Name'],
            ['prefix', null, InputOption::VALUE_REQUIRED, 'Prefix for all files'],
            ['plural', null, InputOption::VALUE_REQUIRED, 'Plural Model name'],
            ['views', null, InputOption::VALUE_REQUIRED, 'Views to rollback'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'Singular Model name'],
            ['type', InputArgument::REQUIRED, 'Rollback type: (api / scaffold / api_scaffold)'],
        ];
    }
}
