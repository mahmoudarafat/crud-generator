<?php

namespace AlBadrSystems\Generator\Commands\Common;

use AlBadrSystems\Generator\Commands\BaseCommand;
use AlBadrSystems\Generator\Common\CommandData;
use AlBadrSystems\Generator\Generators\RepositoryGenerator;

class RepositoryGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'albadrsystems:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create repository command';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->generate();

        $this->performPostActions();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }
}
