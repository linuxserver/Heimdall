<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Database\QueryException;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Solutions\Laravel\RunMigrationsSolution;
use Throwable;

class TableNotFoundSolutionProvider implements HasSolutionsForThrowable
{
    /**
     * See https://dev.mysql.com/doc/refman/8.0/en/server-error-reference.html#error_er_bad_table_error.
     */
    const MYSQL_BAD_TABLE_CODE = '42S02';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof QueryException) {
            return false;
        }

        return  $this->isBadTableErrorCode($throwable->getCode());
    }

    protected function isBadTableErrorCode(string $code): bool
    {
        return $code === static::MYSQL_BAD_TABLE_CODE;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new RunMigrationsSolution('A table was not found')];
    }
}
