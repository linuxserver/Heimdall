<?php

namespace Spatie\ErrorSolutions\Contracts;

use Throwable;

/**
 * Interface used for SolutionProviders.
 */
interface HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool;

    /** @return array<int, Solution> */
    public function getSolutions(Throwable $throwable): array;
}
