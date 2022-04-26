<?php

namespace Facade\IgnitionContracts;

use Throwable;

interface HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool;

    /** \Facade\IgnitionContracts\Solution[] */
    public function getSolutions(Throwable $throwable): array;
}
