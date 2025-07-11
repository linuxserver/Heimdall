<?php

namespace Spatie\ErrorSolutions;

use Illuminate\Support\Collection;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Contracts\ProvidesSolution;
use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Contracts\SolutionProviderRepository as SolutionProviderRepositoryContract;
use Throwable;

class SolutionProviderRepository implements SolutionProviderRepositoryContract
{
    /** @var Collection<int, class-string<HasSolutionsForThrowable>|HasSolutionsForThrowable> */
    protected Collection $solutionProviders;

    /** @param array<int, class-string<HasSolutionsForThrowable>|HasSolutionsForThrowable> $solutionProviders */
    public function __construct(array $solutionProviders = [])
    {
        $this->solutionProviders = Collection::make($solutionProviders);
    }

    public function registerSolutionProvider(string|HasSolutionsForThrowable $solutionProvider): SolutionProviderRepositoryContract
    {
        $this->solutionProviders->push($solutionProvider);

        return $this;
    }

    public function registerSolutionProviders(array $solutionProviderClasses): SolutionProviderRepositoryContract
    {
        $this->solutionProviders = $this->solutionProviders->merge($solutionProviderClasses);

        return $this;
    }

    public function getSolutionsForThrowable(Throwable $throwable): array
    {
        $solutions = [];

        if ($throwable instanceof Solution) {
            $solutions[] = $throwable;
        }

        if ($throwable instanceof ProvidesSolution) {
            $solutions[] = $throwable->getSolution();
        }

        $providedSolutions = $this
            ->initialiseSolutionProviderRepositories()
            ->filter(function (HasSolutionsForThrowable $solutionProvider) use ($throwable) {
                try {
                    return $solutionProvider->canSolve($throwable);
                } catch (Throwable $exception) {
                    return false;
                }
            })
            ->map(function (HasSolutionsForThrowable $solutionProvider) use ($throwable) {
                try {
                    return $solutionProvider->getSolutions($throwable);
                } catch (Throwable $exception) {
                    return [];
                }
            })
            ->flatten()
            ->toArray();

        return array_merge($solutions, $providedSolutions);
    }

    public function getSolutionForClass(string $solutionClass): ?Solution
    {
        if (! class_exists($solutionClass)) {
            return null;
        }

        if (! in_array(Solution::class, class_implements($solutionClass) ?: [])) {
            return null;
        }

        if (! function_exists('app')) {
            return null;
        }

        return app($solutionClass);
    }

    /** @return Collection<int, HasSolutionsForThrowable> */
    protected function initialiseSolutionProviderRepositories(): Collection
    {
        return $this->solutionProviders
            ->filter(function (HasSolutionsForThrowable|string $provider) {
                if (! in_array(HasSolutionsForThrowable::class, class_implements($provider) ?: [])) {
                    return false;
                }

                if (function_exists('config') && in_array($provider, config('ErrorSolutions.ignored_solution_providers', []))) {
                    return false;
                }

                return true;
            })
            ->map(function (string|HasSolutionsForThrowable $provider): HasSolutionsForThrowable {
                if (is_string($provider)) {
                    return new $provider;
                }

                return $provider;
            });
    }
}
