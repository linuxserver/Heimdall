<?php

namespace Facade\Ignition;

use Exception;
use Facade\FlareClient\Api;
use Facade\FlareClient\Flare;
use Facade\FlareClient\Http\Client;
use Facade\Ignition\Commands\SolutionMakeCommand;
use Facade\Ignition\Commands\SolutionProviderMakeCommand;
use Facade\Ignition\Commands\TestCommand;
use Facade\Ignition\Context\LaravelContextDetector;
use Facade\Ignition\DumpRecorder\DumpRecorder;
use Facade\Ignition\ErrorPage\IgnitionExceptionRenderer;
use Facade\Ignition\ErrorPage\IgnitionWhoopsHandler;
use Facade\Ignition\ErrorPage\Renderer;
use Facade\Ignition\Exceptions\InvalidConfig;
use Facade\Ignition\Http\Controllers\ExecuteSolutionController;
use Facade\Ignition\Http\Controllers\HealthCheckController;
use Facade\Ignition\Http\Controllers\ScriptController;
use Facade\Ignition\Http\Controllers\ShareReportController;
use Facade\Ignition\Http\Controllers\StyleController;
use Facade\Ignition\Http\Middleware\IgnitionConfigValueEnabled;
use Facade\Ignition\Http\Middleware\IgnitionEnabled;
use Facade\Ignition\JobRecorder\JobRecorder;
use Facade\Ignition\Logger\FlareHandler;
use Facade\Ignition\LogRecorder\LogRecorder;
use Facade\Ignition\Middleware\AddDumps;
use Facade\Ignition\Middleware\AddEnvironmentInformation;
use Facade\Ignition\Middleware\AddExceptionInformation;
use Facade\Ignition\Middleware\AddGitInformation;
use Facade\Ignition\Middleware\AddJobInformation;
use Facade\Ignition\Middleware\AddLogs;
use Facade\Ignition\Middleware\AddQueries;
use Facade\Ignition\Middleware\AddSolutions;
use Facade\Ignition\Middleware\SetNotifierName;
use Facade\Ignition\QueryRecorder\QueryRecorder;
use Facade\Ignition\SolutionProviders\BadMethodCallSolutionProvider;
use Facade\Ignition\SolutionProviders\DefaultDbNameSolutionProvider;
use Facade\Ignition\SolutionProviders\IncorrectValetDbCredentialsSolutionProvider;
use Facade\Ignition\SolutionProviders\InvalidRouteActionSolutionProvider;
use Facade\Ignition\SolutionProviders\LazyLoadingViolationSolutionProvider;
use Facade\Ignition\SolutionProviders\MergeConflictSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingAppKeySolutionProvider;
use Facade\Ignition\SolutionProviders\MissingColumnSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingImportSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingLivewireComponentSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingMixManifestSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingPackageSolutionProvider;
use Facade\Ignition\SolutionProviders\RunningLaravelDuskInProductionProvider;
use Facade\Ignition\SolutionProviders\SolutionProviderRepository;
use Facade\Ignition\SolutionProviders\TableNotFoundSolutionProvider;
use Facade\Ignition\SolutionProviders\UndefinedLivewireMethodSolutionProvider;
use Facade\Ignition\SolutionProviders\UndefinedLivewirePropertySolutionProvider;
use Facade\Ignition\SolutionProviders\UndefinedPropertySolutionProvider;
use Facade\Ignition\SolutionProviders\UndefinedVariableSolutionProvider;
use Facade\Ignition\SolutionProviders\UnknownValidationSolutionProvider;
use Facade\Ignition\SolutionProviders\ViewNotFoundSolutionProvider;
use Facade\Ignition\Support\SentReports;
use Facade\Ignition\Views\Engines\CompilerEngine;
use Facade\Ignition\Views\Engines\PhpEngine;
use Facade\IgnitionContracts\SolutionProviderRepository as SolutionProviderRepositoryContract;
use Illuminate\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Log\LogManager;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine as LaravelCompilerEngine;
use Illuminate\View\Engines\PhpEngine as LaravelPhpEngine;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;
use Livewire\CompilerEngineForIgnition;
use Monolog\Logger;
use Throwable;

class IgnitionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/flare.php' => config_path('flare.php'),
            ], 'flare-config');

            $this->publishes([
                __DIR__.'/../config/ignition.php' => config_path('ignition.php'),
            ], 'ignition-config');

            if (isset($_SERVER['argv']) && ['artisan', 'tinker'] === $_SERVER['argv']) {
                Api::sendReportsInBatches(false);
            }

            $this->app->make(JobRecorder::class)->register();
        }

        $this
            ->registerViewEngines()
            ->registerHousekeepingRoutes()
            ->registerLogHandler()
            ->registerCommands();

        if ($this->app->bound('queue')) {
            $this->setupQueue($this->app->get('queue'));
        }

        if (isset($_SERVER['LARAVEL_OCTANE'])) {
            $this->setupOctane();
        }

        if (config('flare.reporting.report_logs', true)) {
            $this->app->make(LogRecorder::class)->register();
        }

        if (config('flare.reporting.report_queries', true)) {
            $this->app->make(QueryRecorder::class)->register();
        }

        $this->app->make(DumpRecorder::class)->register();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/flare.php', 'flare');
        $this->mergeConfigFrom(__DIR__.'/../config/ignition.php', 'ignition');

        $this
            ->registerSolutionProviderRepository()
            ->registerRenderer()
            ->registerExceptionRenderer()
            ->registerIgnitionConfig()
            ->registerFlare()
            ->registerDumpCollector()
            ->registerJobRecorder();

        if (config('flare.reporting.report_logs', true)) {
            $this->registerLogRecorder();
        }

        if (config('flare.reporting.report_queries', true)) {
            $this->registerQueryRecorder();
        }

        if (config('flare.reporting.anonymize_ips')) {
            $this->app->get(Flare::class)->anonymizeIp();
        }

        $this->app->get(Flare::class)->censorRequestBodyFields(config('flare.reporting.censor_request_body_fields', ['password']));

        $this->registerBuiltInMiddleware();
    }

    protected function registerViewEngines()
    {
        if (! $this->hasCustomViewEnginesRegistered()) {
            return $this;
        }

        $this->app->make('view.engine.resolver')->register('php', function () {
            return new PhpEngine($this->app['files']);
        });

        $this->app->make('view.engine.resolver')->register('blade', function () {
            if (class_exists(CompilerEngineForIgnition::class)) {
                return new CompilerEngineForIgnition($this->app['blade.compiler']);
            }

            return new CompilerEngine($this->app['blade.compiler']);
        });

        return $this;
    }

    protected function registerHousekeepingRoutes()
    {
        if ($this->app->runningInConsole()) {
            return $this;
        }

        Route::group([
            'as' => 'ignition.',
            'prefix' => config('ignition.housekeeping_endpoint_prefix', '_ignition'),
            'middleware' => [IgnitionEnabled::class],
        ], function () {
            Route::get('health-check', HealthCheckController::class)->name('healthCheck');

            Route::post('execute-solution', ExecuteSolutionController::class)
                ->middleware(IgnitionConfigValueEnabled::class.':enableRunnableSolutions')
                ->name('executeSolution');

            Route::post('share-report', ShareReportController::class)
                ->middleware(IgnitionConfigValueEnabled::class.':enableShareButton')
                ->name('shareReport');

            Route::get('scripts/{script}', ScriptController::class)->name('scripts');
            Route::get('styles/{style}', StyleController::class)->name('styles');
        });

        return $this;
    }

    protected function registerSolutionProviderRepository()
    {
        $this->app->singleton(SolutionProviderRepositoryContract::class, function () {
            $defaultSolutions = $this->getDefaultSolutions();

            return new SolutionProviderRepository($defaultSolutions);
        });

        return $this;
    }

    protected function registerRenderer()
    {
        $this->app->bind(Renderer::class, function () {
            return new Renderer(__DIR__.'/../resources/views/');
        });

        return $this;
    }

    protected function registerExceptionRenderer()
    {
        if (interface_exists(\Whoops\Handler\HandlerInterface::class)) {
            $this->app->bind(\Whoops\Handler\HandlerInterface::class, function (Application $app) {
                return $app->make(IgnitionWhoopsHandler::class);
            });
        }

        if (interface_exists(\Illuminate\Contracts\Foundation\ExceptionRenderer::class)) {
            $this->app->bind(\Illuminate\Contracts\Foundation\ExceptionRenderer::class, function (Application $app) {
                return $app->make(IgnitionExceptionRenderer::class);
            });
        }

        return $this;
    }

    protected function registerIgnitionConfig()
    {
        $this->app->singleton(IgnitionConfig::class, function () {
            $options = [];

            try {
                if ($configPath = $this->getConfigFileLocation()) {
                    $options = require $configPath;
                }
            } catch (Throwable $e) {
                // possible open_basedir restriction
            }

            return new IgnitionConfig($options);
        });

        return $this;
    }

    protected function registerFlare()
    {
        $this->app->singleton('flare.http', function () {
            return new Client(
                config('flare.key'),
                config('flare.secret'),
                config('flare.base_url', 'https://reporting.flareapp.io/api')
            );
        });

        $this->app->singleton(SentReports::class);

        $this->app->alias('flare.http', Client::class);

        $this->app->singleton(Flare::class, function () {
            $client = new Flare($this->app->get('flare.http'), new LaravelContextDetector(), $this->app);
            $client->applicationPath(base_path());
            $client->stage(config('app.env'));

            return $client;
        });

        return $this;
    }

    protected function registerLogHandler()
    {
        $this->app->singleton('flare.logger', function ($app) {
            $handler = new FlareHandler(
                $app->make(Flare::class),
                $app->make(SentReports::class)
            );

            $logLevelString = config('logging.channels.flare.level', 'error');

            $logLevel = $this->getLogLevel($logLevelString);

            $handler->setMinimumReportLogLevel($logLevel);

            $logger = new Logger('Flare');
            $logger->pushHandler($handler);

            return $logger;
        });

        if ($this->app['log'] instanceof LogManager) {
            Log::extend('flare', function ($app) {
                return $app['flare.logger'];
            });
        } else {
            $this->bindLogListener();
        }

        return $this;
    }

    protected function getLogLevel(string $logLevelString): int
    {
        $logLevel = Logger::getLevels()[strtoupper($logLevelString)] ?? null;

        if (! $logLevel) {
            throw InvalidConfig::invalidLogLevel($logLevelString);
        }

        return $logLevel;
    }

    protected function registerLogRecorder(): self
    {
        $this->app->singleton(LogRecorder::class, function (Application $app): LogRecorder {
            return new LogRecorder(
                $app,
                $app->get('config')->get('flare.reporting.maximum_number_of_collected_logs')
            );
        });

        return $this;
    }

    protected function registerDumpCollector()
    {
        $dumpCollector = $this->app->make(DumpRecorder::class);

        $this->app->singleton(DumpRecorder::class);

        $this->app->instance(DumpRecorder::class, $dumpCollector);

        return $this;
    }

    protected function registerJobRecorder()
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }

        $this->app->singleton(JobRecorder::class);

        return $this;
    }

    protected function registerCommands()
    {
        $this->app->bind('command.flare:test', TestCommand::class);
        $this->app->bind('command.make:solution', SolutionMakeCommand::class);
        $this->app->bind('command.make:solution-provider', SolutionProviderMakeCommand::class);

        if ($this->app['config']->get('flare.key')) {
            $this->commands(['command.flare:test']);
        }

        if ($this->app['config']->get('ignition.register_commands', false)) {
            $this->commands(['command.make:solution']);
            $this->commands(['command.make:solution-provider']);
        }

        return $this;
    }

    protected function registerQueryRecorder(): self
    {
        $this->app->singleton(QueryRecorder::class, function (Application $app): QueryRecorder {
            return new QueryRecorder(
                $app,
                $app->get('config')->get('flare.reporting.report_query_bindings'),
                $app->get('config')->get('flare.reporting.maximum_number_of_collected_queries')
            );
        });

        return $this;
    }

    protected function registerBuiltInMiddleware()
    {
        $middlewares = [
            SetNotifierName::class,
            AddEnvironmentInformation::class,
            AddExceptionInformation::class,
        ];

        if (config('flare.reporting.report_logs', true)) {
            $middlewares[] = AddLogs::class;
        }

        $middlewares[] = AddDumps::class;

        if (config('flare.reporting.report_queries', true)) {
            $middlewares[] = AddQueries::class;
        }

        $middlewares[] = AddSolutions::class;

        if ($this->app->runningInConsole()) {
            $middlewares[] = AddJobInformation::class;
        }

        $middleware = collect($middlewares)
            ->map(function (string $middlewareClass) {
                return $this->app->make($middlewareClass);
            });

        if (config('flare.reporting.collect_git_information')) {
            $middleware[] = (new AddGitInformation());
        }

        foreach ($middleware as $singleMiddleware) {
            $this->app->get(Flare::class)->registerMiddleware($singleMiddleware);
        }

        return $this;
    }

    protected function getDefaultSolutions(): array
    {
        return [
            IncorrectValetDbCredentialsSolutionProvider::class,
            MissingAppKeySolutionProvider::class,
            DefaultDbNameSolutionProvider::class,
            BadMethodCallSolutionProvider::class,
            TableNotFoundSolutionProvider::class,
            MissingImportSolutionProvider::class,
            MissingPackageSolutionProvider::class,
            InvalidRouteActionSolutionProvider::class,
            ViewNotFoundSolutionProvider::class,
            UndefinedVariableSolutionProvider::class,
            MergeConflictSolutionProvider::class,
            RunningLaravelDuskInProductionProvider::class,
            MissingColumnSolutionProvider::class,
            UnknownValidationSolutionProvider::class,
            UndefinedLivewireMethodSolutionProvider::class,
            UndefinedLivewirePropertySolutionProvider::class,
            UndefinedPropertySolutionProvider::class,
            MissingMixManifestSolutionProvider::class,
            MissingLivewireComponentSolutionProvider::class,
            LazyLoadingViolationSolutionProvider::class,
        ];
    }

    protected function hasCustomViewEnginesRegistered()
    {
        $resolver = $this->app->make('view.engine.resolver');

        if (! $resolver->resolve('php') instanceof LaravelPhpEngine) {
            return false;
        }

        if (! $resolver->resolve('blade') instanceof LaravelCompilerEngine) {
            return false;
        }

        return true;
    }

    protected function bindLogListener()
    {
        $this->app['log']->listen(function (MessageLogged $messageLogged) {
            if (config('flare.key')) {
                try {
                    $this->app['flare.logger']->log(
                        $messageLogged->level,
                        $messageLogged->message,
                        $messageLogged->context
                    );
                } catch (Exception $exception) {
                    return;
                }
            }
        });
    }

    protected function getConfigFileLocation(): ?string
    {
        $configFullPath = base_path().DIRECTORY_SEPARATOR.'.ignition';

        if (file_exists($configFullPath)) {
            return $configFullPath;
        }

        $configFullPath = Arr::get($_SERVER, 'HOME', '').DIRECTORY_SEPARATOR.'.ignition';

        if (file_exists($configFullPath)) {
            return $configFullPath;
        }

        return null;
    }

    protected function resetFlare()
    {
        $this->app->get(SentReports::class)->clear();
        $this->app->get(Flare::class)->reset();

        if (config('flare.reporting.report_logs', true)) {
            $this->app->make(LogRecorder::class)->reset();
        }

        if (config('flare.reporting.report_queries', true)) {
            $this->app->make(QueryRecorder::class)->reset();
        }

        if ($this->app->runningInConsole()) {
            $this->app->make(JobRecorder::class)->reset();
        }

        $this->app->make(DumpRecorder::class)->reset();
    }

    protected function setupQueue(QueueManager $queue)
    {
        // Reset before executing a queue job to make sure the job's log/query/dump recorders are empty.
        // When using a sync queue this also reports the queued reports from previous exceptions.
        $queue->before(function () {
            $this->resetFlare();
        });

        // Send queued reports (and reset) after executing a queue job.
        $queue->after(function () {
            $this->resetFlare();
        });

        // Note: the $queue->looping() event can't be used because it's not triggered on Vapor
    }

    /** @psalm-suppress UndefinedClass */
    protected function setupOctane()
    {
        $this->app['events']->listen(RequestReceived::class, function () {
            $this->resetFlare();
        });

        $this->app['events']->listen(TaskReceived::class, function () {
            $this->resetFlare();
        });

        $this->app['events']->listen(TickReceived::class, function () {
            $this->resetFlare();
        });
    }
}
