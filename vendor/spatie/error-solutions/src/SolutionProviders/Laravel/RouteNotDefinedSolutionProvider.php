<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Support\Facades\Route;
use Spatie\ErrorSolutions\Contracts\BaseSolution;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Support\Laravel\StringComparator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class RouteNotDefinedSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/Route \[(.*)\] not defined/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof RouteNotFoundException) {
            return false;
        }

        return (bool)preg_match(self::REGEX, $throwable->getMessage(), $matches);
    }

    public function getSolutions(Throwable $throwable): array
    {
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        $missingRoute = $matches[1] ?? '';

        $suggestedRoute = $this->findRelatedRoute($missingRoute);

        if ($suggestedRoute) {
            return [
                BaseSolution::create("{$missingRoute} was not defined.")
                    ->setSolutionDescription("Did you mean `{$suggestedRoute}`?"),
            ];
        }

        return [
            BaseSolution::create("{$missingRoute} was not defined.")
                ->setSolutionDescription('Are you sure that the route is defined'),
        ];
    }

    protected function findRelatedRoute(string $missingRoute): ?string
    {
        Route::getRoutes()->refreshNameLookups();

        return StringComparator::findClosestMatch(array_keys(Route::getRoutes()->getRoutesByName()), $missingRoute);
    }
}
