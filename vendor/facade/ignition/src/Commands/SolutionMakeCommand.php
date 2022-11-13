<?php

namespace Facade\Ignition\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class SolutionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ignition:make-solution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom Ignition solution class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Solution';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('runnable')
            ? __DIR__.'/stubs/runnable-solution.stub'
            : __DIR__.'/stubs/solution.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Solutions';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['runnable', null, InputOption::VALUE_NONE, 'Create runnable solution'],
        ];
    }
}
