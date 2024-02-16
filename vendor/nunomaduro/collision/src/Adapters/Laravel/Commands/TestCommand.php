<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel\Commands;

use Dotenv\Exception\InvalidPathException;
use Dotenv\Parser\Parser;
use Dotenv\Store\StoreBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use NunoMaduro\Collision\Adapters\Laravel\Exceptions\RequirementsException;
use NunoMaduro\Collision\Coverage;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

/**
 * @internal
 *
 * @final
 */
class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test
        {--without-tty : Disable output to TTY}
        {--coverage : Indicates whether code coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for code coverage}
        {--p|parallel : Indicates if the tests should run in parallel}
        {--recreate-databases : Indicates if the test databases should be re-created}
        {--drop-databases : Indicates if the test databases should be dropped}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $phpunitVersion = \PHPUnit\Runner\Version::id();

        if ((int) $phpunitVersion[0] === 1) {
            throw new RequirementsException('Running PHPUnit 10.x or Pest 2.x requires Collision 7.x.');
        }

        if ((int) $phpunitVersion[0] < 9) {
            throw new RequirementsException('Running Collision 6.x artisan test command requires at least PHPUnit 9.x.');
        }

        $laravelVersion = (int) \Illuminate\Foundation\Application::VERSION;

        // @phpstan-ignore-next-line
        if ($laravelVersion < 9) {
            throw new RequirementsException('Running Collision 6.x artisan test command requires at least Laravel 9.x.');
        }

        if ($this->option('coverage') && ! Coverage::isAvailable()) {
            $this->output->writeln(sprintf(
                "\n  <fg=white;bg=red;options=bold> ERROR </> Code coverage driver not available.%s</>",
                Coverage::usingXdebug()
                    ? " Did you set <href=https://xdebug.org/docs/code_coverage#mode>Xdebug's coverage mode</>?"
                    : ''
            ));

            $this->newLine();

            return 1;
        }

        if ($this->option('parallel') && ! $this->isParallelDependenciesInstalled()) {
            if (! $this->confirm('Running tests in parallel requires "brianium/paratest". Do you wish to install it as a dev dependency?')) {
                return 1;
            }

            $this->installParallelDependencies();
        }

        $options = array_slice($_SERVER['argv'], $this->option('without-tty') ? 3 : 2);

        $this->clearEnv();

        $parallel = $this->option('parallel');

        $process = (new Process(array_merge(
            // Binary ...
            $this->binary(),
            // Arguments ...
            $parallel ? $this->paratestArguments($options) : $this->phpunitArguments($options)
        ),
            null,
            // Envs ...
            $parallel ? $this->paratestEnvironmentVariables() : $this->phpunitEnvironmentVariables(),
        ))->setTimeout(null);

        try {
            $process->setTty(! $this->option('without-tty'));
        } catch (RuntimeException $e) {
            $this->output->writeln('Warning: '.$e->getMessage());
        }

        $exitCode = 1;

        try {
            $exitCode = $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }

        if ($exitCode === 0 && $this->option('coverage')) {
            if (! $this->usingPest() && $this->option('parallel')) {
                $this->newLine();
            }

            $coverage = Coverage::report($this->output);

            $exitCode = (int) ($coverage < $this->option('min'));

            if ($exitCode === 1) {
                $this->output->writeln(sprintf(
                    "\n  <fg=white;bg=red;options=bold> FAIL </> Code coverage below expected:<fg=red;options=bold> %s %%</>. Minimum:<fg=white;options=bold> %s %%</>.",
                    number_format($coverage, 1),
                    number_format((float) $this->option('min'), 1)
                ));
            }
        }

        $this->newLine();

        return $exitCode;
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        if ($this->usingPest()) {
            $command = $this->option('parallel') ? ['vendor/pestphp/pest/bin/pest', '--parallel'] : ['vendor/pestphp/pest/bin/pest'];
        } else {
            $command = $this->option('parallel') ? ['vendor/brianium/paratest/bin/paratest'] : ['vendor/phpunit/phpunit/phpunit'];
        }

        if ('phpdbg' === PHP_SAPI) {
            return array_merge([PHP_BINARY, '-qrr'], $command);
        }

        return array_merge([PHP_BINARY], $command);
    }

    /**
     * Gets the common arguments of PHPUnit and Pest.
     *
     * @return array
     */
    protected function commonArguments()
    {
        $arguments = [];

        if ($this->option('coverage')) {
            $arguments[] = '--coverage-php';
            $arguments[] = Coverage::getPath();
        }

        return $arguments;
    }

    /**
     * Determines if Pest is being used.
     *
     * @return bool
     */
    protected function usingPest()
    {
        return class_exists(\Pest\Laravel\PestServiceProvider::class);
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = array_merge(['--printer=NunoMaduro\\Collision\\Adapters\\Phpunit\\Printer'], $options);

        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=')
                && $option != '-q'
                && $option != '--quiet'
                && $option != '--coverage'
                && ! Str::startsWith($option, '--min');
        }));

        if (! file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }

        return array_merge($this->commonArguments(), ["--configuration=$file"], $options);
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param  array  $options
     * @return array
     */
    protected function paratestArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=')
                && $option != '--coverage'
                && $option != '-q'
                && $option != '--quiet'
                && ! Str::startsWith($option, '--min')
                && ! Str::startsWith($option, '-p')
                && ! Str::startsWith($option, '--parallel')
                && ! Str::startsWith($option, '--recreate-databases')
                && ! Str::startsWith($option, '--drop-databases');
        }));

        if (! file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }

        return array_merge($this->commonArguments(), [
            "--configuration=$file",
            "--runner=\Illuminate\Testing\ParallelRunner",
        ], $options);
    }

    /**
     * Get the array of environment variables for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitEnvironmentVariables()
    {
        return [];
    }

    /**
     * Get the array of environment variables for running Paratest.
     *
     * @return array
     */
    protected function paratestEnvironmentVariables()
    {
        return [
            'LARAVEL_PARALLEL_TESTING' => 1,
            'LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES' => $this->option('recreate-databases'),
            'LARAVEL_PARALLEL_TESTING_DROP_DATABASES' => $this->option('drop-databases'),
        ];
    }

    /**
     * Clears any set Environment variables set by Laravel if the --env option is empty.
     *
     * @return void
     */
    protected function clearEnv()
    {
        if (! $this->option('env')) {
            $vars = self::getEnvironmentVariables(
                // @phpstan-ignore-next-line
                $this->laravel->environmentPath(),
                // @phpstan-ignore-next-line
                $this->laravel->environmentFile()
            );

            $repository = Env::getRepository();

            foreach ($vars as $name) {
                $repository->clear($name);
            }
        }
    }

    /**
     * @param  string  $path
     * @param  string  $file
     * @return array
     */
    protected static function getEnvironmentVariables($path, $file)
    {
        try {
            $content = StoreBuilder::createWithNoNames()
                ->addPath($path)
                ->addName($file)
                ->make()
                ->read();
        } catch (InvalidPathException $e) {
            return [];
        }

        $vars = [];

        foreach ((new Parser())->parse($content) as $entry) {
            $vars[] = $entry->getName();
        }

        return $vars;
    }

    /**
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isParallelDependenciesInstalled()
    {
        return class_exists(\ParaTest\Console\Commands\ParaTestCommand::class);
    }

    /**
     * Install parallel testing needed dependencies.
     *
     * @return void
     */
    protected function installParallelDependencies()
    {
        $command = $this->findComposer().' require brianium/paratest --dev';

        $process = Process::fromShellCommandline($command, null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }
        }

        try {
            $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        $composerPath = getcwd().'/composer.phar';

        if (file_exists($composerPath)) {
            return '"'.PHP_BINARY.'" '.$composerPath;
        }

        return 'composer';
    }
}
