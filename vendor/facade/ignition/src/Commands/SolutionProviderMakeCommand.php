<?php

namespace Facade\Ignition\Commands;

use Illuminate\Console\GeneratorCommand;

class SolutionProviderMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ignition:make-solution-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom Ignition solution provider class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Solution Provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/solution-provider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\SolutionProviders';
    }
}
