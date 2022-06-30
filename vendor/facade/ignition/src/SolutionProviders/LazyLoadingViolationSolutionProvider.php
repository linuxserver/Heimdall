<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Support\LaravelVersion;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Database\LazyLoadingViolationException;
use Throwable;

class LazyLoadingViolationSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if ($throwable instanceof LazyLoadingViolationException) {
            return true;
        }

        if (! $previous = $throwable->getPrevious()) {
            return false;
        }

        return $previous instanceof LazyLoadingViolationException;
    }

    public function getSolutions(Throwable $throwable): array
    {
        $majorVersion = LaravelVersion::major();

        return [BaseSolution::create(
            'Lazy loading was disabled to detect N+1 problems'
        )
            ->setSolutionDescription(
                'Either avoid lazy loading the relation or allow lazy loading.'
            )
            ->setDocumentationLinks([
                'Read the docs on preventing lazy loading' => "https://laravel.com/docs/{$majorVersion}.x/eloquent-relationships#preventing-lazy-loading",
                'Watch a video on how to deal with the N+1 problem' => 'https://www.youtube.com/watch?v=ZE7KBeraVpc',
            ]),];
    }
}
