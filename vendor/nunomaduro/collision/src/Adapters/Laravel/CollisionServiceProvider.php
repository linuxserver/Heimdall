<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand;
use NunoMaduro\Collision\Contracts\Provider as ProviderContract;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;

/**
 * @internal
 *
 * @final
 */
class CollisionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boots application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            $this->app->bind(ProviderContract::class, function () {
                // @phpstan-ignore-next-line
                if ($this->app->has(\Spatie\Ignition\Contracts\SolutionProviderRepository::class)) {
                    /** @var \Spatie\Ignition\Contracts\SolutionProviderRepository $solutionProviderRepository */
                    $solutionProviderRepository = $this->app->get(\Spatie\Ignition\Contracts\SolutionProviderRepository::class);

                    $solutionsRepository = new IgnitionSolutionsRepository($solutionProviderRepository);
                } else {
                    $solutionsRepository = new NullSolutionsRepository();
                }

                $writer = new Writer($solutionsRepository);
                $handler = new Handler($writer);

                return new Provider(null, $handler);
            });

            /** @var \Illuminate\Contracts\Debug\ExceptionHandler $appExceptionHandler */
            $appExceptionHandler = $this->app->make(ExceptionHandlerContract::class);

            $this->app->singleton(
                ExceptionHandlerContract::class,
                function ($app) use ($appExceptionHandler) {
                    return new ExceptionHandler($app, $appExceptionHandler);
                }
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [ProviderContract::class];
    }
}
