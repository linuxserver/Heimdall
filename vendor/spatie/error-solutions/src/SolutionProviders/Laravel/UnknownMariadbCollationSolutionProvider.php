<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Database\QueryException;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Solutions\Laravel\SuggestUsingMariadbDatabaseSolution;
use Throwable;

class UnknownMariadbCollationSolutionProvider implements HasSolutionsForThrowable
{
    const MYSQL_UNKNOWN_COLLATION_CODE = 1273;

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof QueryException) {
            return false;
        }

        if ($throwable->getCode() !== self::MYSQL_UNKNOWN_COLLATION_CODE) {
            return false;
        }

        return str_contains(
            $throwable->getMessage(),
            'Unknown collation: \'utf8mb4_uca1400_ai_ci\''
        );
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestUsingMariadbDatabaseSolution()];
    }
}
