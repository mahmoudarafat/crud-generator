<?php

namespace AlBadrSystems\Generator\Commands\Publish;

class PublishTemplateCommand extends PublishBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'alBadrsystems.publish:templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes api generator templates.';

    private $templatesDir;

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->templatesDir = config(
            'alBadrsystems.laravel_generator.path.templates_dir',
            resource_path('alBadrsystems/alBadrsystems-generator-templates/')
        );

        if ($this->publishGeneratorTemplates()) {
            $this->publishScaffoldTemplates();
            $this->publishSwaggerTemplates();
        }
    }

    /**
     * Publishes templates.
     */
    public function publishGeneratorTemplates()
    {
        $templatesPath = __DIR__.'/../../../templates';

        return $this->publishDirectory($templatesPath, $this->templatesDir, 'alBadrsystems-generator-templates');
    }

    /**
     * Publishes scaffold stemplates.
     */
    public function publishScaffoldTemplates()
    {
        $templateType = config('alBadrsystems.laravel_generator.templates', 'adminlte-templates');

        $templatesPath = get_templates_package_path($templateType).'/templates/scaffold';

        return $this->publishDirectory($templatesPath, $this->templatesDir.'scaffold', 'alBadrsystems-generator-templates/scaffold', true);
    }

    /**
     * Publishes swagger stemplates.
     */
    public function publishSwaggerTemplates()
    {
        $templatesPath = base_path('vendor/alBadrsystems/swagger-generator/templates');

        return $this->publishDirectory($templatesPath, $this->templatesDir, 'swagger-generator', true);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
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
