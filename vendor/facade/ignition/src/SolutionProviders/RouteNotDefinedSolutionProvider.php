<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\Support\StringComparator;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class RouteNotDefinedSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/Route \[(.*)\] not defined/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (version_compare(Application::VERSION, '6.0.0', '>=')) {
            if (! $throwable instanceof RouteNotFoundException) {
                return false;
            }
        }

        if (version_compare(Application::VERSION, '6.0.0', '<')) {
            if (! $throwable instanceof InvalidArgumentException && ! $throwable instanceof ViewException) {
                return false;
            }
        }

        return (bool)preg_match(self::REGEX, $throwable->getMessage(), $matches);
    }

    public function getSolutions(Throwable $throwable): array
    {
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        $missingRoute = $matches[1] ?? null;

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
